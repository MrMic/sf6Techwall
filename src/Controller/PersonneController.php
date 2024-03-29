<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// ______________________________________________________________________
#[
    Route('/personne'), 
    IsGranted('ROLE_USER')
]
// ______________________________________________________________________
class PersonneController extends AbstractController
{
    private $em;

// ══════════════════════════════ __construct ══════════════════════════════
    public function __construct(
        EntityManagerInterface $em,
        private Helpers $helpers,
            private  EventDispatcherInterface $dispatcher
    )
    {
        $this->em = $em;
    }

// ══════════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════════
    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePDFPersonne(
        Personne $personne = null,
        PdfService $pdfService,
    ) : Response
    {
        $html = $this->render(
            'personne/detail.html.twig', [
               'personne' => $personne
            ]
        );


        $pdfService->showPdfFile($html);

    }

// ══════════════════════════════════════════════════════════════════════
    #[Route('/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'personne.list.age')]
    public function personnesBetweenAge(
        ManagerRegistry $doctrine,
        $ageMin,
        $ageMax,
    ) : Response
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

// ══════════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════════
    /**
     * Liste toutes les personnes 
     *
     * @param ManagerRegistry $doctrine Registry Manager 
     * @param Number $page Numero de page
     * @param Number $nbre Nombre d'item par page
     * @return Response
     */
    #[
        Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls'),
        IsGranted('ROLE_USER')
    ]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre) : Response
    {
        // $helpers = new Helpers();
        // echo($this->helpers->sayCc());

        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonnes = count($repository->findAll());
        $nbPages = ceil($nbPersonnes / $nbre);
 
        $personnes = $repository->findBy(
            [], [], 
            limit: $nbre, 
            offset: ($page - 1) * $nbre
        );  

        // ____________________ HACK: EVENT CREATED and DISPATCHED _________________
        $listAllPersonnesEvent = new ListAllPersonnesEvent(count($personnes));
        $this->dispatcher->dispatch(
            $listAllPersonnesEvent,
            ListAllPersonnesEvent::LIST_ALL_PERSONNES_EVENT,
        );
        // ______________________________________________________________________

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

    // ══════════════════════════════════════════════════════════════════════

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
    
    // ══════════════════════════════════════════════════════════════════════
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function editPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailerService
    ): Response
    {
        // NOTE: Security Access Control
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;

        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }

        // NOTE: $personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        // dump($request);
        $form->handleRequest($request);

        // ______________________________________________________________________
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($personne);
            // $personne = $form->getData();

            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $directory = $this->getParameter('photos_directory');
                $personne->setImage($uploaderService->upload($photoFile, $directory));
            }

            if ($new) {
                $message = "a bien été ajoutée avec succès";
                $personne->setCreatedBy($this->getUser());
            } else {
                $message = "a bien été mis à jour avec succès";
            }

            // $this->getDoctrine : Sf <= 5
            $entityManager = $doctrine->getManager();
            $entityManager->persist($personne);

            // Execute la transaction
            $entityManager->flush();

            if ($new) {
                // _______________________ Création de lévénement ____________________
                $addPersonneEvent = new AddPersonneEvent($personne);
                // ______________________ On dispatche l'événement ___________________
                $this->dispatcher->dispatch(
                    $addPersonneEvent,
                    $addPersonneEvent::ADD_PERSONNE_EVENT,
                );
            }

            $mailMessage = "Bonjour " . $personne->getName() . ' ' . $personne->getFirstname() . ' ' . $message;
            $mailerService->sendEmail(content: $mailMessage);

            $this->addFlash('success', $personne->getName() . ' ' . $personne->getFirstname() . ' ' . $message);

            return $this->redirectToRoute('personne.list.alls');
        } else {
            return $this->render(
                'personne/add-personne.html.twig', [
                    'form' => $form->createView(),
                ]
            );
        }

    }

// ______________________________________________________________________
    #[ 
        Route('/update/{id<\d+>}/{name}/{firstname}/{age<\d+>}', name: 'personne.update'),
        IsGranted('ROLE_ADMIN')
    ]
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

    // ______________________________________________________________________
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
