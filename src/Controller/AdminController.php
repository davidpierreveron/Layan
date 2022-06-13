<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\AdminRegistrationType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminusers(EntityManagerInterface $manager, UsersRepository $repo): Response
    {
        $colonnes = $manager->getClassMetadata(Users::class)->getFieldNames();
        dump($colonnes);

        $users= $repo->findAll();
        dump($users);
        return $this->render('admin/user.html.twig', [
            'colonnes' => $colonnes,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/{id}/edit-user", name="edit_users")
     */
    public function editUser(Users $user, Request $request, EntityManagerInterface $manager): Response
    {

        $formUser = $this->createForm(AdminRegistrationType::class, $user);
       $formUser->handleRequest($request);
        
        if ($formUser->isSubmitted() && $formUser->isValid()) { 
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Modification buen été enregistrer ");
            
        }
        return $this->render('admin/edit.html.twig', [
            $formUser->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}/delete-user", name="delete_users")
     */
    public function deleteUser(Users $users, EntityManagerInterface $manager): Response
    {
        $manager->remove($users);
        $manager->flush();
        $this->addFlash('success' , "Deleted");
        return $this->redirectToRoute('admin_users');
    }
}
