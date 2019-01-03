<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\Chirp;
use ChirperBundle\Entity\User;
use ChirperBundle\Form\ChirpType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChirpController extends Controller
{
    /**
     * @Route("/chirp/create", name="chirp_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $chirp = new Chirp();
        $form = $this->createForm(ChirpType::class, $chirp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $currentUser = $this->getUser();
            $chirp->setAuthor($currentUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($chirp);
            $em->flush();

            $this->addFlash('edit', "Chirp published.");

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/chirp/edit/{id}", name="chirp_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $chirp = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->find($id);

        if ($chirp === null) {
            return $this->redirectToRoute('homepage');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($chirp) && !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ChirpType::class, $chirp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $chirp->setDateAdded(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->merge($chirp);
            $em->flush();

            $this->addFlash('edit', "Chirp edited");

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('chirp/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $chirp->getAuthor(),
                'chirp' => $chirp
            ]
        );
    }

    /**
     * @Route("/chirp/delete/{id}", name="chirp_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $chirp = $this
            ->getDoctrine()
            ->getRepository(Chirp::class)
            ->find($id);

        if ($chirp === null) {
            return $this->redirectToRoute('homepage');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($chirp) && !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

            $currentUser = $this->getUser();
            $chirp->setAuthor($currentUser);
            $currentUser->removeChirpLike($chirp);
            $chirp->removeUserLike($currentUser);

            $em = $this->getDoctrine()->getManager();
            $em->remove($chirp);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
    }

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
}
