<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;

class NavbarController extends AbstractController
{
    public function navbar(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('_navbar.html.twig', [
            'categories' => $categories,
        ]);
    }
}
