<?php

declare(strict_types=1);

namespace App\Controller\Site;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="site.home_page", methods={"GET"})
     * @Template("base.html.twig")
     *
     * @return array
     */
    public function index()
    {
        return [];
    }
}