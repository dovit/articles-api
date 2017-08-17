<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ArticleType;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * Class ArticleController
 * @package AppBundle\Controller\Api\v1
 */
class ArticleController extends FOSRestController
{
    /**
     * @SWG\Get(
     *     description="Articles",
     *     path="/articles",
     *     tags={"article"},
     *     @SWG\Response(
     *          response="200",
     *          description="List of article"
     *      )
     * )
     *
     * @View()
     *
     * @Route("/articles", name="articles")
     *
     * @Method({"GET"})
     *
     */
    public function getArticlesAction()
    {
        $repository = $this->get('doctrine')
            ->getManager()
            ->getRepository('AppBundle:Article')
            ->findAll();
        return $this->handleView($this->view($repository, Response::HTTP_OK));
    }

    /**
     * @Method({"GET"})
     *
     * @SWG\Get(
     *   path="/articles/{article}",
     *   summary="Get an article",
     *   tags={"article"},
     *   description="Get an article",
     *   @SWG\Parameter(
     *     name="article",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="id of article"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Article"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="Bad request"
     *   )
     * )
     *
     * @View()
     *
     * @Route("/articles/{article}",
     *     name="article",
     *     requirements={"article": "\d+"},)
     *
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getArticleAction(Article $article)
    {
        return $this->handleView($this->view($article, Response::HTTP_OK));
    }

    /**
     * @Method({"POST"})
     *
     * @SWG\Post(
     *   path="/articles",
     *   summary="Create an article",
     *   tags={"article"},
     *   description="Create an article",
     *   @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="Article object",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/Article")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Article created"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="Bad request"
     *   )
     * )
     *
     * @View()
     *
     * @Route("/articles", name="article_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createArticleAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article,
            [
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            return $this->handleView($this->view([$article], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     *
     * @Method({"POST"})
     *
     * @SWG\Post(
     *   path="/articles/{article}",
     *   summary="Update an article",
     *   tags={"article"},
     *   description="Update an article",
     *   @SWG\Parameter(
     *     name="article",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="id of article"
     *   ),
     *   @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="Article object",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/Article")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Article created"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="Bad request"
     *   )
     * )
     *
     * @Route("/articles/{article}", name="article_update",
     *     requirements={"article": "\d+"})
     *
     * @ParamConverter("article", class="AppBundle:Article")
     *
     * @param Request $request
     *
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws HttpException
     */
    public function updateArticleAction(Request $request, Article $article)
    {
        if (!$article) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Article not found');
        }

        $form = $this->createForm(ArticleType::class, $article, ['method' => 'POST']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            return $this->handleView($this->view(['id' => $article->getId()], Response::HTTP_OK));
        }
        return $this->handleView($this->view([], Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Method({"DELETE"})
     *
     * @SWG\Delete(
     *   path="/articles/{article}",
     *   summary="Delete an article",
     *   tags={"article"},
     *   description="Delete an article",
     *   @SWG\Parameter(
     *     name="article",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="id of article"
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Article deleted"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="Bad request"
     *   )
     * )
     *
     * @Route("/articles/{article}",
     *     name="article_delete",
     *     requirements={"article": "\d+"})
     *
     * @ParamConverter("article", class="AppBundle:Article")
     *
     * @View()
     *
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws HttpException
     */
    public function deleteArticleAction(Article $article)
    {
        if (!$article) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Article not found');
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($article);
        $manager->flush();
        return $this->handleView($this->view([], Response::HTTP_NO_CONTENT));
    }
}
