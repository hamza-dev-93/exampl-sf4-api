<?php

namespace App\Controller;

use App\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AuthorController extends Controller
{
    /**
     * @Route("/author", name="author")
     */
    public function index()
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

     /**
     * @Route("/author/{id}", name="author_show")
     */
    public function show()
    {
        $author = new Author();
        $author->setFullname("Hamza dev")
                ->setBiography("ma super biography");

        $data = $this->get('serializer')->serialize($author, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

        // return $this->render('author/index.html.twig', [
        //     'controller_name' => 'AuthorController',
        // ]);
    }

    /**
     * @Route("/authorCreate", name="author_create")
     * @Method=({"POST"})
     */
    public function create(Request $request)
    {
        $data =$request->getContent();
        $author = $this->get('serializer')
                        ->deserialize($data, 'App\Entity\Author', 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();

        return new Response('save author', Response::HTTP_CREATED);

    }


}
