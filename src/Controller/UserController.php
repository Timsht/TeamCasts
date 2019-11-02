<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Posts;
use App\Form\PostType;
use App\Entity\Friendship;
use App\Form\ConfigUserFormType;
use App\Form\PasswordUserFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * addFriend
     *
     * @param  mixed $id
     * @Route("/add_friend_{id}", name="add_friend", requirements={"id": "\d+"},  condition="request.isXmlHttpRequest()")
     */
    public function addFriend(int $id)
    {
        $friendship = new Friendship();
        $receiver = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($receiver === null || $receiver === $this->getUser()) {
            // Notification flash
            $this->addFlash("fail", "Une erreur est survenue. Veuillez réessayer ultérieurement.");
        }

        // Test for request inverse is exist or not
        $existRequest = $this->getDoctrine()->getRepository(Friendship::class)->checkLinkUser(
            $this->getUser()->getId(),
            $receiver->getId()
        );

        // If exist throw exception
        if ($existRequest !== null) {
            throw new \Exception("Request already exist");
        }

        $friendship->setAsk($this->getUser());
        $friendship->setReceive($receiver);

        $this->getUser()->addFriendship($friendship);
        $receiver->addFriendship($friendship);

        // Notification flash
        $this->addFlash("success", "Demande bien envoyée");

        $em = $this->getDoctrine()->getManager();
        // @ManyToMany User->friendships
        $em->persist($this->getUser());
        $em->persist($receiver);
        // New friendship in database
        $em->persist($friendship);
        $em->flush();

        return $this->json([
            'code' => '200'
        ], 200);
    }

    /**
     * manageFriend for accept request or delete friendship
     * @Route("/user/{id}/friend", name="app_manage", condition="request.isXmlHttpRequest()")
     * 
     * @param  mixed $id
     *
     * @return void
     */
    public function manageFriend(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $friendship = $em->getRepository(Friendship::class)->checkLinkUser($id, $this->getUser()->getId());

        if (!$friendship) {
            throw $this->createNotFoundException('No friendship found');
        }

        $friend = $this->getDoctrine()->getRepository(User::class)->find($id);

        // The $id value is the id of the user which have ask 
        // A friend request is obviously accept by the user which receive
        if ($id !== $friendship->getReceive()->getId() && $friendship->getValide() === 0) {
            $friendship->setValide(1);
            $this->getUser()->addFriend($friend);
            // Notification flash
            $this->addFlash("success", $friend->getUsername() . " est maintenant votre ami");
        
        } else if ($friendship->getValide() === 1) {
            // Friendship entity
            $this->getUser()->removeFriendship($friendship);
            $friend->removeFriendship($friendship);
            $em->remove($friendship);

            // Self-referencing User entity
            $this->getUser()->removeFriendTarget($friend);
            $this->getUser()->removeFriend($friend);
            
            // Notification flash
            $this->addFlash("success", $friend->getUsername() . " n'est plus votre ami");
        
        } else {
            $this->addFlash("fail", "$id , ".$friendship->getReceive()->getId()." Une erreure est survenue. Veuillez réessayer ultérieurement.");
            return $this->json([
                'code' => '200',
                'message' => 'Action failed',
                'class' => 'alert-danger',
                'count' => $this->getDoctrine()->getRepository(Friendship::class)->count([
                    'receive' => $this->getUser()->getId(),
                    'valide' => 0
                ])
            ], 200);
        }

        $em->flush();

        return $this->json([
            'code' => '200',
            'message' => 'Action successed',
            'class' => 'alert-success',
            'count' => $this->getDoctrine()->getRepository(Friendship::class)->count([
                'receive' => $this->getUser()->getId(),
                'valide' => 0
            ])
        ], 200);
    }

    /**
     * @Route("/friends/{slug}", name="app_friendsList")
     */
    public function userFriends(User $user): Response
    {
        return $this->render("user/friends.html.twig", array(
            'user' => $user,
            'friends' => $this->getListUserFriends()
        ));
    }

    /**
     * @Route("/config", name="app_config")
     * @return response
     */
    public function configUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(ConfigUserFormType::class, $this->getUser());
        $formPasswordUser = $this->createForm(PasswordUserFormType::class, $this->getUser());

        $formUser->handleRequest($request);
        $formPasswordUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {

            // em -> entityManager      
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash("success", "Modification enregistré");
        }

        if ($formPasswordUser->isSubmitted() && $formPasswordUser->isValid()) {
            
            $encoded = $encoder->encodePassword($this->getUser(), $formPasswordUser->getData()->getPassword());
            $this->getUser()->setPassword($encoded);

            // em -> entityManager      
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash("success", "Modification enregistré");
        }

        return $this->render("user/config.html.twig", array(
            "user" => $this->getUser(),     // layout
            "friends" => $this->getListUserFriends(),
            "formUser" => $formUser->createView(),
            "formPasswordUser" => $formPasswordUser->createView()
        ));
    }

    /**
     * @Route("delete/{id}", name="app_delete")
     */
    public function deleteProfil(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        if (in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
            $em->remove($user);
            
            $this->addFlash("success", "L'utilisateur " . $user->getUsername() ." a bien été supprimé");
        } else {
            // Action on own account
            if ($user === $this->getUser()) {
                $this->getUser()->setValide(0);
                $em->persist($user);
                $em->flush();
                $this->addFlash("success", "Votre compte est suspendu, vous pouvez le réactiver en vous reconnectant.");
                return $this->redirectToRoute("app_logout");
            } else {
                $this->addFlash("fail", "Opération impossible");
                return $this->redirectToRoute("homepage");
            }
        }
        $em->flush();

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute("admin_users");
        } else {
            return $this->redirectToRoute("app_logout");
        }
    }

    /**
     * userProfil
     *
     * @Route("/{slug}", name="app_profil")
     */
    public function userProfil($slug, Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array("slug" => $slug));

        if ($user === null) {
            throw new \Exception("Page not found");    
        }

        $friendship = $this->getDoctrine()->getRepository(Friendship::class)->checkLinkUser($this->getUser()->getId(), $user->getId());

        $post = new Posts();
        $formPost = $this->createForm(PostType::class, $post);

        $formPost->handleRequest($request);

        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setUser($user);
            $post->setAuthor($this->getUser());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();

            $this->addFlash("success", "Votre message a été posté avec succès");

            return $this->redirectToRoute("app_profil", array("slug" => $user->getSlug()));
        }


        $depotPost = $this->getDoctrine()->getRepository(Posts::class);
        // search post's users byorder reverse chronological
        $posts = $depotPost->findBy(array("user" => $user), array('id' => 'DESC'));

        return $this->render("user/user_profil.html.twig", array(
            "user" => $user,
            'friends' => $this->getListUserFriends(),
            "friendship" => $friendship,    // if valide
            'formPost' => $formPost->createView(),
            "posts" => $posts,
        ));
    }

    /**
     * numberAskFriend
     *
     * @param  mixed $user
     *
     * @return array
     */
    public function numberAskFriend(): Response
    {
        $friend_request = $this->getDoctrine()->getRepository(Friendship::class)->findBy(array(
            "receive" => $this->getUser()->getId(),
            "valide" => 0
        ));

        return $this->render("home/menu.html.twig", array(
            "friend_request" => $friend_request
        ));
    }

    /**
     * @return array
     */
    public function getListUserFriends(): array
    {
        $friends = $this->getDoctrine()->getRepository(Friendship::class)->findFriendList($this->getUser()->getId());

        return $friends;
    }
}
