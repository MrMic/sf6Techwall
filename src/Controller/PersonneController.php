<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine) : Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();  

        return $this->render(
            'personne/index.html.twig', [
            'personnes' => $personnes
            ]
        );
    }

    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre) : Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonnes = count($repository->findAll());
        $nbPages = ceil($nbPersonnes / $nbre);
        // TODO: Finir calcul pagination
 
        $personnes = $repository->findBy(
            [], [], 
            limit: $nbre, 
            offset: ($page - 1) * $nbre
        );  

        return $this->render(
            'personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => true
            ]
        );
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null) : Response
    // public function detail(ManagerRegistry $doctrine, int $id) : Response
    {
        // $repository = $doctrine->getRepository(Personne::class);
        // $personne = $repository->find($id);  

        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }

        return $this->render(
            'personne/detail.html.twig', [
            'personne' => $personne
            ]
        );
    }
    
    #[Route('/add', name: 'parsonne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        // $this->getDoctrine : Sf <= 5
        $entityManager = $doctrine->getManager();

        $personne = new Personne();
        $personne->setFirstname('Michael');
        $personne->setName('CHLON');
        $personne->setAge('50');

        // $personne2 = new Personne();
        // $personne2->setFirstname('Toto');
        // $personne2->setName('CHLON');
        // $personne2->setAge('5');

        // Ajouter l'pération de la personne dans la transaction
        $entityManager->persist($personne);
        // $entityManager->persist($personne2);

        // Execute la transaction
        $entityManager->flush();

        return $this->render(
            'personne/detail.html.twig', [
            'personne' => $personne
            ]
        );
    }
}
