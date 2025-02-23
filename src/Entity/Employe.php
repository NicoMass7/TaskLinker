<?php

namespace App\Entity;

use App\Enum\EmployeStatut;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Employe implements PasswordAuthenticatedUserInterface, UserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Assert\NotBlank]
  private ?string $nom = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Assert\NotBlank]
  private ?string $prenom = null;


  #[ORM\Column(length: 255, unique: true)]
  #[Assert\NotBlank]
  #[Assert\Email]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   * La contrainte Regex valide que le mot de passe :
   * * contient au moins un chiffre
   * * contient au moins une lettre en minuscule
   * * contient au moins une lettre en majuscule
   * * contient au moins un caractère spécial qui n'est pas un espace
   * * fait entre 8 et 32 caractères de long
   */
  // #[Assert\NotCompromisedPassword()]
  // #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_STRONG)]
  // #[Assert\Regex('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,32}$/')]
  #[ORM\Column]
  private ?string $password = null;

  // #[ORM\Column(type: Types::STRING, length: 20, nullable: false)]
  // private ?EmployeStatut $statut = null;

  #[ORM\Column(type: Types::STRING, enumType: EmployeStatut::class)]
  private EmployeStatut $statut;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  #[Assert\NotBlank]
  private ?\DateTimeInterface $dateArrivee = null;

  #[ORM\ManyToMany(targetEntity: Projet::class, mappedBy: 'employes')]
  private Collection $projets;

  public function __construct()
  {
    $this->projets = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getNom(): ?string
  {
    return $this->nom;
  }

  public function setNom(?string $nom): static
  {
    $this->nom = $nom;

    return $this;
  }

  public function getPrenom(): ?string
  {
    return $this->prenom;
  }

  public function setPrenom(?string $prenom): static
  {
    $this->prenom = $prenom;

    return $this;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(?string $email): static
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;
    return $this;
  }

  public function getStatut(): ?EmployeStatut
  {
    return $this->statut;
  }

  public function setStatut(EmployeStatut|string|null $statut): static
  {
    if (is_string($statut)) {
      $statut = EmployeStatut::from($statut);
    }

    $this->statut = $statut;
    return $this;
  }

  /**
   * Méthode requise par UserInterface (inutile ici)
   */
  public function eraseCredentials()
  {
    // Cette méthode peut être vide, mais doit exister
  }

  public function getDateArrivee(): ?\DateTimeInterface
  {
    return $this->dateArrivee;
  }

  public function setDateArrivee(?\DateTimeInterface $dateArrivee): static
  {
    $this->dateArrivee = $dateArrivee;

    return $this;
  }

  /**
   * @return Collection<int, Projet>
   */
  public function getProjets(): Collection
  {
    return $this->projets;
  }

  public function addProjet(Projet $projet): static
  {
    if (!$this->projets->contains($projet)) {
      $this->projets->add($projet);
      $projet->addEmploye($this);
    }

    return $this;
  }

  public function removeProjet(Projet $projet): static
  {
    if ($this->projets->removeElement($projet)) {
      $projet->removeEmploye($this);
    }

    return $this;
  }

  public function isAdmin(): bool
  {
    return in_array('ROLE_ADMIN', $this->roles);
  }

  public function setAdmin(bool $admin): static
  {
    $this->roles = $admin ? ['ROLE_ADMIN'] : [];

    return $this;
  }
}
