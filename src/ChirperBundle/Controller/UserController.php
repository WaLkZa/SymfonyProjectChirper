<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\Chirp;
use ChirperBundle\Entity\User;
use ChirperBundle\Form\ChirpType;
use ChirperBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

//            $role = $this
//                ->getDoctrine()
//                ->getRepository(Role::class)
//                ->findOneBy(['name' => 'ROLE_USER']);
//            $user->addRole($role);
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($user);
//            $em->flush();
            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/discover", name="user_discover")
     */
    public function discoverAction()
    {
        $users = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/discover.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        $userId = $this->getUser()->getId();
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        $chirps = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->getAllChirpsByUserId($userId);

        $chirp = new Chirp();
        $form = $this->createForm(ChirpType::class, $chirp);

        return $this->render('user/profile.html.twig',
            [
                'user' => $user,
                'chirps' => $chirps,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/feed", name="user_feed")
     */
    public function feedAction() {
        $userId = $this->getUser()->getId();
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        $chirps = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->getAllChirps();

        return $this->render('user/feed.html.twig',
            [
                'user' => $user,
                'chirps' => $chirps
            ]);
    }
}
