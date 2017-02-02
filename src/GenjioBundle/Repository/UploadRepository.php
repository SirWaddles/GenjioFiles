<?php

namespace GenjioBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UploadRepository extends EntityRepository
{
    public function getAllUploads($user)
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->orderBy('u.id', 'DESC')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}
