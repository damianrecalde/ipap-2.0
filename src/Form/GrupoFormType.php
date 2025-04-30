<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Curso;
use App\Entity\Grupo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha_inicio', null, [
                'widget' => 'single_text',
            ])
            ->add('fecha_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('fecha_cierre', null, [
                'widget' => 'single_text',
            ])
            ->add('formato')
            ->add('cupo_actual')
            ->add('cupo_maximo')
            ->add('cantidad_encuentros')
            ->add('localidad', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grupo::class,
        ]);
    }
}
