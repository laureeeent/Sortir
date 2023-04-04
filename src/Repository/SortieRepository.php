<?php

namespace App\Repository;

use App\Entity\RechercheSortie;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findSearch(RechercheSortie $rechercheSortie): array {

        $query = $this
            ->createQueryBuilder('s')
            ->innerJoin("s.etat", "etat")
            ;

        if ($rechercheSortie->getIsInscrit() === true && $rechercheSortie->getIsNonInscrit() === true) {
            $query = $query
                ->leftjoin("s.participants", "participants");
        }
        else if ($rechercheSortie->getIsInscrit() === true) {
            $query = $query
                ->leftJoin("s.participants", "participants")
                ->andWhere(" :parti = participants")
                ->setParameter("parti", $rechercheSortie->getParticipant());
        }
        else if ($rechercheSortie->getIsNonInscrit() === true) {
            $query = $query
                ->leftJoin("s.participants", "participants")
                ->andWhere(":parti_id != participants.id")
                ->setParameter("parti_id", $rechercheSortie->getParticipant()->getId());
        }
        else {
            $query = $query
                ->leftJoin("s.participants", "participants");
        }


        if (!empty($rechercheSortie->getCampus())) {
            $query = $query
                ->andWhere('s.campusOrganisateur = :c')
                ->setParameter('c', $rechercheSortie->getCampus());
        }

        if (!empty($rechercheSortie->getNomSortieContient())) {
            $query = $query
                ->andWhere("s.nom LIKE :nsc")
                ->setParameter("nsc", "%".$rechercheSortie->getNomSortieContient()."%");
        }

        if (!(empty($rechercheSortie->getDateAPartirDe())) && !(empty($rechercheSortie->getDateJusquA()))) {
            $query = $query
                ->andWhere("s.dateHeureDebut BETWEEN :dapd AND :djqa")
                ->setParameter("dapd", $rechercheSortie->getDateAPartirDe())
                ->setParameter("djqa", $rechercheSortie->getDateJusquA());
        }
        else if(!(empty($rechercheSortie->getDateAPartirDe()))) {
            $query = $query
                ->andWhere("s.dateHeureDebut > :dapd")
                ->setParameter("dapd", $rechercheSortie->getDateAPartirDe());
        }
        else if (!(empty($rechercheSortie->getDateJusquA()))) {
            $query = $query
                ->andWhere("s.dateHeureDebut < :djqa")
                ->setParameter("djqa", $rechercheSortie->getDateJusquA());
        }

        if ($rechercheSortie->getIsOrganisateur() === true) {
            $query = $query
                ->andWhere("s.organisateur = :user")
                ->setParameter("user", $rechercheSortie->getParticipant());
        }

        if ($rechercheSortie->getSortiePassee() === true) {
            $query = $query
                ->andWhere("etat.libelle = 'passée'");
        }

        $query = $query
            ->getQuery()
            ->getResult();

        return $query;
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
