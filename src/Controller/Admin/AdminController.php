<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\Posts;
use App\Entity\Friendship;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use App\Form\CreateUserTypeFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{	
	/**
	 * @Route("/dashboard", name="admin_dashboard")
	 */
	public function adminDashboard()
	{
		$users = count($this->getDoctrine()->getRepository(User::class)->findAll());
		$posts = count($this->getDoctrine()->getRepository(Posts::class)->findAll());
		$friendships = count($this->getDoctrine()->getRepository(Friendship::class)->findAll());
		$group = count($this->getDoctrine()->getRepository(Group::class)->findAll());
		
		return $this->render("admin/dashboard.html.twig", array(
			"users" => $users,
			"posts" => $posts,
			"friendships" => $friendships,
			"group" => $group,
		));
	}

	/**
	 * @Route("/users", name="admin_users")
	 */
	public function adminUsers(UserRepository $userRepository, PaginatorInterface $paginator, Request $request)
	{
		$q = $request->query->get('u');
		$queryBuilder = $userRepository->findAllWithLimit();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
		);
		
		return $this->render("admin/users.html.twig", array(
			"pagination" => $pagination
		));
	}

	/**
	 * @Route("/searchUser", name="search_user", condition="request.isXmlHttpRequest()")
	 *
	 * @param Request $request
	 * @return void
	 */
	public function searchUser(Request $request)
	{
		$user = new User();
		$user->setUsername($request->get("query"));
		$users = $this->getDoctrine()->getRepository(User::class)->findBySearchUsername($user);

		return $this->render("admin/adminSearchUser.html.twig", array(
			"users" => $users
		));
	}

	/**
	 * @Route("/editUser/{id}", name="admin_edit_user")
	 * Manage Modal
	 * 
	 * @param User $user
	 * @param Request $request
	 * @return void
	 */
	public function editUser(User $user, Request $request)
	{
		$formEditUser = $this->createForm(EditUserType::class, $user);

		$formEditUser->handleRequest($request);
        if ($formEditUser->isSubmitted() && $formEditUser->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			
			$this->addFlash("success", "Modification bien enregistré");

            return $this->redirectToRoute("admin_users");
		}
		
		return $this->render("modal/editUser.html.twig", array(
			"formEditUser" => $formEditUser->createView(),
			"userEdit" => $user
		));
	}

	/**
	 * @Route("/create-user", name="create-user")
	 * Manage Modal
	 *
	 * @return void
	 */
	public function createUserModal(Request $request, UserPasswordEncoderInterface $encoder)
	{
		$newUser = new User();
		$formCreateUser = $this->createForm(CreateUserTypeFormType::class, $newUser);

		$formCreateUser->handleRequest($request);
        if ($formCreateUser->isSubmitted() && $formCreateUser->isValid()) {
			$encoded = $encoder->encodePassword($newUser, $formCreateUser->getData()->getPassword());
            $newUser->setPassword($encoded);

			$em = $this->getDoctrine()->getManager();
			$em->persist($newUser);
            $em->flush();

			$this->addFlash("success", "L'utilisateur ". $newUser->getUsername() ." a bien été créé.");

            return $this->redirectToRoute("admin_users");
		}

		return $this->render("modal/createUser.html.twig", array(
			"formCreateUser" => $formCreateUser->createView()
		));
	}


	/**
	 * @Route("/messages", name="admin_messages")
	 */
	public function adminMessages()
	{
		// TODO - template
		return $this->render("admin/messages.html.twig");
	}

	/**
	 * @Route("/color", name="admin_color")
	 */
	public function adminColor()
	{
		// TODO - template
		return $this->render("admin/color.html.twig");
	}
}
