<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AuthCodeGenerator implements CodeGeneratorInterface
{
  private EntityManagerInterface $entityManager;
  private MailerInterface $mailer;

  public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
  {
    $this->entityManager = $entityManager;
    $this->mailer = $mailer;
  }

  public function generateAndSend(TwoFactorInterface  $user): void
  {
    // Génère un code aléatoire à 6 chiffres
    $code = random_int(100000, 999999);

    // Stocke le code dans l'utilisateur
    if (method_exists($user, 'setEmailAuthCode')) {
      $user->setEmailAuthCode($code);
      $this->entityManager->persist($user);
      $this->entityManager->flush();

      // Construire et envoyer l'email
      $email = (new Email())
        ->from('noreply@tasklinker.com')
        ->to($user->getEmailAuthRecipient())
        ->subject('Votre code de vérification')
        ->text("Votre code de vérification est : $code");

      $this->mailer->send($email);
    }
  }
}
