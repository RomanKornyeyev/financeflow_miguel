<?php

namespace App\Repository;

use App\Entity\Movimiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Usuario;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Movimiento>
 */
class MovimientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movimiento::class);
    }

    //    /**
    //     * @return Movimiento[] Returns an array of Movimiento objects
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

    //    public function findOneBySomeField($value): ?Movimiento
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function createFilteredQueryBuilder(Usuario $usuario, array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaMovimiento', 'DESC');

        if (!empty($filters['desde'])) {
            $qb->andWhere('m.fechaMovimiento >= :desde')
            ->setParameter('desde', $filters['desde']);
        }

        if (!empty($filters['hasta'])) {
            $qb->andWhere('m.fechaMovimiento <= :hasta')
            ->setParameter('hasta', $filters['hasta']);
        }

        if (!empty($filters['concepto'])) {
            $qb->andWhere('LOWER(m.concepto) LIKE :concepto')
            ->setParameter('concepto', '%' . strtolower($filters['concepto']) . '%');
        }

        if (!empty($filters['descripcion'])) {
            $qb->andWhere('LOWER(m.descripcion) LIKE :descripcion')
            ->setParameter('descripcion', '%' . strtolower($filters['descripcion']) . '%');
        }

        if (!empty($filters['tipoTransaccion'])) {
            $qb->andWhere('m.tipoTransaccion = :tipoTransaccion')
            ->setParameter('tipoTransaccion', $filters['tipoTransaccion']);
        }

        if (!empty($filters['categoria'])) {
            $qb->andWhere('m.categoria = :categoria')
            ->setParameter('categoria', $filters['categoria']);
        }

        return $qb;
    }

}
