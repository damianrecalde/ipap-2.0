<?php

namespace App\Form;

use App\Entity\{User, City };
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, PasswordType, FileType, TextType, ChoiceType };
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{ IsTrue, Length, NotBlank };
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstname', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Apellido',
            ])
            ->add('dni', TextType::class, [
                'label' => 'DNI',
                'attr' => [
                    'inputmode' => 'numeric',     // teclado numérico en móviles
                    'pattern' => '\d*',           // solo números (sin puntos, guiones, etc.)
                    'maxlength' => 8,            // opcional: limita cantidad de dígitos
                    'placeholder' => 'Ej: 12345678'
                ]
            ])
            ->add('imageProfile', FileType::class, [
                'label' => 'Foto de perfil',
                'mapped' => false,
                'required' => false,
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name', // Asegúrate de que City tenga un método getName()
                'label' => 'Ciudad',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administración' => 'ROLE_ADMINISTRACION',
                    'Campus' => 'ROLE_CAMPUS',
                    'Soporte' => 'ROLE_SOPORTE',
                    'Analista de datos' => 'ROLE_ANALISTA_DATOS',
                ],
                'expanded' => true,  // Mostrar como checkboxes
                'multiple' => true,  // Permite seleccionar múltiples roles
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Repetir Contraseña',
                'mapped' => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
