<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function save(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function  getAllVehiculeByIdCateg(int $idCateg){
        $query = $this->createQueryBuilder('siDispo')
            ->select('v.id,v.immatriculation,v.marque,v.modele')
            ->from("App\Entity\Vehicle","v")
            ->join('v.categorie', 'c')
            ->where('c.id = :idCateg')
            ->setParameter('idCateg', $idCateg);
        return $query->getQuery()->getResult();
    }
    public function getSiVoitureDispoByDate(string $date,int $idCateg)
    {
        $query = $this->createQueryBuilder('siDispo')
            ->select('v.id,v.immatriculation,v.marque,v.modele')
            ->from('App\Entity\Lecon', 'l')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c')
            ->where('c.id = :idCateg')
            ->andWhere("l.date != :date")
            ->setParameter('idCateg', $idCateg)
            ->setParameter('date', $date)
            ->groupBy('v.id');
        return $query->getQuery()->getResult();
    }
    public function getSiVoitureDispoByHorraire(string $horaire,int $idCateg,string $date)
    {
        $query = $this->createQueryBuilder('siDispo')
            ->select('v.id,v.immatriculation,v.marque,v.modele')
            ->from('App\Entity\Lecon', 'l')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c')
            ->where('c.id = :idCateg')
            ->andWhere("l.heure != :horaire")
            ->andWhere("l.date = :date")
            ->setParameter('idCateg', $idCateg)
            ->setParameter('date', $date)
            ->setParameter('horaire', $horaire)
            ->groupBy('v.id');
        return $query->getQuery()->getResult();
    }

    public function getVehiculesLesPlusUtilisesByCateg(string $categorie)
    {
        if($categorie == "Toutes"){
            return $this->getEntityManager()->getConnection()->prepare("
                SELECT lesMax.nbUtilMax, lesNbUtil.marque, lesNbUtil.modele, lesNbUtil.libelle
                FROM (
                    SELECT MAX(lesNbUtil.nbUtil) AS nbUtilMax
                      FROM (
                          SELECT COUNT(lecon.id) AS nbUtil
                          FROM lecon
                          GROUP BY lecon.vehicule_id
                      ) AS lesNbUtil
                ) AS lesMax
                JOIN (
                    SELECT COUNT(lecon.id) AS nbUtil, vehicule.marque, vehicule.modele, categorie.libelle
                    FROM lecon
                    JOIN vehicule ON lecon.vehicule_id = vehicule.id
                    JOIN categorie ON vehicule.categorie_id = categorie.id
                    GROUP BY lecon.vehicule_id
                ) AS lesNbUtil ON lesNbUtil.nbUtil = lesMax.nbUtilMax
            ")->executeQuery()->fetchAll();
        }
        else{
            $rqt = $this->getEntityManager()->getConnection()->prepare("
            SELECT lesMax.nbUtilMax, lesNbUtil.marque, lesNbUtil.modele, lesNbUtil.libelle
                FROM (
                    SELECT MAX(lesNbUtil.nbUtil) AS nbUtilMax
                      FROM (
                          SELECT COUNT(lecon.id) AS nbUtil, lecon.vehicule_id
                          FROM lecon
                          JOIN vehicule ON lecon.vehicule_id = vehicule.id
                          JOIN categorie ON vehicule.categorie_id = categorie.id
                          WHERE categorie.libelle = :categorie
                          GROUP BY lecon.vehicule_id
                      ) AS lesNbUtil
                ) AS lesMax
                JOIN (
                    SELECT COUNT(lecon.id) AS nbUtil, vehicule.marque, vehicule.modele, categorie.libelle
                    FROM lecon
                    JOIN vehicule ON lecon.vehicule_id = vehicule.id
                    JOIN categorie ON vehicule.categorie_id = categorie.id
                    WHERE categorie.libelle = :categorie
                    GROUP BY lecon.vehicule_id
                ) AS lesNbUtil ON lesNbUtil.nbUtil = lesMax.nbUtilMax
            ");
            $rqt->bindValue(":categorie", $categorie, PDO::PARAM_STR);
            return $rqt->executeQuery()->fetchAll();
        }
        return 0;
    }

//    /**
//     * @return Vehicule[] Returns an array of Vehicule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vehicule
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
