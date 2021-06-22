<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/create", name="comment_create", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param RecetteRepository $recetteRepository
     * @return JsonResponse
     */
    public function comment_recipe(Request $request, UserRepository $userRepository, RecetteRepository $recetteRepository)
    {
        $recette = $recetteRepository->find(intval($request->get('recipe')));
        $comment = new Comment();
        $comment->setContent($request->get('content'));
        $comment->setRecette($recette);
        $user = $userRepository->find($this->getUser()->getId());
        $comment->setAuteur($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->json($comment, 200, [], ['groups' => 'comment:read']);
        /*return $this->json(
            [
                'comment' => $comment,
                'total' => 12
            ],
            200,
            [],
            ['groups' => 'comment:read']
        );*/
    }

    /**
     * @Route("/{id}/find", name="comment_find", methods={"GET"})
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function find_comment_by_recipe(Request $request, CommentRepository $commentRepository, SerializerInterface $serializer, NormalizerInterface $normalizer)
    {
        $recipe_id = $request->get('id');
        $comments = $commentRepository->findByRecipe($recipe_id);

        sleep(2);
        //dd($comments);

        /*$result = $serializer->normalize($comments, 'json', ['groups' => 'comment:read']);

        $commentsNormalizer = $normalizer->normalize($comments, null, ['groups' => 'comment:read']);
        $json = json_encode($commentsNormalizer);

        $response = new JsonResponse($result, 200);
        $response = new JsonResponse($json, 200, [], true);*/
        return $this->json($comments, 200, [], ['groups' => 'comment:read']);
    }
}
