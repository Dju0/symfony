<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;
use App\Repository\ActorRepository;

class NavbarController extends AbstractController
{
    public function navbar(CategoryRepository $categoryRepository, ActorRepository $actorRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $actors = $actorRepository->findAll();
        return $this->render('_navbar.html.twig', [
            'categories' => $categories,
            'actors' => $actors,
        ]);
    }
}
