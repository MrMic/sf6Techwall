<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/sayHello/{name}', name: 'say.hello')]
    public function index(Request $request,$name): Response
    {
        // dd($request);

        return $this->render(
            'first/index.html.twig', [
                'controller_name' => 'FirstController',
                'lastName' => 'CHLON',
                'firstName' => 'Mic',
                'nom' => $name,
            ]
        );
        // return new Response('Hello World');
    }
}
