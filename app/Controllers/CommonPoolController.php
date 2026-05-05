<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\GroupFund;
use Core\Controller;

class CommonPoolController extends Controller
{

    public function contribute($fundId)
    {
        $contributorId = Auth::id(); // Using dummy ID 1 for now
        $amount        = floatval($_POST['amount'] ?? 0);
        $itineraryId   = $_POST['itineraryId'] ?? 1;

        if ($amount > 0) {
            $pool = new GroupFund();
            $pool->setFundId($fundId);
            $pool->addFunds($contributorId, $amount);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}
