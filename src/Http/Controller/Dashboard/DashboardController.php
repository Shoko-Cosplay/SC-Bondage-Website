<?php

namespace App\Http\Controller\Dashboard;

use App\Http\Controller\BaseController;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends BaseController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard()
    {
        $loggedInUser = $this->getUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_root');
        }
    }
}
