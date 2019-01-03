<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends Controller
{
    /**
     * @Route("/follow/{followedId}", name="user_follow")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $followedId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function followAction(Request $request, $followedId)
    {
        $followedUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($followedId);

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $currentUser->setFollowing($followedUser);
        $currentUser->incrementFollowingCounter();

        $followedUser->setFollower($currentUser);
        $followedUser->incrementFollowersCounter();

        $em = $this->getDoctrine()->getManager();
        $em->persist($currentUser);
        //$em->persist($followedUser);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/unfollow/{followedId}", name="user_unfollow")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $followedId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unfollowAction(Request $request, $followedId)
    {
        $followedUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($followedId);

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $currentUser->removeFollowing($followedUser);
        $currentUser->decrementFollowingCounter();

        $followedUser->removeFollower($currentUser);
        $followedUser->decrementFollowersCounter();

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
