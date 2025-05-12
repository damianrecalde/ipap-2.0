<?php

namespace App\Form;

use App\Entity\{User, City, WorkTeam};
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, PasswordType, FileType, TextType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{IsTrue, Length, NotBlank};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false; // Asegura que sabemos si estamos editando un usuario

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
                    'maxlength' => 8,             // opcional: limita cantidad de dígitos
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
            ->add('workTeams', EntityType::class, [
                'class' => WorkTeam::class,
                'choice_label' => 'name',
                'label' => 'Grupos de trabajo',
                'multiple' => true,       // permite seleccionar más de uno
                'expanded' => false,      // false = <select>, true = checkboxes
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administración' => 'ROLE_ADMINISTRACION',
                    'Campus' => 'ROLE_CAMPUS',
                    'Soporte' => 'ROLE_SOPORTE',
                    'Analista de datos' => 'ROLE_ANALISTA_DATOS',
                    'Admin' => 'ROLE_ADMIN',
                    'Usuario' => 'ROLE_USER',
                ],
                'expanded' => true,  // Mostrar como checkboxes
                'multiple' => true,  // Permite seleccionar múltiples roles
            ])
            ->add('googleAuthenticatorEnabled', CheckboxType::class, [
                'label' => 'Activar autenticación en dos pasos (2FA)',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [
                    new NotBlank([
                        'message' => 'Por favor ingresá una contraseña.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Repetir Contraseña',
                'mapped' => false,
                'required' => !$isEdit,
            ])
        ;

        if (!$isEdit) {
            // Validación para asegurar que las contraseñas coincidan
            $builder->get('confirmPassword')->addModelTransformer(new CallbackTransformer(
                function ($originalPassword) {
                    // Transformar el valor al pasar el formulario
                    return $originalPassword;
                },
                function ($submittedPassword) {
                    // Comparar las contraseñas
                    $plainPassword = $this->getData()->getPlainPassword();
                    if ($submittedPassword !== $plainPassword) {
                        // Si no coinciden, generar un error de validación
                        throw new TransformationFailedException('Las contraseñas no coinciden.');
                    }
                    return $submittedPassword;
                }
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,  // Por defecto no estamos editando un usuario
        ]);
    }
}
