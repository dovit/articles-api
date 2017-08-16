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
     *     @SWG\Response(response="200", description="List of article")
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
     *
     * @Method({"GET"})
     *
     * @ApiDoc(
     *     description="Get an article",
     *     views={"article", "default"},
     *     section="article",
     * )
     *
     * @View()
     *
     * @Route("/article/{article}",
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
     *
     * @Method({"PUT"})
     *
     * @ApiDoc(
     *     description="Create an article",
     *     views={"article", "default"},
     *     section="article",
     *     input={
     *          "class"="AppBundle\Form\Type\ArticleType"
     *     }
     * )
     *
     * @View()
     *
     * @Route("/article", name="article_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createArticleAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            return $this->handleView($this->view(['id' => $article->getId()], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view([], Response::HTTP_BAD_REQUEST));
    }

    /**
     *
     * @Method({"POST"})
     *
     * @ApiDoc(
     *     description="Update an article",
     *     views={"article", "default"},
     *     section="article",
     *     input={
     *          "class"="AppBundle\Form\Type\ArticleType"
     *     }
     * )
     *
     * @Route("/article/{article}", name="article_update",
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
            throw new HttpException(404, 'Article not found');
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
     * @ApiDoc(
     *     description="Delete an article",
     *     views={"article", "default"},
     *     section="article"
     * )
     *
     * @Route("/article/{article}",
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
            throw new HttpException(404, 'Article not found');
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($article);
        $manager->flush();
        return $this->handleView($this->view([], Response::HTTP_OK));
    }
}
