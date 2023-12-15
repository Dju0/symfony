<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\ProgramDuration;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/program', name: 'program_')]
Class ProgramController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
         $programs = $programRepository->findAll();

         return $this->render(
             'program/index.html.twig',
             ['programs' => $programs]
         );
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, SluggerInterface $slugger) : Response
    {
        // Create a new Category Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le nouveau programme a été ajouté avec succès.');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('julienginestal@orange.fr')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));
                
            $mailer->send($email);

        return $this->redirectToRoute('program_index');
        }
    
        return $this->render('program/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, Program $program, SluggerInterface $slugger, ProgramDuration $programDuration): Response
    {
        $slug = $slugger->slug($program->getTitle());
        $program->setSlug($slug);

        $duration = $programDuration->calculate($program);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'duration' => $duration,
        ]);
    }

    #[Route('/{slug}/season/{season}', name: 'season_show')]
    public function showSeason(string $slug, Program $program, SluggerInterface $slugger, Season $season): Response
    {   
        $slug = $slugger->slug($program->getTitle());
        $program->setSlug($slug);
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    #[Route('/{slug}/season/{season}/episode/{episode}', name: 'episode_show')]
    public function showEpisode(string $slug, Program $program, Season $season, Episode $episode, SluggerInterface $slugger, Request $request, UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        $slug = $slugger->slug($program->getTitle());
        $program->setSlug($slug);
       
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le commentaire à l'épisode et à l'utilisateur
            $comment->setEpisode($episode);
            $comment->setAuthor($user); // Assurez-vous que l'utilisateur est connecté

            // Enregistrer le commentaire en base de données
            $entityManager->persist($comment);
            $entityManager->flush();

            // Rediriger vers la même page pour éviter les soumissions de formulaire en double
            return $this->redirectToRoute('program_episode_show', [
                'slug' => $program ->getSlug(),
                'season' => $season->getId(),
                'episode' => $episode->getId()
            ]);
        }

        // Récupérer les commentaires de l'épisode
        $comments = $entityManager->getRepository(Comment::class)->findBy(['episode' => $episode]);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
            'comments' => $comments,
        ]);
    }
    
    #[Route('/{slug}/edit', name: 'edit')]
    public function edit(string $slug,Request $request, ProgramRepository $programRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = $programRepository->findOneBy(['slug' => $slug]);
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le programme a été mis à jour avec succès.');

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();

            $this->addFlash('danger', 'Le programme a été supprimé.');

            return $this->redirectToRoute('program_index');
        }

        return $this->redirectToRoute('program_index');
    }



}