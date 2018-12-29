<?php

namespace ChirperBundle\Repository;

use Doctrine\DBAL\Connection;
use PDO;

/**
 * ChirpRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChirpRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllChirps() {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM ChirperBundle:Chirp c 
                        ORDER BY c.dateAdded DESC'
            );

        return $query->getResult();
    }

    public function getAllChirpsByUserId($userId) {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM ChirperBundle:Chirp c 
                        WHERE c.authorId = ?1 
                        ORDER BY c.dateAdded DESC'
            );

        $query->setParameter(1, $userId);
        return $query->getResult();
    }

    /**
     * @param $userId
     * @return integer
     */
    public function countAllUserChirps($userId) {
        $query = $this
            ->getEntityManager()
            ->createQuery(
                'SELECT COUNT(c.id) FROM ChirperBundle:Chirp c 
                        WHERE c.authorId = ?1'
            );

        $query->setParameter(1, $userId);
        $result = $query->getResult();
        return $result[0][1];
    }
}
