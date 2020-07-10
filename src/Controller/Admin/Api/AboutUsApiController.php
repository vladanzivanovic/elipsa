<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Settings;
use App\Parser\AboutUsRequestParser;
use App\Parser\ParserTrait;
use App\Repository\SettingsRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Blog;
use App\Entity\BlogTranslation;
use App\Handler\BlogHandler;
use App\Helper\ConstantsHelper;
use App\Parser\BlogRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AboutUsApiController extends AbstractController
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @param ParameterBagInterface $bag
     * @param SettingsRepository    $settingsRepository
     */
    public function __construct(
        ParameterBagInterface $bag,
        SettingsRepository $settingsRepository
    ) {
        $this->bag = $bag;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @Route("/api/about-us", name="admin.set_about_us_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setAboutUs(Request $request): JsonResponse
    {
        $this->save($request->request);


        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    private function save(ParameterBag $bag): void
    {
        $languages = $this->setLanguageArray($this->bag, $bag);

        foreach ($languages as $locale => $langBag) {
            $settings = $this->settingsRepository->findOneBy(['locale' => $locale, 'slug' => 'ABOUT_US']);

            if (!$settings instanceof Settings) {
                $settings = new Settings();

                $this->settingsRepository->persist($settings);
            }

            $settings->setName('O nama')
                ->setValue($bag->get($locale.'_description'))
                ->setSlug('ABOUT_US')
                ->setLocale($locale);
        }

        $this->settingsRepository->flush();
    }
}