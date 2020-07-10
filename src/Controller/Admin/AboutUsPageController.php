<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Tags;
use App\Formatter\Admin\BlogEditResponseFormatter;
use App\Repository\SettingsRepository;
use App\Repository\TagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AboutUsPageController extends AbstractController
{
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @Route("/about-us", name="admin.about_us_page", methods={"GET"})
     * @Template("Admin/Pages/aboutUs.html.twig")
     *
     * @return array
     */
    public function set(): array
    {
        $returnData = [];

        foreach (explode('|', $this->getParameter('locales')) as $locale) {
            $settings = $this->settingsRepository->findOneBy(['locale' => $locale, 'slug' => 'ABOUT_US']);

            $returnData[$locale.'_description'] = null !== $settings ? $settings->getValue() : '';
        }
        return $returnData;
    }
}