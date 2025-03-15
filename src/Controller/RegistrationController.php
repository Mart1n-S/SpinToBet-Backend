<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private UserRepository $userRepository) {}

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        try {
            $user = $this->userRepository->find($request->query->get('id'));

            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            dd($exception);
            // ⚠️Définir plustard la route d'erreur du frontend⚠️
            return $this->redirect('');
        }
        dd('Email vérifié avec succès');
        // ⚠️Définir plustard la route de succès du frontend⚠️
        return $this->redirect('');
    }
}
