<?php
/**
 * Home controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET", "POST"},
     *     name="home",
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'index.html.twig',
        );
    }
}
