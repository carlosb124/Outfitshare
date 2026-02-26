<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class SecurityController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): Response
    {
        

        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('google') 
            ->redirect(['email', 'profile'], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction()
    {
        
        
        

        
        
    }
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_feed');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
