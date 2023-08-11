<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function save(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public  function getCategoriesByIdMoniteur(int $idMoniteur){
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.licences','l')
            ->innerJoin('l.moniteur','m')
            ->where('m.id=:val')
            ->setParameter('val', $idMoniteur)
            ->getQuery();
        return $query->getResult();
    }
    public  function getCategoriesByIdEleve(int $eleve){
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.vehicule','v')
            ->innerJoin('v.lecons','l')
            ->innerJoin('l.eleve','e')
            ->where('e.id=:val')
            ->setParameter('val', $eleve)
            ->getQuery();
        return $query->getResult();
    }

//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
