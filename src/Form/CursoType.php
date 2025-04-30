<?php

namespace App\Form;

use App\Entity\Curso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('prefijo')
            ->add('activo')
            ->add('modalidad_implementacion')
            ->add('fecha_inicio', null, [
                'widget' => 'single_text',
            ])
            ->add('fecha_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('cantidad_horas')
            ->add('eje')
            ->add('evaluacion')
            ->add('creditos')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curso::class,
        ]);
    }
}
