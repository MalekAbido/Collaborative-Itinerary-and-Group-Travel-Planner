<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\GroupFund;

class CommonPoolController extends Controller
{

    public function contribute($fundId)
    {
        $contributorId = $_POST['userId'] ?? 1; // Using dummy ID 1 for now
        $amount = floatval($_POST['amount'] ?? 0);
        $itineraryId = $_POST['itineraryId'] ?? 1; 

        if ($amount > 0) {
            $pool = new GroupFund();
            $pool->setFundId($fundId);
            $pool->addFunds($contributorId, $amount);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }

}