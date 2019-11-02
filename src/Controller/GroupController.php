<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/group")
 * @IsGranted("ROLE_USER")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/", name="group_index", methods={"GET"})
     * @Route("/all", name="all_groups", methods={"GET"})
     */
    public function index(GroupRepository $groupRepository, Request $request): Response
    {
        if($request->attributes->get('_route') === "all_groups" ){
            return $this->render('group/index.html.twig', array(
                'groups' => $groupRepository->findBy(array("valide" => 1)),
            ));
        }
        return $this->render('group/index.html.twig', array(
            'groups' => $this->getUser()->getGroups(),
        ));
    }

    /**
     * @Route("/new", name="group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setAuthor($this->getUser());
            $this->getUser()->addGroup($group);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            $this->addFlash("success", "Le groupe " . $group->getName() . " a bien été créé");

            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_show", methods={"GET"})
     */
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group): Response
    {
        if ($group->getAuthor() === $this->getUser()) {
            $form = $this->createForm(GroupType::class, $group);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash("success", "Le groupe " . $group->getName() . " a bien été mis à jour");

                return $this->redirectToRoute('group_index', [
                    'id' => $group->getId(),
                ]);
            }

            return $this->render('group/edit.html.twig', [
                'group' => $group,
                'form' => $form->createView(),
            ]);
        }
        $this->addFlash("fail", "Vous ne pouvez que modifier les groupes pour lesquels vous êtes l'auteur");
        return $this->render('group/index.html.twig', array(
            'groups' => $this->getUser()->getGroups(),
        ));
    }

    /**
     * @Route("/{id}", name="group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $this->addFlash("success", "Le groupe " . $group->getName() . " a bien été supprimé");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index');
    }

    /**
     * @Route("/manage/{id}", name="manage_group")
     */
    public function manage(Group $group)
    {
        $jsonResp = [];
        $em = $this->getDoctrine()->getManager();
        if(!$this->getUser()->getGroups()->contains($group)) {
            $this->getUser()->addGroup($group);
            
            $jsonResp['class'] = 'alert-success';
            $jsonResp['classBtn'] = 'btn-info';
            $jsonResp['contentBtn'] = 'Ne plus suivre';
            $this->addFlash("success", "Vous suivez désormais " . $group->getName());
        } else {
            $this->getUser()->removeGroup($group);

            $jsonResp['class'] = 'alert-warning';
            $jsonResp['classBtn'] = 'btn-light';
            $jsonResp['contentBtn'] = 'Spy';
            $this->addFlash("fail", "Vous ne suivez plus le groupe " . $group->getName());

        }
        $jsonResp['code'] = '200';
        $jsonResp['message'] = 'Action successed';
        
        $em->persist($this->getUser());
        $em->flush();
        return $this->json($jsonResp, 200);
    }
}
