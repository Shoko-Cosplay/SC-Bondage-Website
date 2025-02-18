<?php

namespace App\Http\Controller;

use App\Infrastructure\Pwa\PwaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PwaController extends AbstractController
{
    private PwaService $pwaService;

    public function __construct(PwaService $pwaService)
    {
        $this->pwaService = $pwaService;
    }

    #[Route('/manifest.json', name: 'app_pwa_manifest')]
    public function manifest(): JsonResponse
    {
        $manifest = $this->pwaService->generateManifest();
        return $this->json($manifest);
    }

    #[Route('/service-worker.js', name: 'app_pwa_service_worker')]
    public function serviceWorker(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/javascript');
        $response->setContent('
            self.addEventListener("install", (event) => {
                console.log("Service Worker installing.");
            });

            self.addEventListener("fetch", (event) => {
            });
        ');
        return $response;
    }


}
