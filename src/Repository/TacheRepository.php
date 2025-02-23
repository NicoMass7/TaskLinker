<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\Projet;
use App\Entity\Statut;
use App\Entity\Employe;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Tache>
 *
 * @method Tache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tache|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tache[]    findAll()
 * @method Tache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TacheRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Tache::class);
  }

  /**
   * @return Task by id employe and id projet
   */
  public function findByEmployeProjet(Employe $employe, Projet $projet): array
  {
    return $this->createQueryBuilder('t')
      ->where('t.employe = :eid')
      ->andWhere('t.projet = :pid')
      ->setParameter('eid', $employe)
      ->setParameter('pid', $projet)
      ->getQuery()
      ->getResult();
  }
}
