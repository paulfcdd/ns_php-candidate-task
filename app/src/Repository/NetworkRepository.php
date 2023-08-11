<?php

namespace App\Repository;

use App\DTO\NetworkDTO;
use App\Entity\Network;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Network>
 *
 * @method Network|null find($id, $lockMode = null, $lockVersion = null)
 * @method Network|null findOneBy(array $criteria, array $orderBy = null)
 * @method Network[]    findAll()
 * @method Network[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Network::class);
    }

    public function isNetworkSynced(string $networkId): bool
    {
        $qb = $this->createQueryBuilder('n');

        $count = $qb->select('COUNT(n.networkId)')
            ->where('n.networkId = :networkId')
            ->setParameter('networkId', $networkId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }


}
