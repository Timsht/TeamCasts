<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Form\SearchUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * search
     *
     * @return void
     */
    public function search()
    {
        $form = $this->createForm(SearchUserType::class);

        return $this->render('search/search.html.twig', array(
            'search_form' => $form->createView()
        ));
    }

    /**
     * searchResult
     * @Route("/list", name="app_result_search")
     * 
     * @param  mixed $request
     * @param  mixed $userRepository
     *
     * @return void
     */
    public function searchResult(Request $request, UserRepository $userRepository)
    {
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $userRepository->findBySearchUsername($form->getData());
        } else {
            return $this->redirectToRoute("homepage");
        }

        $friends = [];

        if ($this->getUser() !== null ) {
            $friends = $this->getDoctrine()->getRepository(Friendship::class)->findFriendList($this->getUser()->getId());
        }

        return $this->render("search/result.html.twig", array(
            'users' => $result,
            'friends' => $friends
        ));
    }
}
