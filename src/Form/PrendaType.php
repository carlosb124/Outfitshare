<?php

namespace App\Form;

use App\Entity\Prenda;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PrendaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la prenda',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ej: Camiseta blanca'],
            ])
            ->add('marca', TextType::class, [
                'label' => 'Marca',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ej: Zara, H&M...'],
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Categoría',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Camisetas' => 'camisetas',
                    'Pantalones' => 'pantalones',
                    'Vestidos' => 'vestidos',
                    'Faldas' => 'faldas',
                    'Chaquetas' => 'chaquetas',
                    'Zapatos' => 'zapatos',
                    'Accesorios' => 'accesorios',
                    'Otros' => 'otros',
                ],
            ])
            ->add('imagenFile', FileType::class, [
                'label' => 'Imagen',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Por favor sube una imagen válida (JPG, PNG o WebP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prenda::class,
        ]);
    }
}
