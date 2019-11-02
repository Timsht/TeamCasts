<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        if ($this->getUser() !== null) {
            $friendsPosts = $this->getDoctrine()->getRepository(Posts::class)->getFriendsPosts($this->getUser(), 0);

            return $this->render("home/home.html.twig", array(
                "friendsPosts" => $friendsPosts
            ));
        }
        return $this->render("home/home.html.twig");
    }

    /**
     * @Route("/loadPosts/{offset}", condition="request.isXmlHttpRequest()")
     *
     * @return void
     */
    public function loadPosts($offset)
    {
        if (is_int($offset)) {
            throw new \Exception("Offset unvailable");
        }

        $friendsPosts = $this->getDoctrine()->getRepository(Posts::class)->getFriendsPosts($this->getUser(), $offset);

        return $this->render("home/wall.html.twig", array(
            "friendsPosts" => $friendsPosts
        ));
    }
    
    /**
     * Display flash message
     * 
     * @Route("/flash", name="flash_message", condition="request.isXmlHttpRequest()")
     *
     * @return void
     */
    public function displayFlash()
    {
        return $this->render("flash.html.twig");
    }
}
