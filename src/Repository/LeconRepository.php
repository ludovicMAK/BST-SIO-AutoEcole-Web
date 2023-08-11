<?php

namespace App\Repository;

use App\Entity\Lecon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecon>
 *
 * @method Lecon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecon[]    findAll()
 * @method Lecon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecon::class);
    }

    public function save(Lecon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getNbLeconByIdMoniteur_IdCategorie(int $idMoniteur, String $dateDebut, String $dateFin,int $categ){

        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.id) ')
            ->join('l.moniteur', 'm')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c');
        $query->where($query->expr()->between('l.date', ':start', ':end'))
            ->andWhere('c.id = :categ')
            ->andWhere('m.id = :idMoniteur')
            ->setParameter('start', $dateDebut)
            ->setParameter('end', $dateFin)
            ->setParameter('idMoniteur', $idMoniteur)
            ->setParameter('categ', $categ);


        return $query->getQuery()->getResult();
    }

    public function remove(Lecon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getPourcentageLeconByCategorie(){
        return $this->getEntityManager()->getConnection()->prepare("
        SELECT ROUND(lesNbLecons.nbLecons / totalLecons.total * 100, 2) AS pourcentage, lesNbLecons.libelle
        FROM (
            SELECT COUNT(lecon.id) AS nbLecons, categorie.libelle
        	FROM lecon
        	JOIN vehicule ON lecon.vehicule_id = vehicule.id
        	JOIN categorie ON vehicule.categorie_id = categorie.id
        	GROUP BY categorie.id
            ) AS lesNbLecons, (
                SELECT COUNT(lecon.id) AS total
        		FROM lecon
                ) AS totalLecons
        ")->executeQuery()->fetchAll();
    }


//    /**
//     * @return Lecon[] Returns an array of Lecon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lecon
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
