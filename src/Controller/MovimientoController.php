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


#[Route('/movimientos')]
class MovimientoController extends AbstractController
{
    #[Route('/', name: 'movimiento_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(MovimientoFilterType::class);
        $form->handleRequest($request);

        $filtros = $form->getData() ?? [];

        $repo = $entityManager->getRepository(Movimiento::class);

        // totales por categoría (ingresos) para chart
        $totalesIngresosPorCategoria = $repo->getTotalesPorCategoria(
            $this->getUser(),
            $filtros,
            MovimientoTipo::INGRESO
        );

        // totales por categoría (gastos) para chart
        $totalesGastosPorCategoria = $repo->getTotalesPorCategoria(
            $this->getUser(),
            $filtros,
            MovimientoTipo::GASTO
        );

        // totales (ingresos, gastos, balance) para tarjetas
        $totales = $entityManager->getRepository(Movimiento::class)
            ->getTotalesFiltrados($this->getUser(), $filtros);

        // ordenación tabla/lista
        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        // Crear el QueryBuilder filtrado
        $queryBuilder = $entityManager->getRepository(Movimiento::class)
            ->createFilteredQueryBuilder($this->getUser(), $filtros, $sort, $dir);
    
        // Paginación
        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $page,
            10,
            [
                'sortFieldParameterName' => null,
                'sortDirectionParameterName' => null,
            ]
        );
    
        return $this->render('movimiento/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
            'totales' => $totales,
            'ingresosPorCategoria' => $totalesIngresosPorCategoria,
            'gastosPorCategoria' => $totalesGastosPorCategoria,
        ]);
    }    

    #[Route('/editar/{id}', name: 'movimiento_editar')]
    public function edit(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {    
        if ($id) {
            $movimiento = $entityManager->getRepository(Movimiento::class)->find($id);
    
            if (!$movimiento || $movimiento->getUsuario() !== $this->getUser()) {
                throw $this->createNotFoundException('Movimiento no encontrado.');
            }
        } else {
            $movimiento = new Movimiento();
        }
    
        $form = $this->createForm(MovimientoType::class, $movimiento);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$id) {
                $movimiento->setUsuario($this->getUser());
            }

            $importe = abs($movimiento->getImporte());

            if ($movimiento->getTipoTransaccion() === MovimientoTipo::GASTO) {
                $movimiento->setImporte(-$importe);
            } else {
                $movimiento->setImporte($importe);
            }
    
            $entityManager->persist($movimiento);
            $entityManager->flush();
    
            $this->addFlash('success', $id ? 'Movimiento actualizado correctamente.' : 'Movimiento creado correctamente.');
    
            return $this->redirectToRoute('movimiento_index');
        }
    
        return $this->render('movimiento/edit.html.twig', [
            'form' => $form->createView(),
            'isEdit' => (bool) $id,
        ]);
    }

    #[Route('/eliminar/{id}', name: 'movimiento_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $movimiento = $entityManager->getRepository(Movimiento::class)->find($id);

        if (!$movimiento || $movimiento->getUsuario() !== $this->getUser()) {
            throw $this->createNotFoundException('Movimiento no encontrado.');
        }

        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('csrf_eliminar_movimiento' . $movimiento->getId(), $submittedToken)) {
            $entityManager->remove($movimiento);
            $entityManager->flush();

            $this->addFlash('success', 'Movimiento eliminado correctamente.');
        } else {
            $this->addFlash('danger', 'Token CSRF inválido. No se pudo eliminar el movimiento.');
        }

        return $this->redirectToRoute('movimiento_index');
    }

}