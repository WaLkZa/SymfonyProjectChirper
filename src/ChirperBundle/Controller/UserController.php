<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\Chirp;
use ChirperBundle\Entity\Role;
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
            $userNameForm = $form->getData()->getUsername();

            $userForm = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['username' => $userNameForm]);

            if (null !== $userForm) {
                $this->addFlash('info', "That username " . $userNameForm . " already taken!");
                return $this->render('user/register.html.twig');
            }

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $role = $this
                ->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/discover", name="user_discover")
     */
    public function discoverAction()
    {
        $currentUserId = $this->getUser()->getId();

        $users = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->getAllUsersExceptCurrentLogged($currentUserId);

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

        $chirpsCount = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->countAllUserChirps($userId);

        $followingCount = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->countUserFollowers($userId);

        $followersCount = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->countUserFollowing($userId);

        return $this->render('user/profile.html.twig',
            [
                'user' => $user,
                'chirps' => $chirps,
                'form' => $form->createView(),
                'chirpsCount' => $chirpsCount,
                'followingCount' => $followingCount['counter'],
                'followersCount' => $followersCount['counter']
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

        $follewedChirps = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->getAllChirpsByFollowedUsers($userId);

        return $this->render('user/feed.html.twig',
            [
                'user' => $user,
                'chirps' => $follewedChirps
            ]);
    }

    /**
     * @Route("/profile/{userId}", name="user_foreign_profile", requirements={"userId": "\d+"})
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function foreignProfileAction(Request $request, $userId)
    {
        /** @var User $currentLoggedUser */
        $currentLoggedUser = $this->getUser();
        $currentLoggedUserId = $currentLoggedUser->getId();

        if ($userId == $currentLoggedUserId) {
            return $this->redirectToRoute('user_profile');
        }

        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        if ($user === null) {
            return $this->redirectToRoute('user_discover');
        }

        $chirps = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->getAllChirpsByUserId($userId);

        if ($chirps === null) {
            return $this->redirectToRoute('user_discover');
        }

        $chirpsCount = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->countAllUserChirps($userId);

        $followingCount = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->countUserFollowers($userId);

        $followersCount = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->countUserFollowing($userId);

        $isFollowed = $currentLoggedUser->isUserFollowed($user);

        return $this->render('user/foreign_profile.html.twig',
            [
                'user' => $user,
                'chirps' => $chirps,
                'chirpsCount' => $chirpsCount,
                'followingCount' => $followingCount['counter'],
                'followersCount' => $followersCount['counter'],
                'isFollowed' => $isFollowed
            ]
        );
    }
}
