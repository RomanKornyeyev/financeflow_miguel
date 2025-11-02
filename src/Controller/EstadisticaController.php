<?php

namespace App\Controller;

// ORM, ENTITY, FORM
use App\Entity\Movimiento;
use App\Form\MovimientoType;
use App\Form\MovimientoFilterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\MovimientoTipo;

// PAGINATION
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Knp\Component\Pager\PaginatorInterface;

// HTTPFOUNDATION
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// SERVICES
use App\Service\ExcelExportService;

#[Route('/estadistica')]
class EstadisticaController extends AbstractController
{
    #[Route('/', name: 'estadistica_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $movimientos = $entityManager->getRepository(Movimiento::class);
        $ingresos = $movimientos->findBy(['tipoTransaccion' => MovimientoTipo::INGRESO]);
        $gastos = $movimientos->findBy(['tipoTransaccion' => MovimientoTipo::GASTO]);
        $ingresosJson = [];
        foreach ($ingresos as $ingreso) {
            $ingresosJson[] = [
                'id' => $ingreso->getId(),
                'tipoTransaccion' => $ingreso->getTipoTransaccion()->value,
                'categoria' => $ingreso->getCategoria()->value,
                'importe' => $ingreso->getImporte(),
                'concepto' => $ingreso->getConcepto(),
                'descripcion' => $ingreso->getDescripcion(),
                'createdAt' => $ingreso->getCreatedAt()->format('Y-m-d H:i:s'),
                'fechaMovimiento' => $ingreso->getFechaMovimiento()->format('Y-m-d')
            ];
        }

        $gastosJson = [];
        foreach ($gastos as $gasto) {
            $gastosJson[] = [
                'id' => $gasto->getId(),
                'tipoTransaccion' => $gasto->getTipoTransaccion()->value,
                'categoria' => $gasto->getCategoria()->value,
                'importe' => $gasto->getImporte(),
                'concepto' => $gasto->getConcepto(),
                'descripcion' => $gasto->getDescripcion(),
                'createdAt' => $gasto->getCreatedAt()->format('Y-m-d H:i:s'),
                'fechaMovimiento' => $gasto->getFechaMovimiento()->format('Y-m-d')
            ];
        }

        return $this->render('estadistica/index.html.twig', [
            'ingresosPorCategoria' => $ingresos,
            'gastosPorCategoria' => $gastos,
            'ingresosJson' => $ingresosJson,
            'gastosJson' => $gastosJson,
        ]);
    }
}