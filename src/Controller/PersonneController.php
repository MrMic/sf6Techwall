<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'parsonne.add')]
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
