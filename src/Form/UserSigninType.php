<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSigninType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class)
            ->add('password', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class , [
                'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne sont pas identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                ])
            ->add('role', 
                     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,
                    ["class"=>'App:Role',
                    'query_builder'=> function(\App\Repository\RoleRepository $er)
                                        {
                                            return $er->createQueryBuilder('r')->orderBy('r.value','ASC');
                                        },
                    "required"=>true,
                    ])
            ->add('Signin', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
