<?php

namespace App\Http\Controller\Dashboard;

use App\Http\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends DashboardBaseController
{
    #[Route('/dashboard/choice', name: 'app_choice')]
    public function choice(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $loggedInUser = $this->getUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_root');
        }

        $typeAccount = ['master', 'slave', 'self-bondage'];
        if ($this->isGranted('ROLE_ADMIN')) {
            $typeAccount[] = 'admin';
        }

        if ($request->query->has('type')) {
            $request->getSession()->set('type_account', $request->get('type'));

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('application/choice.twig', [
            'typeAccount' => $typeAccount,
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $loggedInUser = $this->getUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_root');
        }

        $session = $request->getSession()->get('type_account', null);
        if (is_null($session)) {
            return $this->redirectToRoute('app_choice');
        }

        return $this->render('application/dashboard.twig', []);
    }
}
