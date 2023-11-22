<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'first')]
    public function index2(): Response
    {
        return $this->render(
            'first/index.html.twig', [
            'firstName' => 'Mic',
            'lastName' => 'CHLON',
            'path' => '   ',
            'nom' => 'CHLON'
            ]
        );    
    }

    // #[Route('/sayHello/{name}', name: 'say.hello')]
    public function sayHello(Request $request, String $name): Response
    {
        // dd($request);

        return $this->render(
            'first/hello.html.twig', [
                'controller_name' => 'FirstController',
                'lastName' => 'CHLON',
                'firstName' => 'Mic',
                'nom' => $name,
                'path' => '   '
            ]
        );
        // return new Response('Hello World');
    }

    #[Route(
        '/multi/{entier1<\d+>}/{entier2<\d+>}', 
        name: 'multi', 
        // requirements: ['entier1' => '\d+', 'entier2' => '\d+']
    )]
    public function multiplication($entier1, $entier2): Response
    {
        $resultat = $entier1 * $entier2;
        return new Response("<h1>$resultat</h1>");
    }
}
