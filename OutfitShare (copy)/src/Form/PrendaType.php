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
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => [
                    'class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm',
                    'placeholder' => 'Ej: Camiseta blanca'
                ],
            ])
            ->add('marca', TextType::class, [
                'label' => 'Marca',
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'required' => false,
                'attr' => [
                    'class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm',
                    'placeholder' => 'Ej: Zara, H&M...'
                ],
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Categoría',
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => ['class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm'],
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
                'placeholder' => 'Selecciona una categoría',
                // HTML5 required attribute
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank([
                        'message' => 'Por favor selecciona una categoría',
                    ]),
                ],
            ])
            ->add('imagenFile', FileType::class, [
                'label' => 'Imagen',
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-neon-lime file:text-dark-gray hover:file:bg-lime-400'
                ],
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
