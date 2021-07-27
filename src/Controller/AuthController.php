<?php

namespace App\Controller;

use App\Data\Service;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    use Service;

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setSlug($this->slug_generate($user->getUsername()));
            if ($request->get('role') == null) {
                $user->setRules('ROLE_USER');
            }
            else {
                $user->setRules($request->get('role'));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}

    /**
     * @Route("/me", name="me")
     * @param UserRepository $userRepository
     */
    public function me(UserRepository $userRepository)
    {
        $user = $userRepository->find($this->getUser()->getId());
        dd([$user, $this->getUser(), $user->getRoles()]);
    }

    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController'
        ]);
    }

    /**
     * @Route("/profile/{slug}", name="user_profile")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function profile(
        Request $request,
        UserRepository $userRepository,
        CategorieRepository $categorieRepository
    )
    {
        $slug = $request->get('slug');

        $user = $userRepository->find_by_slug($slug);
        $user->setUsername(strtoupper($user->getUsername()));
        //dd($user);
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'categories' => $categorieRepository->findAll(),
        ]);
    }


}
