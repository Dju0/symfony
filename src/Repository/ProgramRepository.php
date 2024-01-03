<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 *
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }


    public function findLikeNameOrActor(string $search)
    {
        
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->leftJoin('p.actors', 'a');
        $queryBuilder->where($queryBuilder->expr()->like('p.title', ':search'))
                    ->orWhere($queryBuilder->expr()->like('a.name', ':search'))
                    ->setParameter('search', '%' . $search . '%');
        $queryBuilder->orderBy('p.title', 'ASC');

        
        return $queryBuilder->getQuery()->getResult();
    }


}
