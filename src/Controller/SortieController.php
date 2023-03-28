<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/list', name: 'sortir_list')]
    public function list(
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortir/list.html.twig',
            compact("sorties")
        );
    }

    #[Route('/detail/{sortie}', name: 'sortir_detail')]
    public function detail(
        Sortie $sortie,
        SortieRepository $sortieRepository
    ): Response
    {

        return $this->render('sortir/detail.html.twig',
            compact("sortie")
        );
    }
}
