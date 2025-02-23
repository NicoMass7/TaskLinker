<?php

namespace App\Security;

use App\Entity\Projet;
use App\Entity\Employe;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjetVoter extends Voter
{
  // Définition des permissions disponibles pour un projet
  public const VIEW = 'view_projet';
  public const EDIT = 'edit_projet';

  public function __construct(private Security $security) {}

  /**
   * Vérifie si le Voter doit s'appliquer pour cette permission et cet objet.
   *
   * @param string $attribute La permission demandée (ex: 'view_projet', 'edit_projet')
   * @param mixed $subject L'objet sur lequel porte la permission (ex: une instance de Projet)
   * @return bool Retourne true si ce Voter doit gérer cette permission
   */
  protected function supports(string $attribute, mixed $subject): bool
  {
    return in_array($attribute, [self::VIEW, self::EDIT]) && $subject instanceof Projet;
  }

  /**
   * Autorise ou non l'accès.
   *
   * @param string $attribute La permission demandée
   * @param mixed $projet L'objet Projet sur lequel porte la permission
   * @param TokenInterface $token Contient les informations de l'utilisateur connecté
   * @return bool Retourne true si l'accès est accordé
   */
  protected function voteOnAttribute(string $attribute, mixed $projet, TokenInterface $token): bool
  {
    $user = $token->getUser();

    if (!$user instanceof Employe) {
      return false; // Si l'utilisateur n'est pas un employé, accès refusé
    }

    /** @var Projet $projet */
    $projet = $projet;

    switch ($attribute) {
      case self::VIEW:
        // Un employé peut voir le projet s'il y participe
        return $projet->getEmployes()->contains($user) || $this->isAdmin($user);

      case self::EDIT:
        // Seuls les admins peuvent modifier le projet
        return $this->isAdmin($user);
    }

    return false;
  }

  private function isAdmin(Employe $user): bool
  {
    return in_array('ROLE_ADMIN', $user->getRoles());
  }
}
