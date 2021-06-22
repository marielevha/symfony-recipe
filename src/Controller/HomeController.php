<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RecetteRepository $recetteRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'popular_recipes' => $recetteRepository->popular_recipes(6),
            'latest_recipes' => $recetteRepository->latest_recipes(6)
        ]);
    }
}
