<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

// use FOS\RestBundle\Controller\Annotations\Get;
// use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Pagerfanta\Pagerfanta;

use App\Representation\Articles;
// use FOS\RestBundle\View\ViewHandler;

class ArticleController extends FOSRestController
{
    public $data;
    public $meta;
    
    public function __construct(Pagerfanta $data)
    {
        $this->data = $data;
        
        $this->addMeta('limit', $data->getMaxPerPage());
        $this->addMeta('current_items', count($data->getCurrentPageResults()));
        $this->addMeta('total_items', $data->getNbResults());
        $this->addMeta('offset', $data->getCurrentPageOffsetStart());
    }
    
    public function addMeta($name, $value)
    {
        if (isset($this->meta[$name])) {
            throw new \LogicException(sprintf('This meta already exists. You are trying to override this meta, use the setMeta method instead for the %s meta.', $name));
        }
        
        $this->setMeta($name, $value);
    }
    
    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }

    /**
     * @Rest\Get(
     *      path = "/articles/{id}", 
     *      name = "app_article_show", 
     *      requirements = {"id"="\id+"}
     * )
     * @return View
     */
    public function showAction()
    {
        $article = new Article();
        $article->setTitle("Mon super article")
                ->setContent("voici le contenue de mon super article ! waxwoo");
      
       
        return $article;
    }

    /**
     * @Rest\Post(
     *  path= "/articles",
     *  name = "app_article_show"
     * )
     * @return View(StatusCode=201)
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createAction(Article $article)
    {
       $em = $this->getDoctrine()->getManager();

       $em->persist($article);
       $em->flush();
    //    return $article;
    return $this->View(
        $article,
        Response::HTTP_CREATED,
        [
            'Location' => $this->generateUrl('app_article_show', ['id' => $article->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ]
    );
    }

    /**
     * @Rest\Get("/articlesHamza", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('App:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        // return $pager->getCurrentPageResults();
        return new Articles($pager);
    }


    // /**
    //  * @Route("/article", name="article")
    //  */
    // public function index()
    // {
    //     return $this->render('article/index.html.twig', [
    //         'controller_name' => 'ArticleController',
    //     ]);
    // }

    // /**
    //  * @Route("/article/{id}", name="article_show")
    //  */
    // public function show()
    // {

    //     $article = new Article();
    //     $article->setTitle("my first article for api")
    //             ->setContent("lorem upsum content of article ");
    //     $data = $this->get('jms_serializer')->serialize($article, 'json');

    //     $response = new Response($data);
    //     $response->headers->set('Content-Type', 'application/json');

    //     return $response;

    // //     return $this->render('article/index.html.twig', [
    // //         'controller_name' => 'ArticleController',
    // //     ]);
    //  }

    //  /**
    //  * @Route("/articleCreate", name="article_create")
    //  * @Method({"POST"})
    //  */
    // public function createAction(Request $request)
    // {
    //     $data = $request->getContent();
        
    //     $article = $this->get('jms_serializer')->deserialize($data, 'App\Entity\Article', 'json');

    //     $em = $this->getDoctrine()->getManager();
    //     $em->persist($article);
    //     $em->flush();

    //     return new Response('', Response::HTTP_CREATED);
    // }

    // /**
    //  * @Route("/articles", name="article_list")
    //  * @Method({"GET"})
    //  */
    // public function list()
    // {
    //     $article = $this->getDoctrine()->getRepository('App\Entity\Article')->findAll();
        
    //     $data = $this->get('jms_serializer')->serialize($article, 'json');

    //     $response = new Response($data);
    //     $response->headers->set('Content-Type', 'application/json');

    //     return $response;

    // }

}
