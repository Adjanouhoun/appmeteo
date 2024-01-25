<?php

namespace App\Controller;

use App\Entity\Position;
use App\Form\PositionType;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/position")
 */
class PositionController extends AbstractController
{
    /**
     * @Route("/", name="app_position_index", methods={"GET"})
     */
    public function index(PositionRepository $positionRepository): Response
    {
        return $this->render('position/index.html.twig', [
            'positions' => $positionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_position_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PositionRepository $positionRepository): Response
    {
        $position = new Position();
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $positionRepository->add($position, true);

            return $this->redirectToRoute('app_position_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('position/new.html.twig', [
            'position' => $position,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_position_show", methods={"GET"})
     */
    public function show(Position $position): Response
    {
        return $this->render('position/show.html.twig', [
            'position' => $position,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_position_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Position $position, PositionRepository $positionRepository): Response
    {
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $positionRepository->add($position, true);

            return $this->redirectToRoute('app_position_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('position/edit.html.twig', [
            'position' => $position,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_position_delete", methods={"POST"})
     */
    public function delete(Request $request, Position $position, PositionRepository $positionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$position->getId(), $request->request->get('_token'))) {
            $positionRepository->remove($position, true);
        }

        return $this->redirectToRoute('app_position_index', [], Response::HTTP_SEE_OTHER);
    }
}
