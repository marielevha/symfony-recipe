<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Data\Service;
use App\Entity\Ingredient;
use App\Entity\Media;
use App\Entity\Note;
use App\Entity\Recette;
use App\Form\CreeRecetteType;
use App\Form\EditRecetteType;
use App\Form\SearchForm;
use App\Repository\CategorieRepository;
use App\Repository\IngredientRepository;
use App\Repository\NoteRepository;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecetteController extends AbstractController
{
    use Service;

    private $ingredientRepository;

    public function __construct()
    {
        $this->ingredientRepository = IngredientRepository::class;
    }

    /**
     * @Route("/recette/", name="recette_index")
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(Request $request, RecetteRepository $recetteRepository, CategorieRepository $categorieRepository): Response
    {
        $data = new SearchData();
        $categories = $categorieRepository->findAll();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data->categorie = $categorieRepository->find($request->get('search_categorie'));
        }
        $recettes = $recetteRepository->findSearch($data);

        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recette/create", name="recette_create")
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
            $recette->setSlug($this->slug_generate($recette->getNom()));

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
     * @Route("/recette/{id}/edit", name="recette_edit")
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function edit(Request $request, RecetteRepository $recetteRepository, CategorieRepository $categorieRepository): Response
    {
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
                $recette->setSlug($this->slug_generate($recette->getNom()));
                $entityManager->persist($recette);
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
     * @Route("/recette/{slug}", name="recette_show", methods={"GET"})
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @return Response
     */
    public function show(Request $request, RecetteRepository $recetteRepository)
    {
        $slug = $request->attributes->get('slug');
        $recette = $recetteRepository->find_by_slug($slug);
        //dd($recette);

        //$id = $request->attributes->getInt('id');
        //$recette = $recetteRepository->find($id);
        $comments = $recette->getComments();
        foreach ($comments as $comment) {
            $comment->setTimeAgo($this->time_ago(date_format($comment->getPublishAt(), 'Y-m-d H:i:s')));
        }
        $comments = $comments->getValues();
        usort($comments, function($a, $b) {
            return $b->getPublishAt()->format('U') - $a->getPublishAt()->format('U');
        });
        $recette->rating = $this->avg_rating($recette->getNotes());

        //dd($recette->getAuteur()->);

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'comments' => $comments,
            'comments_count' => count($comments),
        ]);
    }

    /**
     * @Route("/recette/popular_recipes", name="popular_recipes", methods={"GET"})
     * @param RecetteRepository $recetteRepository
     */
    public function popular_recipes(RecetteRepository $recetteRepository)
    {
        $recipes = $recetteRepository->popular_recipes(6);
        dd($recipes);
    }

    /**
     * @Route("/recette/latest_recipes/{id}", name="latest_recipes", methods={"GET"})
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function latest_recipes(Request $request, RecetteRepository $recetteRepository, SerializerInterface $serializer)
    {
        $category = $request->attributes->getInt('id');
        $recipes = $recetteRepository->latest_recipes(5, $category);
        return $this->json([
            'code' => 200,
            'data' => $recipes
        ]);
    }

    /**
     * @Route("/data_recipes", name="data_recipes", methods={"GET"})
     * @param Request $request
     * @param RecetteRepository $recetteRepository
     * @param CategorieRepository $categorieRepository
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function data_recipes(Request $request, RecetteRepository $recetteRepository, CategorieRepository $categorieRepository, UserRepository $userRepository)
    {
        $search = new SearchData();
        if ($request->query->has('q')) {
            $search->q = $request->query->get('q');
        }
        if ($request->query->has('level') && $request->query->get('level') != null) {
            $search->categorie = $request->query->get('category');
        }
        if ($request->query->has('category') && $request->query->get('category') != null) {
            $search->categorie = $categorieRepository->find($request->query->get('category'));
        }
        if ($request->query->has('slug') && $request->query->get('slug') != null) {
            //$slug = $this->slug_generate($request->query->get('username'));
            $search->user = $userRepository->find_by_slug($request->query->get('slug'));
        }
        if ($request->query->has('start') && $request->query->get('start') != null) {
            $search->start = strtotime($request->query->get('start'));
            $search->start = new \DateTime(date('m/d/Y', $search->start));
        }
        if ($request->query->has('end') && $request->query->get('end') != null) {
            $search->end = strtotime($request->query->get('end'));
            $search->end = new \DateTime(date('m/d/Y H:i:s', $search->end));
        }

        $recipes = $recetteRepository->findSearch($search);

        sleep(2);
        return $this->json($recipes, 200, [], ['groups' => 'recette:read']);
    }

    /**
     * @Route("/recette/{id}/delete", name="recette_delete", methods={"POST"})
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

    /**
     * @Route("/recette/{id}/rate", name="recette_rate", methods={"GET", "POST"})
     * @param Request $request
     * @param NoteRepository $noteRepository
     * @param RecetteRepository $recetteRepository
     * @param UserRepository $userRepository
     */
    public function rate(Request $request, NoteRepository $noteRepository, RecetteRepository $recetteRepository, UserRepository $userRepository)
    {
        $recette = $recetteRepository->find($request->get('id'));
        $value = $request->get('value');
        $user = $userRepository->find(7);
        $updated = false;

        //dd($recette->getNotes()->getValues());
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($recette->getNotes() as $note) {
            if ($note->getAuteur()->getId() === $user->getId()) {
                $note->setValeur($value);
                $entityManager->persist($note);
                $updated = !$updated;
                $entityManager->flush();
                break;
            }
        }
        if (!$updated) {
            $_note = new Note();
            $_note->setValeur($value);
            $_note->setAuteur($user);
            $_note->setRecette($recette);
            $entityManager->persist($_note);
            $entityManager->flush();
            $recette->addNote($_note);
        }
        //dd($recette->getNotes()->getValues());
        $rating = $this->avg_rating($recette->getNotes());
        return $this->json(['rating' => $rating]);
        //return $this->json($recette, 200, [], ['groups' => 'recette:read']);
        //die();
    }

    /**
     * @Route("/recette/{id}/print")
     */
    public function export_pdf()
    {

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

    private function _avg_rating($notes)
    {
        $rating = 0;
        foreach ($notes as $note) {
            $rating += $note->getValeur();
        }
        return intval(ceil($rating / $notes->count()));
    }

    private function avg_rating($notes): int
    {
        $rating = 0;
        foreach ($notes as $note) {
            $rating += $note->getValeur();
        }
        return $rating == 0 ? 0 : intval(ceil($rating / $notes->count()));
    }


    /**
     * @Route("/generate_slug")
     */
    public function generate_slug(Request $request, RecetteRepository $recetteRepository)
    {
        $recettes = $recetteRepository->findAll();

        $em = $this->getDoctrine()->getManager();
        foreach ($recettes as $recette) {
            $slug = $this->slug_generate($recette->getNom().' '.$recette->getId());
            $recette->setSlug($slug);
            $em->persist($recette);
        }
        $em->flush();

        dd($recettes);
    }
}
