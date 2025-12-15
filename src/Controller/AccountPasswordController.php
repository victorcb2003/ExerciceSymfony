<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    #[Route('/profile/password', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();

            if ($hasher->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
                $password = $hasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);
                $this->entityManager->persist($user); //non obligatoire sur MAJ contrairement sur une création
                $this->entityManager->flush($user);
                
                $this->addFlush(
                    'notice',
                    'Votre mot de passe a bien été mise à jour'
                );
            } else {
                $this->addFlush(
                    'notice',
                    'Votre mot de passe actuel est incorrect'
                );
            }

        } 
        return $this->render('account_password/index.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}