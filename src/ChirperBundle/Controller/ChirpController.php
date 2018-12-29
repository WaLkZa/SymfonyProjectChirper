<?php

namespace ChirperBundle\Controller;

use ChirperBundle\Entity\Chirp;
use ChirperBundle\Entity\User;
use ChirperBundle\Form\ChirpType;
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

            $em = $this->getDoctrine()->getManager();
            $em->remove($chirp);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
    }

    private function getCurrentUser() {
        $userId = $this->getUser()->getId();
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        return $user;
    }
}
