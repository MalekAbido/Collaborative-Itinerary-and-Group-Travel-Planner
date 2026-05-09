<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\GroupFund;
use App\Models\FundContribution;
use App\Models\TripMember;
use App\Enums\TripMemberRole;
use App\Helpers\Session;
use App\Constants\Messages;
use Core\Controller;

class CommonPoolController extends Controller
{

    public function contribute($fundId)
    {
        $amount        = floatval($_POST['amount'] ?? 0);
        $itineraryId   = $_POST['itineraryId'] ?? 1;

        $member = Auth::requireMembership($itineraryId);

        if ($amount > 0) {
            $pool = new GroupFund();
            $pool->setFundId($fundId);
            if ($pool->addFunds($member->getId(), $amount)) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::CONTRIBUTION_ADDED);
            } else {
                Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            }
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }

    public function removeContribution($id)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $contribution = new FundContribution();
        if (!$contribution->read($id)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /dashboard");
            exit;
        }

        $itineraryId = $contribution->getItineraryId();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header("Location: /dashboard");
            exit;
        }

        // Authorization: Creator OR Editor/Organizer
        $isCreator = ($contribution->getTripMemberId() == $member->getId());
        $isEditor  = Auth::hasRole(TripMemberRole::EDITOR->value, $member->getRole());

        if (!$isCreator && !$isEditor) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        if ($contribution->delete($member->getId())) {
            // Subtract from balance
            $db = \Core\Database::getInstance()->getConnection();
            $sql = "UPDATE GroupFund SET currentBalance = currentBalance - :amount WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':amount' => $contribution->getAmount(), ':id' => $contribution->getGroupFundId()]);
            
            Session::setFlash(Session::FLASH_SUCCESS, Messages::CONTRIBUTION_DELETED);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}
