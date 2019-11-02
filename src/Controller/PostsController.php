<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts/{id}", name="delete_post")
     */
    public function delete(Posts $post)
    {
        if ($this->getUser() === $post->getAuthor() || $this->getUser() === $post->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();

            $this->addFlash("success", "Le post a bien été supprimé");

            return $this->redirectToRoute("app_profil", array(
                "slug" => $post->getUser()->getSlug()
            ));
        }

        $this->addFlash("fail", "Erreur veuillez réessayer ultérieurement");

        return $this->redirectToRoute("homepage");
    }
}
