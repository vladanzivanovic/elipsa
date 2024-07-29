<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
use App\Parser\ColorRequestParser;
use App\Parser\TagRequestParser;
use App\Request\Dto\Admin\TagEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TagEditController extends AbstractController
{
    public function __construct(
        private readonly TagRequestParser $requestParser,
        private readonly TagHandler $tagHandler,
        private readonly TranslatorInterface $translator
    ) {}

    /**
     *
     * @throws \Exception
     */
    #[Route(path: '/api/tag/product/add', name: 'admin.add_product_tag_api', options: ['expose' => true], methods: ['POST'])]
    #[Route(path: '/api/tag/blog/add', name: 'admin.add_blog_tag_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(TagEditRequestDto $tagEditRequestDto): JsonResponse
    {
        $tags = $this->requestParser->parse($tagEditRequestDto, null);

        $this->tagHandler->save($tags);

        $tagEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Exception
     */
    #[Route(path: '/api/tag/product/{slug}', name: 'admin.edit_product_tag_api', options: ['expose' => true], methods: ['PUT'])]
    #[Route(path: '/api/tag/blog/{slug}', name: 'admin.edit_blog_tag_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(TagEditRequestDto $tagEditRequestDto, string $slug): JsonResponse
    {
        $tags = $this->requestParser->parse($tagEditRequestDto, $slug);

        $this->tagHandler->save($tags);

        $tagEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }
}
