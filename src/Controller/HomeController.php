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
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(RecetteRepository $recetteRepository, CategorieRepository $categorieRepository): Response
    {
        //dd($recetteRepository->popular_recipes(6));
        return $this->render('home/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'popular_recipes_slider' => $recetteRepository->popular_recipes(3),
            'popular_recipes' => $recetteRepository->popular_recipes(8),
            'latest_recipes' => $recetteRepository->latest_recipes(8)
        ]);
    }
}
