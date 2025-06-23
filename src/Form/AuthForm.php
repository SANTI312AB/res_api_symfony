<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AuthForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(
                        message: 'El campo email no puede estar vacío.'
                    ),
                    new Length(
                        min: 5,
                        max: 180,
                        minMessage: 'El email debe tener al menos {{ limit }} caracteres.',
                        maxMessage: 'El email no puede tener más de {{ limit }} caracteres.'
                    )
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false, // 🔥 Esto evita que se aplique directamente al User
                'required' => false, // 🔥 Permite enviar el formulario sin contraseña
                'constraints' => [
                    new NotBlank(
                        message: 'El campo contraseña no puede estar vacío.'
                    ),
                    new Length(
                        min: 6,
                        max: 20,
                        minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres.',
                        maxMessage: 'La contraseña no puede tener más de {{ limit }} caracteres.'
                    ),
                    new Regex(
                        pattern: '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\|`~-]).{6,}$/',
                        message: 'La contraseña debe tener al menos 6 caracteres, incluyendo al menos una mayúscula, un número y un carácter especial.'
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => User::class
        ]);
    }
}
