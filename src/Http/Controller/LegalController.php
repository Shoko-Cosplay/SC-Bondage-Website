<?php

namespace App\Http\Controller;

use App\Http\DataModel\ContactDataModel;
use App\Http\Types\ContactFormType;
use App\Infrastructure\Legal\CreditServices;
use App\Infrastructure\Mailing\Mailer;
use App\Infrastructure\Spam\GeoIpService;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LegalController extends BaseController
{

    #[Route('/mentions-legal', name: 'app_legal_mention')]
    public function legalMentions(): Response
    {
        $this->params['og_title'] = 'Mentions légal';
        $this->params['og_description'] = 'Mentions légal';
        return $this->render('app/legal/mentions.twig');
    }

    #[Route('/donne-personnel', name: 'app_legal_rgpd')]
    public function rgpd(): Response
    {
        $this->params['og_title'] = 'Données Personnel';
        $this->params['og_description'] = 'Données Personnel';
        return $this->render('app/legal/rgpd.twig');
    }

    #[Route('/conditions-general-utilisation', name: 'app_legal_cgu')]
    public function cgu(): Response
    {
        $this->params['og_title'] = 'Conditions générale d\'utilsation';
        $this->params['og_description'] = 'Conditions générale d\'utilsation';
        return $this->render('app/legal/cgu.twig');
    }



    #[Route('/contact', name: 'app_legal_contact',options: ['sitemap' => ['priority' => 0.5, 'changefreq' => UrlConcrete::CHANGEFREQ_DAILY]])]
    public function contact(Request $request,HttpClientInterface $httpClient,Mailer $mailer,GeoIpService $geoIpService): Response
    {
        $this->params['og_title'] = 'Me contacter';
        $this->params['og_description'] = 'Me contacter';

        $contactModelData = new ContactDataModel();
        $formContact = $this->createForm(ContactFormType::class,$contactModelData);
        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid()){
            if(isset($_POST['contact_form']['captacha'])) {
                $result = $httpClient->request('POST',"https://api.hcaptcha.com/siteverify",[
                    'body' => [
                        'secret' => $_ENV['S_CAPTCHA'],
                        'response' =>  $_POST['contact_form']['captacha'],
                    ]
                ]);
                $response = json_decode( $result->getContent());
                if($response->success) {
                    $email = $mailer->createEmail('mails/contact.twig',[
                        'object' => $contactModelData,
                        'srcIp' => $geoIpService->getRealIp(),
                        'asn' => $geoIpService->asn(),
                        'country' => $geoIpService->country(),
                        'subject' => "[Shoko Cosplay] - Demmande de contact",
                    ]);
                    $email->subject("[Shoko Cosplay] - Demmande de contact");
                    $email->from('no-reply@shoko-cosplay.fr');
                    $email->to('contact@shoko-cosplay.fr');
                    $mailer->send($email);
                }
                $this->addFlash("contact_form","Votre message à été envoyée");
            }
            return $this->redirectToRoute('app_legal_contact');
        }
        return $this->render('app/legal/contact.twig',[
            'form' => $formContact->createView(),
        ]);
    }

    #[Route('/reglement',name: 'app_legal_rules')]
    public function legalRules()
    {
        $this->params['og_title'] = 'Réglement';
        $this->params['og_description'] = 'Réglement';

        $this->params['isNoIndex'] = true;
        return $this->render('app/legal/rule.twig',[
        ]);
    }

    #[Route('/credits', name: 'app_legal_credit')]
    public function legalCredit(CreditServices $creditServices): Response
    {
        $this->params['og_title'] = 'Liste des copyright - Crédit';
        $this->params['og_description'] = 'Liste des copyright - Crédit';



        $this->params['isNoIndex'] = true;
        return $this->render('app/legal/credit.twig',[
            'credits' => $creditServices->listCredit(),
            'libs' => $creditServices->listLibs(),
        ]);
    }
}
