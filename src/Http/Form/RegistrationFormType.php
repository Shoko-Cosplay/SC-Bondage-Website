<?php

namespace App\Http\Form;

use App\Database\Auth\User;
use App\Infrastructure\Captcha\CaptchaType;
use Omines\AntiSpamBundle\Form\Type\HoneypotType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
class RegistrationFormType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
        ;


        /** @var ?User $user */
        $user = $builder->getData();

        if ($user && empty($user->getEmail())) {
            $builder->add('email', EmailType::class, ['label' => 'Adresse email']);
        }

        if ($user && !$user->useOauth()) {
            $passwordAttrs = [
                'minlength' => 6,
            ];
            $builder
                ->add('plainPassword', RepeatedType::class, [
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 6,
                            'max' => 255,
                        ]),
                    ],
                    'first_options' => ['label' => 'Mot de passe', 'attr' => $passwordAttrs],
                    'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => $passwordAttrs],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'antispam_time' => true,
            'allow_extra_fields' => true,
            'antispam_honeypot' => true,
            'antispam_profile' => 'registration'
        ]);
    }
}
