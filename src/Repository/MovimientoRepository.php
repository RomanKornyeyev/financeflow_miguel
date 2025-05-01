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

    public function createFilteredQueryBuilder(Usuario $usuario, array $filters = [], ?string $sort = null, ?string $dir = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.usuario = :usuario')
            ->setParameter('usuario', $usuario);
    
        // Aplicar filtros como antes
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

        if (!empty($filters['importeMin'])) {
            $qb->andWhere('m.importe >= :importeMin')
               ->setParameter('importeMin', $filters['importeMin']);
        }
        
        if (!empty($filters['importeMax'])) {
            $qb->andWhere('m.importe <= :importeMax')
               ->setParameter('importeMax', $filters['importeMax']);
        }
    
        // 🧠 Ordenamiento dinámico
        $allowedSorts = ['fechaMovimiento', 'importe', 'concepto', 'categoria', 'tipoTransaccion'];
        $direction = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
    
        if (in_array($sort, $allowedSorts, true)) {
            $qb->orderBy('m.' . $sort, $direction);
        } else {
            $qb->orderBy('m.fechaMovimiento', 'DESC');
        }
    
        return $qb;
    }

}
