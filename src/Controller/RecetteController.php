<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Media;
use App\Entity\Recette;
use App\Form\CreeRecetteType;
use App\Form\EditRecetteType;
use App\Repository\CategorieRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/recette")
 */
class RecetteController extends AbstractController
{
    private $ingredientRepository;

    public function __construct()
    {
        //$this->entityManager = $this->getDoctrine()->getManager();
        $this->ingredientRepository = IngredientRepository::class;
    }

    /**
     * @Route("/", name="recette_index")
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(RecetteRepository $recetteRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'popular_recipes' => $recetteRepository->popular_recipes(6),
            'latest_recipes' => $recetteRepository->latest_recipes(6)
        ]);
    }

    /**
     * @Route("/create", name="recette_create")
     * @param Request $request
     * @param CategorieRepository $categorieRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function create(Request $request, CategorieRepository $categorieRepository, UserRepository $userRepository): Response
    {
        $recette = new Recette();
        $form = $this->createForm(CreeRecetteType::class, $recette);
        $form->handleRequest($request);
        /*$categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);*/

        if ($form->isSubmitted()) {
            //dd([$request->files->get('medias'), $request->files->get('image')]);
            //$image = $request->files->get('$image');
            $image = $request->files->get('image');
            if ($image) {
                $filename = uniqid() . "." . $image->guessExtension();
                $image->move($this->getParameter('upload_directory'), $filename);
                $recette->setImage($filename);
            }
            $categorie = $categorieRepository->findOneBy([
                'id' => $request->get('categorie'),
            ]);
            $recette->setCategorie($categorie);
            $user = $userRepository->findOneBy(['id' => 6]);
            $recette->setAuteur($user);
            $recette->setDifficulte($request->get('difficulte'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            #Add new ingredients
            $ingredients = $request->get('ingredients');
            if (!empty($ingredients)) {
                foreach ($ingredients as $designation) {
                    $ingredient = new Ingredient();
                    $ingredient->setRecette($recette);
                    $ingredient->setDesignation($designation);
                    $entityManager->persist($ingredient);
                    $entityManager->flush();
                }
            }

            $medias = $request->files->get('medias');
            if (!empty($medias)) {
                $this->upload_multiple_media($medias, $recette, $entityManager);
            }

            //dd([$recette, $request->get('categorie'), $image, $ingredients, empty($ingredients)]);

            return $this->redirectToRoute('home');
        }
        return $this->render('recette/create.html.twig', [
            'recette' => $recette,
            'categories' => $categorieRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="recette_edit")
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function edit(
        Request $request,
        RecetteRepository $recetteRepository,
        CategorieRepository $categorieRepository
    ): Response
    {
        //$recette = new Recette();
        $id = $request->attributes->getInt('id');
        $recette = $recetteRepository->find($id);

        $form = $this->createForm(EditRecetteType::class, $recette);
        $form->handleRequest($request);
        /*$categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);*/

        //dd($form->isSubmitted());

        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            //dd($fiche = $form->get('new_image')->getData());
            //dd($request->files->get('new_image'));

            #Clear last ingredients & add new ingredients
            $ingredients_req = $request->get('ingredients');
            if (isset($ingredients_req)) {
                foreach ($recette->getIngredients() as $ingredient) {
                    $entityManager->remove($ingredient);
                    $entityManager->flush();
                }
                if ($recette->getIngredients()->isEmpty()) {
                    foreach ($ingredients_req as $designation) {
                        $ingredient = new Ingredient();
                        $ingredient->setDesignation($designation);
                        $ingredient->setRecette($recette);
                        $recette->addIngredient($ingredient);

                        #Save to db
                        $entityManager->persist($ingredient);
                        $entityManager->flush();
                    }
                }
            }

            #Upload & add new overview image
            $image = $form->get('new_image')->getData();
            if ($image) {
                $filename = uniqid() . "." . $image->guessExtension();
                $image->move($this->getParameter('upload_directory'), $filename);
                $recette->setImage($filename);
            }

            #Get & upload others media
            $medias = $request->files->get('medias');
            $last_medias = $recette->getMedia()->getValues();
            foreach ($recette->getMedia() as $media) {
                $entityManager->remove($media);
                $entityManager->flush();
            }

            if (!empty($medias)) {
                $img_count = 5 - count($medias);
                if ($img_count > 0) {
                    for ($i = 0; $i < $img_count; $i++) {
                        $entityManager->persist($last_medias[$i]);
                        $entityManager->flush();
                    }
                }

                #Save to db
                $entityManager->persist($recette);

                //cascade={"persist"}

                $this->upload_multiple_media($medias, $recette, $entityManager);
            }





            $entityManager->flush();
            /*dd([
                //$ingredients_req,
                //$request->files->get('medias'),
                //$medias,
                $recette->getMedia()->getValues(),
                $recette->getIngredients()->getValues(),
            ]);*/
            //$image = $request->files->get('$image');
            /*$image = $request->files->get('image');
            if ($image) {
                $filename = uniqid() . "." . $image->guessExtension();
                $image->move($this->getParameter('upload_directory'), $filename);
                $recette->setImage($filename);
            }
            $categorie = $categorieRepository->findOneBy([
                'id' => $request->get('categorie'),
            ]);
            $recette->setCategorie($categorie);
            $user = $userRepository->findOneBy(['id' => 6]);
            $recette->setAuteur($user);
            $recette->setDifficulte($request->get('difficulte'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            $ingredients = $request->get('ingredients');
            if (!empty($ingredients)) {
                foreach ($ingredients as $designation) {
                    $ingredient = new Ingredient();
                    $ingredient->setRecette($recette);
                    $ingredient->setDesignation($designation);
                    $entityManager->persist($ingredient);
                    $entityManager->flush();
                }
            }

            $medias = $request->files->get('medias');
            if (!empty($medias)) {
                $this->upload_multiple_media($medias, $recette, $entityManager);
            }*/

            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }
        $categories = $this->array_remove_object($categorieRepository->findAll(), $recette->getCategorie()->getId());
        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recette_show", methods={"GET"})
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @return Response
     */
    public function show(Request $request, RecetteRepository $recetteRepository)
    {
        $id = $request->attributes->getInt('id');
        $recette = $recetteRepository->find($id);
        $comments = $recette->getComments();
        foreach ($comments as $comment) {
            $comment->setTimeAgo($this->time_ago(date_format($comment->getPublishAt(), 'Y-m-d H:i:s')));
        }
        //dump($comments->first());
        //$time = $comments->first()->getPublishAt();
        //dump($this->time_ago(date_format($time, 'Y-m-d H:i:s')));
        //die();
        $comments = $comments->getValues();
        usort($comments, function($a, $b) {
            return $b->getPublishAt()->format('U') - $a->getPublishAt()->format('U');
        });
        //dump($comments);
        //dump(array_reverse($comments));
        //die();

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'comments' => $comments,
            'comments_count' => count($comments),
        ]);
    }

    /**
     * @Route("/popular_recipes", name="popular_recipes", methods={"GET"})
     * @param RecetteRepository $recetteRepository
     */
    public function popular_recipes(RecetteRepository $recetteRepository)
    {
        $recipes = $recetteRepository->popular_recipes(6);
        /*foreach ($recipes as $recipe) {
            $recipe->rating = $this->avg_rating($recipe->getNotes());
        }
        usort($recipes, function($a, $b)
        {
            return strcmp($b->rating, $a->rating);
        });*/
        dd($recipes);
    }

    /**
     * @Route("/latest_recipes/{id}", name="latest_recipes", methods={"GET"})
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function latest_recipes(Request $request, RecetteRepository $recetteRepository, SerializerInterface $serializer)
    {
        $category = $request->attributes->getInt('id');
        //dd($recetteRepository->findAllWithPaginate(1));
        $recipes = $recetteRepository->latest_recipes(5, $category);
        /*foreach ($recipes as $recipe) {
            $recipe->rating = $this->avg_rating($recipe->getNotes());
            //dump($recipe->getNotes());
            //dump($this->avg_rating($recipe->getNotes()));
        }*/

        //$json = $serializer->serialize($recipes, 'json', ['groups' => ['user', 'categorie']]);
        //dd($recipes);
        return $this->json([
            'code' => 200,
            'data' => $recipes
        ]);
        dd($recipes);
    }

    /**
     * @Route("/{id}/delete", name="recette_delete", methods={"POST"})
     * @param Request $request
     * @param Recette $recette
     * @return RedirectResponse
     */
    public function delete(Request $request, Recette $recette)
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }

    private function upload_multiple_media($files, $recette, $entityManager)
    {
        foreach ($files as $file) {
            $media = new Media();
            $filename = uniqid() . '@' . time() . "." . $file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $filename);

            $media->setPath($filename);
            $media->setRecette($recette);
            $entityManager->persist($media);
            //$this->entityManager->flush();
            //$recette->setImage($filename);
        }
        $entityManager->flush();
    }

    private function array_remove_object($array, $value)
    {
        return array_filter($array, function($a) use($value) {
            //dump($a);
            //die();
            return $a->getId() !== $value;
        });
    }

    private function avg_rating($notes)
    {
        $rating = 0;
        foreach ($notes as $note) {
            $rating += $note->getValeur();
        }
        return intval(ceil($rating / $notes->count()));
    }

    private function time_ago($date)
    {
        $timestamp = strtotime($date);

        $strTime = array("second", "minute", "hour", "day", "month", "year");
        $length = array("60","60","24","30","12","10");

        $currentTime = time();
        if($currentTime >= $timestamp) {
            $diff     = time()- $timestamp;
            for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);
            return $diff . " " . $strTime[$i] . "(s) ago ";
        }
    }

}
