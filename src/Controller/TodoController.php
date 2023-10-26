<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
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
    #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
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

    #[Route('/todo/update/{name}/{content}', name: 'todo.update')]
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

    #[Route('/todo/delete/{name}', name: 'todo.delete')]
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

    #[Route('/todo/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('todos');

        return $this->redirectToRoute('todo');
    }
}
