<?php

namespace AppBundle\Repository;

/**
 * ImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImageRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCountImageFomSpot($id)
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('i.spot = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOlderPictureFromSpot($id)
    {
        return $this->createQueryBuilder('i')
            ->where('i.spot = :id')
            ->setParameter('id', $id)
            ->orderBy('i.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
