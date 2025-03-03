<?php

namespace App\Controller;

use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class EmployeController extends AbstractController
{
  public function __construct(
    private EmployeRepository $employeRepository,
    private EntityManagerInterface $entityManager,
  ) {}


  #[Route('/welcome', name: 'app_welcome')]
  public function bienvenue(): Response
  {
    return $this->render('auth/welcome.html.twig');
  }

  #[Route(path: '/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('auth/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
    ]);
  }

  #[Route(path: '/logout', name: 'app_logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  #[IsGranted('ROLE_ADMIN')]
  #[Route('/employes', name: 'app_employes')]
  public function employes(): Response
  {
    $employes = $this->employeRepository->findAll();

    return $this->render('employe/liste.html.twig', [
      'employes' => $employes,
    ]);
  }

  #[Route('/employes/{id}', name: 'app_employe')]
  public function employe($id): Response
  {
    $employe = $this->employeRepository->find($id);

    if (!$employe) {
      return $this->redirectToRoute('app_employes');
    }

    return $this->render('employe/employe.html.twig', [
      'employe' => $employe,
    ]);
  }

  #[Route('/employes/{id}/supprimer', name: 'app_employe_delete')]
  public function supprimerEmploye($id): Response
  {
    $employe = $this->employeRepository->find($id);

    if (!$employe) {
      return $this->redirectToRoute('app_employes');
    }

    $this->entityManager->remove($employe);
    $this->entityManager->flush();

    return $this->redirectToRoute('app_employes');
  }

  #[Route('/employes/{id}/editer', name: 'app_employe_edit')]
  public function editerEmploye($id, Request $request): Response
  {
    $employe = $this->employeRepository->find($id);

    if (!$employe) {
      return $this->redirectToRoute('app_employes');
    }

    $form = $this->createForm(EmployeType::class, $employe);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->flush();
      return $this->redirectToRoute('app_employes');
    }

    return $this->render('employe/employe.html.twig', [
      'employe' => $employe,
      'form' => $form->createView(),
    ]);
  }
}
