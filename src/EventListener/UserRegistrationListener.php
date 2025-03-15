<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::postPersist, entity: User::class, method: 'sendConfirmationEmail')]
class UserRegistrationListener
{
    public function __construct(private EmailVerifier $emailVerifier, private MailerInterface $mailer) {}

    public function sendConfirmationEmail(User $user): void
    {
        // Vérifier si l'utilisateur est créé
        if ($user->isVerified() === false) {
            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@gmail.com', 'SpinToBet'))
                    ->to($user->getEmail())
                    ->subject('Merci de confirmer votre email')
                    ->htmlTemplate('emails/confirmation_email.html.twig');

                // Appel à la méthode d'envoi de confirmation
                // Envoie un lien de confirmation d'email
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);
            } catch (\Exception $e) {
                // Log de l'erreur ou autre gestion d'erreur
                error_log('Erreur d\'envoi d\'email: ' . $e->getMessage());
            }
        }
    }
}
