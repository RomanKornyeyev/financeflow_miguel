<?php

namespace App\Controller;

use App\Entity\Movimiento;
use App\Form\MovimientoType;
use App\Form\MovimientoFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movimientos')]
class MovimientoController extends AbstractController
{
    #[Route('/', name: 'movimiento_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MovimientoFilterType::class);
        $form->handleRequest($request);

        $filtros = $form->getData() ?? [];

        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        $queryBuilder = $entityManager->getRepository(Movimiento::class)
            ->createFilteredQueryBuilder($this->getUser(), $filtros, $sort, $dir);
    
        $adapter = new QueryAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);
    
        $pager->setMaxPerPage(2);
        $page = $request->query->getInt('page', 1);
    
        try {
            $pager->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException $e) {
            return $this->redirectToRoute('movimiento_index', ['page' => 1]);
        }
    
        return $this->render('movimiento/index.html.twig', [
            'pager' => $pager,
            'form' => $form->createView(),
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