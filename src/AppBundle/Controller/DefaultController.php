<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     basePath="/app_dev.php"
 * )
 *
 * @SWG\Info(
 *     title="Articles api",
 *     version="0.1",
 * )
 */
class DefaultController extends Controller
{
    /**
     * @Route("/swagger.json", name="documentation")
     */
    public function documentationAction()
    {
        $swagger = \Swagger\scan($this->get('kernel')->getRootDir() . '/../src');
        return new JsonResponse($swagger);
    }
}
