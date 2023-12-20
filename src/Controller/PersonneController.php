<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine) : Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();  

        return $this->render(
            'personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => false
            ]
        );
    }

    #[Route('/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'personne.list.age')]
    public function personnesBetweenAge(ManagerRegistry $doctrine, $ageMin, $ageMax) : Response
    {
        // $repository = $doctrine->getRepository(Personne::class);
        $repository = $this->em->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);

        return $this->render(
            'personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => false

            ]
        );
        
    }

    #[Route('/stats/{ageMin<\d+>}/{ageMax<\d+>}', name: 'personne.list.stats')]
    public function statsPersonnesBetweenAge(ManagerRegistry $doctrine, $ageMin, $ageMax) : Response
    {
        // $repository = $doctrine->getRepository(Personne::class);
        $repository = $this->em->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);

        // dd($stats);
        return $this->render(
            'personne/stats.html.twig', [
                'stats' => $stats[0],
                'ageMin' => $ageMin,
                'ageMax' => $ageMax

            ]
        );
        
    }

    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre) : Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonnes = count($repository->findAll());
        $nbPages = ceil($nbPersonnes / $nbre);
 
        $personnes = $repository->findBy(
            [], [], 
            limit: $nbre, 
            offset: ($page - 1) * $nbre
        );  

        return $this->render(
            'personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => true,
                'nbPages' => $nbPages,
                'page' => $page,
                'nbre' => $nbre
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
    
    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine, Request $request): Response
    {
        // $this->getDoctrine : Sf <= 5
        $entityManager = $doctrine->getManager();

        $personne = new Personne();
        // NOTE: $personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        // $personne = new Personne();
        // $personne->setFirstname('Michael');
        // $personne->setName('CHLON');
        // $personne->setAge('50');

        // $personne2 = new Personne();
        // $personne2->setFirstname('Toto');
        // $personne2->setName('CHLON');
        // $personne2->setAge('5');

        // Ajouter l'pération de la personne dans la transaction
        // $entityManager->persist($personne);
        // $entityManager->persist($personne2);

        // Execute la transaction
        // $entityManager->flush();
        
        // dump($request);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // dd($personne);
            // $personne = $form->getData();
            $entityManager->persist($personne);
            $entityManager->flush();
            $this->addFlash('success', "La personne a bien été ajoutée");

            return $this->redirectToRoute('personne.list.alls');
        } else {
            return $this->render(
                'personne/add-personne.html.twig', [
                    'form' => $form->createView(),
                ]
            );
        }

    }

    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age<\d+>}', name: 'personne.update')]
    public function updatePersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        string $name,
        string $firstname,
        int $age,
    ): Response {
        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        $personne->setName($name);
        $personne->setFirstname($firstname);
        $personne->setAge($age);

        $manager = $doctrine->getManager();
        $manager->persist($personne);

        $manager->flush();
        $this->addFlash('success', "La personne a bien été modifié");

        return $this->redirectToRoute('personne.list');
    }

    #[Route('/delete/{id<\d+>}', name: 'personne.delete')]
    public function deletePersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
    ) : Response {
        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }

        $manager = $doctrine->getManager();
        $manager->remove($personne);

        $manager->flush();
        $this->addFlash('success', "La personne a bien été supprimé");

        return $this->redirectToRoute('personne.list');
    }
}
