<?php

namespace App\Form;

use App\Enum\MovimientoCategoria;
use App\Enum\MovimientoTipo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovimientoFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      $builder
        ->setMethod('GET')
        ->add('desde', DateType::class, [
          'widget' => 'single_text',
          'required' => false,
          'label' => 'Desde',
        ])
        ->add('hasta', DateType::class, [
          'widget' => 'single_text',
          'required' => false,
          'label' => 'Hasta',
        ])
        ->add('concepto', TextType::class, [
          'required' => false,
          'label' => 'Concepto',
        ])
        ->add('descripcion', TextType::class, [
          'required' => false,
          'label' => 'Descripción',
        ])
        ->add('tipoTransaccion', ChoiceType::class, [
          'choices' => MovimientoTipo::cases(),
          'choice_label' => fn(MovimientoTipo $tipo) => ucfirst($tipo->value),
          'choice_value' => fn(?MovimientoTipo $tipo) => $tipo?->value,
          'required' => false,
          'placeholder' => 'Todos',
          'label' => 'Tipo',
        ])
        ->add('categoria', ChoiceType::class, [
          'choices' => MovimientoCategoria::cases(),
          'choice_label' => fn(MovimientoCategoria $categoria) => ucfirst(str_replace('_', ' ', $categoria->value)),
          'choice_value' => fn(?MovimientoCategoria $categoria) => $categoria?->value,
          'required' => false,
          'placeholder' => 'Todas',
          'label' => 'Categoría',
        ])
        ->add('page', HiddenType::class, [
          'mapped' => false,
          'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
      $resolver->setDefaults([
        'csrf_protection' => false, // No necesitamos CSRF para filtros por GET
      ]);
    }
}
