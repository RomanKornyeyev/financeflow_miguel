<?php

namespace App\Form;

use App\Entity\Movimiento;
use App\Enum\MovimientoTipo;
use App\Enum\MovimientoCategoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovimientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tipoTransaccion', ChoiceType::class, [
                'choices' => MovimientoTipo::cases(),
                'choice_label' => fn(MovimientoTipo $tipo) => ucfirst($tipo->value),
                'choice_value' => fn(?MovimientoTipo $tipo) => $tipo?->value,
                'label' => 'Tipo de transacción',
            ])
            ->add('categoria', ChoiceType::class, [
                'choices' => MovimientoCategoria::cases(),
                'choice_label' => fn(MovimientoCategoria $categoria) => ucfirst(str_replace('_', ' ', $categoria->value)),
                'choice_value' => fn(?MovimientoCategoria $categoria) => $categoria?->value,
                'label' => 'Categoría',
            ])
            ->add('importe', MoneyType::class, [
                'currency' => 'EUR', // O ajusta según la moneda que uses
                'label' => 'Importe',
            ])
            ->add('concepto', TextType::class, [
                'label' => 'Concepto',
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'label' => 'Descripción',
                'attr' => ['rows' => 4],
            ])
            ->add('fechaMovimiento', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha del movimiento',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movimiento::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'csrf_movimiento', // ID único para proteger este formulario
        ]);
    }
}
