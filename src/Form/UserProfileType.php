<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\StylePreferenceEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Full Name',
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => ['class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm']
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Nickname (@handle)',
                'required' => false,
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => ['class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm']
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => ['class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm', 'rows' => 3]
            ])
            ->add('stylePreference', EnumType::class, [
                'class' => StylePreferenceEnum::class,
                'label' => 'Style Preference',
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-1'],
                'attr' => ['class' => 'block w-full rounded-xl border-gray-300 shadow-sm focus:border-neon-lime focus:ring-neon-lime sm:text-sm']
            ])
            ->add('isPublic', ChoiceType::class, [
                'label' => 'Profile Privacy',
                'choices' => [
                    'Public (Everyone can see)' => true,
                    'Private (Only followers)' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'block text-sm font-bold text-dark-gray mb-2'],
                'attr' => ['class' => 'space-y-2'],
                // Styling radio buttons customized in template or here if possible
            ])
            ->add('showLikesPublicly', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'label' => 'Make my "Likes" public',
                'label_attr' => ['class' => 'text-sm font-bold text-dark-gray ml-2'],
                'required' => false,
                'attr' => ['class' => 'rounded border-gray-300 text-neon-lime focus:ring-neon-lime'],
            ])
            ->add('showSavedPublicly', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'label' => 'Make my "Saved Outfits" public',
                'label_attr' => ['class' => 'text-sm font-bold text-dark-gray ml-2'],
                'required' => false,
                'attr' => ['class' => 'rounded border-gray-300 text-neon-lime focus:ring-neon-lime'],
            ])
            ->add('profilePhoto', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Profile Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                    ])
                ],
                'attr' => ['class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-dark-gray hover:file:bg-gray-200'],
            ])
            ->add('bannerPhoto', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Banner Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                    ])
                ],
                'attr' => ['class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-dark-gray hover:file:bg-gray-200'],
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
