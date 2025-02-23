<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Employe;
use App\Form\ProjetType;
use App\Repository\TacheRepository;
use App\Repository\ProjetRepository;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjetController extends AbstractController
{
  public function __construct(
    private ProjetRepository $projetRepository,
    private StatutRepository $statutRepository,
    private TacheRepository $tacheRepository,
    private EntityManagerInterface $entityManager,
  ) {}

  /**
   * Affiche la liste des tâches d'un projet.
   *
   * @param Projet $projet Le projet sélectionné
   * @param Security $security Service de gestion des utilisateurs et rôles
   * @return Response
   */
  #[Route('/projets/{id}', name: 'app_projet', requirements: ['id' => '\d+'])]
  public function projet(?int $id, Security $security): Response
  {

    $projet = $this->projetRepository->find($id);

    if (($id === null) || (!$projet)) {
      return $this->redirectToRoute('app_projets');
    }

    // Récupération de l'utilisateur connecté
    $employe = $security->getUser();

    // Vérification de l'accès avec le Voter
    $this->denyAccessUnlessGranted('view_projet', $projet);

    if ($this->isGranted('ROLE_ADMIN')) {
      $taches = $this->tacheRepository->findByProjet($projet);
    } else {
      $taches = $this->tacheRepository->findByEmployeProjet($employe, $projet);
    }

    $tasksByStatus = [];
    foreach ($taches as $tache) {
      $statutLibelle = $tache->getStatut()->getLibelle();
      $tasksByStatus[$statutLibelle][] = $tache;
    }

    return $this->render('projet/projet.html.twig', [
      'projet' => $projet,
      'tasksByStatus' => $tasksByStatus,
    ]);
  }

  #[Route('/', name: 'app_projets')]
  public function mesProjets(Security $security): Response
  {
    /** @var Employe $employe */
    $employe = $security->getUser();

    if (!$employe) {
      return $this->render('auth/welcome.html.twig');
    }

    if ($this->isGranted('ROLE_ADMIN')) {
      $projets = $this->projetRepository->findBy([
        'archive' => false,
      ]);
    } else {
      $projets = $this->projetRepository->findProjetsByEmploye($employe);
    }

    return $this->render('projet/liste.html.twig', [
      'projets' => $projets,
    ]);
  }

  #[Route('/projets/ajouter', name: 'app_projet_add')]
  public function ajouterProjet(Request $request): Response
  {
    $projet = new Projet();

    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $projet->setArchive(false);
      $this->entityManager->persist($projet);
      $this->entityManager->flush();
      return $this->redirectToRoute('app_projet', ['id' => $projet->getId()]);
    }
    return $this->render('projet/nouveau.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/projets/{id}/archiver', name: 'app_projet_archive')]
  public function archiverProjet(int $id): Response
  {
    $projet = $this->projetRepository->find($id);

    if (!$projet || $projet->isArchive()) {
      return $this->redirectToRoute('app_projets');
    }

    $projet->setArchive(true);
    $this->entityManager->flush();

    return $this->redirectToRoute('app_projets');
  }


  #[Route('/projets/{id}/editer', name: 'app_projet_edit', requirements: ['id' => '\d+'])]
  public function editerProjet(int $id, Request $request): Response
  {
    $projet = $this->projetRepository->find($id);
    if (!$projet || $projet->isArchive()) {
      return $this->redirectToRoute('app_projets');
    }

    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $projet->setArchive(false);
      $this->entityManager->flush();

      return $this->redirectToRoute('app_projet', ['id' => $projet->getId()]);
    }


    return $this->render('projet/editer.html.twig', [
      'projet' => $projet,
      'form' => $form->createView(),
    ]);
  }
}
