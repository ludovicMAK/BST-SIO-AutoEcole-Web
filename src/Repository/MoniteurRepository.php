<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Moniteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @extends ServiceEntityRepository<Moniteur>
 *
 * @method Moniteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moniteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moniteur[]    findAll()
 * @method Moniteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoniteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moniteur::class);
    }

    public function save(Moniteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function siDejaLicence(int $idMoniteur ,int $idCateg){
        $siDejaLicence = false;
        $query = $this->createQueryBuilder("permis")
            ->select('l.id')
            ->from('App\Entity\Moniteur', 'm')
            ->join('m.licence', 'l')
            ->join('l.categorie', 'c')
            ->where('m.id = :idMoniteur')
            ->andWhere('c.id = :categ')
            ->setParameter('idMoniteur', $idMoniteur)
            ->setParameter('categ', $idCateg)
            ->getQuery();
        if($query->getResult()){
            $siDejaLicence = true;
        }
        return $siDejaLicence;
    }

    public function getMoniteur(int $idCateg)
    {
        $query = $this->createQueryBuilder('getPermis')
            ->select('DISTINCT m.id ,m.nom, m.prenom')
            ->from('App\Entity\Moniteur', 'm')
            ->join('m.licence', 'l')
            ->join('l.categorie', 'c');

        if ($idCateg) {
            $query->where('c.id = :categ')
                ->setParameter('categ', $idCateg);
        }
        return $query->getQuery()->getResult();
    }

    public function getSiMoniteurDispo(int $idMoniteur,string $date,string $horaire)
    {

        $query = $this->createQueryBuilder('siDispo')
            ->select('l.id')
            ->from('App\Entity\Moniteur', 'm')
            ->join('m.lecons', 'l')
            ->where('l.date = :date')
            ->andWhere('m.id = :idMoniteur')
            ->andWhere('l.heure = :horaire')
            ->setParameter('date', $date)
            ->setParameter('idMoniteur', $idMoniteur)
            ->setParameter('horaire', $horaire);
        return $query->getQuery()->getResult();
    }
    public function getMoniteurLikeNomOrPrenom(string $critere)
    {
        $query = $this->createQueryBuilder("getEleveLikeCritere")
            ->select('DISTINCT m.nom,m.id,m.prenom')
            ->from('App\Entity\Moniteur', 'm')
            ->where('m.nom like :critere')
            ->orWhere('m.prenom like :critere')
            ->setParameter('critere', '%'.$critere.'%')
            ->getQuery();

        return $query->getResult();
    }

    public function remove(Moniteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getMoniteurMostCalledByCategorie(string $categorie){
        if($categorie == "Toutes"){
            $rqt = $this->getEntityManager()->getConnection()->prepare("
            SELECT lesMax.maxNbLecons, lesNbLecons.prenom, lesNbLecons.nom
            FROM (
                SELECT MAX(lesNbLecons.nbLecons) AS maxNbLecons
            	FROM (
                	SELECT COUNT(lecon.id) AS nbLecons, moniteur.prenom, moniteur.nom, moniteur.id
            		FROM lecon
            		JOIN moniteur ON lecon.moniteur_id = moniteur.id
            		GROUP BY lecon.moniteur_id
                	) AS lesNbLecons
                ) AS lesMax
            JOIN (
                SELECT COUNT(lecon.id) AS nbLecons, moniteur.prenom, moniteur.nom, moniteur.id
            	FROM lecon
            	JOIN moniteur ON lecon.moniteur_id = moniteur.id
            	GROUP BY lecon.moniteur_id
                ) AS lesNbLecons ON lesNbLecons.nbLecons = lesMax.maxNbLecons
            ");
            return $rqt->executeQuery()->fetchAll();
        }
        else{
            $rqt = $this->getEntityManager()->getConnection()->prepare("
            SELECT lesMax.maxNbLecons, lesNbLecons.prenom, lesNbLecons.nom
            FROM (
                SELECT MAX(lesNbLecons.nbLecons) AS maxNbLecons
            	FROM (
                	SELECT COUNT(lecon.id) AS nbLecons, moniteur.prenom, moniteur.nom, moniteur.id
            		FROM lecon
            		JOIN moniteur ON lecon.moniteur_id = moniteur.id
                    JOIN vehicule ON lecon.vehicule_id = vehicule.id
            		JOIN categorie ON vehicule.categorie_id = categorie.id
                    WHERE categorie.libelle = :categorie
            		GROUP BY lecon.moniteur_id
                	) AS lesNbLecons
                ) AS lesMax
            JOIN (
                SELECT COUNT(lecon.id) AS nbLecons, moniteur.prenom, moniteur.nom, moniteur.id
            	FROM lecon
            	JOIN moniteur ON lecon.moniteur_id = moniteur.id
            	JOIN vehicule ON lecon.vehicule_id = vehicule.id
            	JOIN categorie ON vehicule.categorie_id = categorie.id
                WHERE categorie.libelle = :categorie
            	GROUP BY lecon.moniteur_id
                ) AS lesNbLecons ON lesNbLecons.nbLecons = lesMax.maxNbLecons
            ");
            $rqt->bindValue(":categorie", $categorie, PDO::PARAM_STR);
            return $rqt->executeQuery()->fetchAll();
        }
        return 0;
    }

//    /**
//     * @return Moniteur[] Returns an array of Moniteur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Moniteur
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
