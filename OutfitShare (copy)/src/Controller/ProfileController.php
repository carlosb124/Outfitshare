<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/settings', name: 'app_profile_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, EntityManagerInterface $entityManager, \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle Profile Photo
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $profileFile */
            $profileFile = $form->get('profilePhoto')->getData();
            if ($profileFile) {
                $originalFilename = pathinfo($profileFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '.' . $profileFile->guessExtension();

                try {
                    $profileFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/images',
                        $newFilename
                    );
                    // Store relative path or full URL depending on how it's used. 
                    // Based on outfit usage, seems to simply store filename?
                    // But User entity has full URL placeholders in Twig...
                    // Let's store '/uploads/images/' . $newFilename so it works as a src directly.
                    $user->setProfilePhoto('/uploads/images/' . $newFilename);
                } catch (\Exception $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            // Handle Banner Photo
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $bannerFile */
            $bannerFile = $form->get('bannerPhoto')->getData();
            if ($bannerFile) {
                $newBannerFilename = uniqid() . '_banner.' . $bannerFile->guessExtension();
                try {
                    $bannerFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/images',
                        $newBannerFilename
                    );
                    $user->setBannerPhoto('/uploads/images/' . $newBannerFilename);
                } catch (\Exception $e) {
                    // ... handle exception
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');

            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('profile/settings.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}', name: 'app_profile_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // If viewing own profile via this route, redirect to main profile route (canonical)
        if ($this->getUser() === $user) {
            return $this->redirectToRoute('app_profile_index');
        }

        // Privacy Check: if profile is private, user must be valid?
        // For now, let's assume all profiles are public-ish or we check $user->isPublic()
        // If private and not following, maybe show limited view?
        // Implementation Plan says: "Privacy settings apply".

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
