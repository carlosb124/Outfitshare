<?php

namespace App\Form;

use App\Entity\Outfit;
use App\Entity\Prenda;
use App\Validator\MinItems;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutfitType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título del Outfit',
                'attr' => ['placeholder' => 'Ej: Cena de verano, Boda de día...']
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['rows' => 3]
            ])
            ->add('prendas', EntityType::class, [
                'class' => Prenda::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('p')
                        ->where('p.user = :val')
                        ->setParameter('val', $user)
                        ->orderBy('p.categoria', 'ASC')
                        ->addOrderBy('p.nombre', 'ASC');
                },
                'choice_label' => function (Prenda $prenda) {
                    return sprintf('%s (%s)', $prenda->getNombre(), $prenda->getCategoria());
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Selecciona las prendas',
                'constraints' => [
                    new MinItems(['min' => 2]),
                ],
                'attr' => ['class' => 'outfit-prendas-grid'] // Clase para estilizar CSS
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outfit::class,
        ]);
    }
}
