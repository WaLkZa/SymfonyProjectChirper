<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\Chirp;
use ChirperBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends Controller
{
    /**
     * @Route("/chirp/like/{id}", name="chirp_like")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function likeAction(Request $request, $id)
    {
        $currentChirp = $this->getDoctrine()
            ->getRepository(Chirp::class)
            ->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $isLikeExist = $currentUser->isExistChirpLike($currentChirp);

        if ($isLikeExist)
        {
            $currentChirp->decrementLikesCounter();
            $currentUser->removeChirpLike($currentChirp);
        }
        else
        {
            $currentChirp->incrementLikesCounter();
            $currentUser->setChirpLike($currentChirp);
            $em->persist($currentUser);
            $em->persist($currentChirp);
        }

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/chirp/listLikes/{chirpId}", name="chirp_list_likes")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $chirpId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listLikesAction(Request $request, $chirpId)
    {
        $currentChirp = $this->getDoctrine()
            ->getRepository(Chirp::class)
            ->find($chirpId);

        $likesUsers = $currentChirp->getUserLikes();

        return $this->render('chirp/likes.html.twig', ['likes' => $likesUsers]);
    }
}