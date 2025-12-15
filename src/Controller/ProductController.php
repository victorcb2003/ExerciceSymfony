<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductsRepository;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Products;


final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(ProductsRepository $repository): Response
    {
        return $this->render('product/product.html.twig', [
            'product' => $repository->findAll(),
        ]);
    }

    #[Route('/{id<\d+>}',name : "product_show")]
    public function show($id, ProductsRepository $repository) : Response
    {
         return $this->render('product/show.html.twig', [
            'product' => $repository->findOneBy(['id' => $id]),
        ]);
    }

    #[Route('/new',name : "product_new")]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {      
        $product = new Products;

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $product->setUserid($this->getUser()->getUserIdentifier());

            $manager->persist($product);
            $manager->flush();

            // Redirect to avoid form resubmission and to have a proper URL for the created product
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id<\d+>}/edit',name:"product_edit")]
    public function edit(Products $product,EntityManagerInterface $manager, Request $request): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUserId = $this->getUser()->getUserIdentifier();
        if ($product->getUserid() !== $currentUserId) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce produit.');
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->flush();

            $this->addFlash(
                'notice',
                'Le Produit a été modifié'
            );

            return $this->render('product/show.html.twig', [
                "product" => $product,
            ]);
        }

        return $this->render('product/edit.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: "product_delete")]
    public function delete(Request $request,Products $product, EntityManagerInterface $manager): Response
    {
        // Require authentication
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Only owner or admin can delete
        $currentUserId = $this->getUser()->getUserIdentifier();
        if ($product->getUserid() !== $currentUserId && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce produit.');
        }

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('delete'.$product->getId(), $token)) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
            }

            $manager->remove($product);
            $manager->flush();

            $this->addFlash(
                'notice',
                'Le produit a été supprimé'
            );

            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/delete.html.twig', [
            'id' => $product->getId(),
        ]);
    }
}
