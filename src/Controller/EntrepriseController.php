<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    // Entre parenthèses il s’agit de l’URL qui sera appelée
    // name: 'app_entreprise' -> fait référence au nom de la méthode en question (on s’en sert par exemple si on veut voir le détail d’une entreprise ou autre)
    #[Route('/entreprise', name: 'app_entreprise')]
    // public function index(EntityManagerInterface $entityManager): Response
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {

        // $entreprises = $entityManager->getRepository(Entreprise::class)->findAll();
        // $entreprises = $entrepriseRepository->findAll();
        // SELECT * FROM entreprise WHERE ville = 'STRASBOURG' ORDER BY raisonSociale ASC
        $entreprises = $entrepriseRepository->findBy([], ["raisonSociale" => "ASC"]);

        # « render » fait le lien entre le Controller et la vue
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);

    }

    #[Route('/entreprise/new', name: 'new_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(Entreprise $entreprise = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$entreprise) 
        {
            $entreprise = new Entreprise();
        }
        
        // $form = … → méthode qui créé le formulaire
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données du formulaire
            $entreprise = $form->getData();
            // prepare PDO
            $entityManager->persist($entreprise); // persist() = prepare()
            // execute PDO
            $entityManager->flush(); // flush() = execute()

            return $this->redirectToRoute('app_entreprise');

        }
        
        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise' => $form,
            'edit' => $entreprise->getId()
        ]);

    }

    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete(Entreprise $entreprise, EntityManagerInterface $entityManager)
    {
        
        // remove : prépare la requête
        $entityManager->remove($entreprise);
        // Faire la requête SQL DELETE FROM
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise'); 

    }
    
    // Route avec /entreprise/{id} car on veut le détail d’UNE entreprise
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response 
    {

        return $this->render('entreprise/show.html.twig', [
            // on enlève les « s » car il s’agit d’un seul objet entreprise
            'entreprise' => $entreprise
        ]);

    }

}
