<?php

namespace App\Http\Controller;

use App\Database\Auth\Authenticator;
use App\Database\Auth\Event\UserBeforeCreatedEvent;
use App\Database\Auth\Event\UserCreatedEvent;
use App\Database\Auth\User;
use App\Database\Auth\UserRepository;
use App\Database\Password\Data\PasswordResetConfirmData;
use App\Database\Password\Data\PasswordResetRequestData;
use App\Database\Password\Entity\PasswordResetToken;
use App\Database\Password\Form\PasswordResetConfirmForm;
use App\Database\Password\Form\PasswordResetRequestForm;
use App\Database\Password\PasswordService;
use App\Database\Password\Repository\PasswordResetTokenRepository;
use App\Infrastructure\Security\TokenGeneratorService;
use App\Infrastructure\Social\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Http\Form\RegistrationFormType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityController extends BaseController
{

    #[Route('/validation-compte/{id}/{token}', name: 'app_register_confirm')]
    public function appRegisterConfirm(int $id,string $token,UserRepository $userRepository,EntityManagerInterface $entityManager){
        $user = $userRepository->findOneBy(['id'=>$id,'confirmationToken'=>$token]);
        if(!$user instanceof User) {
            $this->addFlash('error', "Ce token n'est pas valide");
            return $this->redirectToRoute('app_register');
        }

        if ($user->getCreatedAt() < new \DateTimeImmutable('-2 hours')) {
            $this->addFlash('error', 'Ce token a expiré');

            return $this->redirectToRoute('app_register');
        }
        $user->setConfirmationToken(null);
        $entityManager->flush();
        $this->addFlash('login_form', 'Votre compte a été validé.');

        return $this->redirectToRoute('app_root');


    }

    #[Route('/password/new', name: 'app_forgot')]
    public function passwordNew(HttpClientInterface $httpClient,Request $request, PasswordService $resetService)
    {
        $error = null;
        $data = new PasswordResetRequestData();
        $form = $this->createForm(PasswordResetRequestForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(isset($_POST['password_reset_request_form']['captacha'])) {
                $result = $httpClient->request('POST', "https://api.hcaptcha.com/siteverify", [
                    'body' => [
                        'secret' => $_ENV['S_CAPTCHA'],
                        'response' => $_POST['password_reset_request_form']['captacha'],
                    ]
                ]);
                $response = json_decode($result->getContent());
                if ($response->success) {
                    try {
                        $resetService->resetPassword($form->getData());
                        $this->addFlash('login_form', 'Les instructions pour réinitialiser votre mot de passe vous ont été envoyées');
                        return $this->redirectToRoute('app_root');
                    } catch (AuthenticationException $e) {
                        $error = $e;
                    }
                }
            }
        }

        return $this->render('app/password_reset.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);

    }
    #[Route('/password/confirm/{id}/{token}', name: 'app_forgot_confirm')]
    public function passwordConfirm(int $id,string $token,Request $request,PasswordService $passwordService,
    UserRepository $userRepository,PasswordResetTokenRepository $passwordResetTokenRepository)
    {
        $user = $userRepository->find($id);
        $token = $passwordResetTokenRepository->findOneBy(['token'=>$token]);
        if(!$user instanceof User) {
            $this->addFlash('login_form_error', 'Ce token a expiré');
            return $this->redirectToRoute('app_login');
        }
        if(!$token instanceof PasswordResetToken) {
            $this->addFlash('login_form_error', 'Ce token a expiré');
            return $this->redirectToRoute('app_login');
        }
        if($token->getUser() != $user) {
            $this->addFlash('login_form_error', 'Ce token a expiré');
            return $this->redirectToRoute('app_login');
        }

        if($passwordService->isExpired($token)){
            $this->addFlash('login_form_error', 'Ce token a expiré');
            return $this->redirectToRoute('app_root');
        }
        $error = null;
        $data = new PasswordResetConfirmData();
        $form = $this->createForm(PasswordResetConfirmForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordService->updatePassword($data->getPassword(), $token);
            $this->addFlash('login_form', 'Votre mot de passe a bien été réinitialisé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('app/password_reset_confirm.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/je-cree-mon-compte', name: 'app_register',options: ['sitemap' => ['priority' => 0.5, 'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY]])]
    public function appRegister(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        TokenGeneratorService $tokenGeneratorService,
        EventDispatcherInterface $dispatcher,
        SocialLoginService $socialLoginService,
        UserAuthenticatorInterface $authenticator,
        Authenticator $appAuthenticator
    )
    {

        $loggedInUser = $this->getUser();
        if ($loggedInUser) {
            return $this->redirectToRoute('app_root');
        }

        $user = new User();
        $rootErrors = [];

        $isOauthUser = $request->get('oauth') ? $socialLoginService->hydrate($request->getSession(), $user) : false;
        $form = $this->createForm(RegistrationFormType::class, $user, [

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(isset($_POST['registration_form']['captacha'])) {
                $result = $httpClient->request('POST', "https://api.hcaptcha.com/siteverify", [
                    'body' => [
                        'secret' => $_ENV['S_CAPTCHA'],
                        'response' => $_POST['registration_form']['captacha'],
                    ]
                ]);
                $response = json_decode($result->getContent());
                if ($response->success) {
                    $user
                        ->setPassword(
                            $form->has('plainPassword') ? $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            ) : ''
                        )
                        ->setLastLoginIp($request->getClientIp())
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setConfirmationToken($isOauthUser ? null : $tokenGeneratorService->generate(60))
                        ->setNotificationsReadAt(new \DateTimeImmutable())
                    ;
                    $dispatcher->dispatch(new UserBeforeCreatedEvent($user, $request));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $dispatcher->dispatch(new UserCreatedEvent($user, $isOauthUser));

                    if ($isOauthUser) {
                        $this->addFlash(
                            'register_form',
                            'Votre compte a été créé avec succès'
                        );

                        return $authenticator->authenticateUser($user, $appAuthenticator, $request) ?: $this->redirectToRoute('app_root');
                    }

                    $this->addFlash(
                        'register_form',
                        'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.'
                    );

                    return $this->redirectToRoute('app_root');
                }
            } else {
                $this->addFlash(
                    'register_form',
                    'Votre compte a été créé avec succès'
                );
                return $this->redirectToRoute('app_root');
            }

        } elseif ($form->isSubmitted()) {
            foreach ($form->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $rootErrors[] = $error;
                }
            }
        }
        return $this->render('app/register.twig',[
            'form' => $form->createView(),
            'errors' => $rootErrors,
            'menu' => 'register',
            'oauth_registration' => $request->get('oauth'),
            'oauth_type' => $socialLoginService->getOauthType($request->getSession()),
        ]);
    }
}
