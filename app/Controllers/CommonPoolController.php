<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\GroupFund;
use App\Models\FundContribution;
use App\Models\TripMember;
use App\Enums\TripMemberRole;
use App\Helpers\Session;
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
            $pool->addFunds($member->getId(), $amount);
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
            Session::setFlash(Session::FLASH_ERROR, 'Contribution not found.');
            header("Location: /dashboard");
            exit;
        }

        $itineraryId = $contribution->getItineraryId();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'You are not a member of this trip.');
            header("Location: /dashboard");
            exit;
        }

        // Authorization: Creator OR Editor/Organizer
        $isCreator = ($contribution->getTripMemberId() == $member->getId());
        $isEditor  = Auth::hasRole(TripMemberRole::EDITOR->value, $member->getRole());

        if (!$isCreator && !$isEditor) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have permission to delete this contribution.');
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        if ($contribution->delete($member->getId())) {
            // Subtract from balance
            $db = \Core\Database::getInstance()->getConnection();
            $sql = "UPDATE GroupFund SET currentBalance = currentBalance - :amount WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':amount' => $contribution->getAmount(), ':id' => $contribution->getGroupFundId()]);
            
            Session::setFlash(Session::FLASH_SUCCESS, 'Contribution deleted successfully.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to delete contribution.');
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}
