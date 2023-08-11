<?php

namespace App\Repository;

use App\DTO\NetworkDTO;
use App\Entity\Network;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
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

    public function findDistinctCountries(): array
    {
        $results = $this->createQueryBuilder('n')
            ->select('DISTINCT n.country')
            ->orderBy('n.country', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'country');
    }

    public function getNetworkId(string $country, string $city): array
    {
        $qb = $this->createQueryBuilder('n');
        $query = $qb->select('n.networkId')
            ->where('n.city = :city')
            ->andWhere('n.country = :country')
            ->setParameter('city', $city)
            ->setParameter('country', $country)
            ->getQuery();

        $result = $query->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(function ($item) {
            return $item['networkId'];
        }, $result);
    }
}
