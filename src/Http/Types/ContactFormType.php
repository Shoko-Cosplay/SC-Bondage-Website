<?php

namespace App\Http\Types;

use App\Http\DataModel\ContactDataModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,[
            'label' => 'Prénom',
            'attr' => [
                'placeholder' => 'Prénom',
            ],
            'required' => true,
        ]);
        $builder->add('surname',TextType::class,[
            'label' => 'Nom',
            'attr' => [
                'placeholder' => 'Nom',
            ],
            'required' => true,
        ]);
        $builder->add('email',EmailType::class,[
            'label' => 'Email',
            'attr' => [
                'placeholder' => 'Email',
            ],
            'required' => true,
        ]);
        $builder->add('message',TextareaType::class,[
            'label' => 'Message',
            'attr' => [
                'placeholder' => 'Message',
            ],
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class',ContactDataModel::class);
        $resolver->setDefaults([
            'antispam_profile' => 'contact',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => '_contact',
            'allow_extra_fields' => true
        ]);

    }
}
