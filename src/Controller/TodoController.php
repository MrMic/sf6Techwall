<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class TodoController extends AbstractController
{
    /**
     * Route par defaut /todo 
     *
     * @param  Request $request  
     * @return Response
     */
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        if (!$session->has('todos')) {
            $todos = [
            'achat' => 'Acheter clé USB',
            'cours' => 'Aller au cours',
            'correction' => 'Corriger mes examens',
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "La liste des todos a bien été initialisé !");
        }

        return $this->render(
            'todo/index.html.twig', [
            'controller_name' => 'TodoController',
            ]
        );
    }

    /**
     * Ajouter un todo
     *
     * @param Request $request Request Object
     * @param String  $name    Le nom du todo 
     * @param String  $content Le contenu du Todo 
     *
     * @return Response
     */
    #[Route('/add/{name?nom Par Defaut}/{content}', name: 'todo.add', defaults: ['content' => 'sf6'])]
    public function addTodo(Request $request, $name, $content): Response
    {
        $session = $request->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('error', "Le todo $name existe déjà !");
                return $this->redirectToRoute('todo');
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo $name a bien été ajouté !");
            };
        } else {
            $this->addFlash('error', "La liste des todos n'existe pas encore !");
        }

        return $this->render('todo/index.html.twig');

    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content): Response
    {
        $session = $request->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                $this->addFlash('error', "Le todo $name n'existe pas !");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo $name a bien été modifié !");
            };
        } else {
            $this->addFlash('error', "La liste des todos n'existe pas encore !");
        }

        return $this->render('todo/index.html.twig');

    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name ): Response
    {
        $session = $request->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                $this->addFlash('error', "Le todo $name n'existe pas !");
            } else {
                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo $name a bien été supprimé!");
            };
        } else {
            $this->addFlash('error', "La liste des todos n'existe pas encore !");
        }

        return $this->render('todo/index.html.twig');
    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('todos');

        return $this->redirectToRoute('todo');
    }
}
