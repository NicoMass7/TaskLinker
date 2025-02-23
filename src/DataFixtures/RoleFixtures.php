<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Employe;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RoleFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $repository = $manager->getRepository(Employe::class);
    $employes = $repository->findAll();

    foreach ($employes as $employe) {
      if ($employe->getEmail() === 'natalie@driblet.com') {
        $employe->setRoles(['ROLE_ADMIN']);
      } else {
        $employe->setRoles(['ROLE_USER']);
      }
      $manager->persist($employe);
    }

    $manager->flush();
  }

  //S'assure d'être exécutée après la fixture d'origine (ordre de load)
  public function getDependencies(): array
  {
    return [
      AppFixtures::class,
    ];
  }
}
