<?php

namespace App\Repository;

use App\Entity\Eleve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eleve>
 *
 * @method Eleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleve[]    findAll()
 * @method Eleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    public function save(Eleve $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Eleve $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getNbLeconByIdEleve_IdCategorie(int $idEleve,int $categ){

        $query = $this->createQueryBuilder("nbLecon")
            ->select('count(l.id)')
            ->from('App\Entity\Eleve', 'e')
            ->join('e.lecon', 'l')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c')
            ->where('e.id = :idEleve')
            ->andWhere('c.id = :categ')
            ->setParameter('idEleve', $idEleve)
            ->setParameter('categ', $categ)
            ->getQuery();
        return $query->getResult();
    }
    public function getNbLeconNotPayeeByIdEleve_IdCategorie(int $idEleve,int $categ){

        $query = $this->createQueryBuilder("nbLecon")
            ->select('count(l.id)')
            ->from('App\Entity\Eleve', 'e')
            ->join('e.lecon', 'l')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c')
            ->where('e.id = :idEleve')
            ->andWhere('c.id = :categ')
            ->andWhere('l.payee = 0')
            ->setParameter('idEleve', $idEleve)
            ->setParameter('categ', $categ)
            ->getQuery();
        return $query->getResult();
    }
    public function getMontantPermis(int $idEleve,int $categ){

        $query = $this->createQueryBuilder("nbLecon")
            ->select('count(l.id)')
            ->from('App\Entity\Eleve', 'e')
            ->join('e.lecon', 'l')
            ->join('l.vehicule', 'v')
            ->join('v.categorie', 'c')
            ->where('e.id = :idEleve')
            ->andWhere('c.id = :categ')
            ->andWhere('l.payee = 0')
            ->setParameter('idEleve', $idEleve)
            ->setParameter('categ', $categ)
            ->getQuery();
        return $query->getResult();
    }
    public function getEleveLikeNomOrPrenom(string $critere){
        $query = $this->createQueryBuilder("getEleveLikeCritere")
            ->select('DISTINCT e.nom,e.id,e.prenom')
            ->from('App\Entity\Eleve', 'e')
            ->where('e.nom like :critere')
            ->orWhere('e.prenom like :critere')
            ->setParameter('critere', '%'.$critere.'%')
            ->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return Eleve[] Returns an array of Eleve objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Eleve
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
