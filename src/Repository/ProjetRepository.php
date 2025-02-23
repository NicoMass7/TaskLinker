<?php

namespace App\Repository;

use App\Entity\Projet;
use App\Entity\Employe;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Projet>
 *
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Projet::class);
  }

  public function findProjetsByEmploye(Employe $employe): array
  {
    return $this->createQueryBuilder('p')
      ->join('p.employes', 'e')
      ->where('p.archive = false')
      ->andWhere('e.id = :employeId')
      ->setParameter('employeId', $employe->getId())
      ->getQuery()
      ->getResult();
  }
}
