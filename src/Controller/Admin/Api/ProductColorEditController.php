<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Handler\ProductColorHandler;
use App\Parser\ColorRequestParser;
use App\Request\Dto\Admin\ColorEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductColorEditController extends AbstractController
{
    public function __construct(
        private readonly ColorRequestParser $requestParser,
        private readonly ProductColorHandler $colorHandler,
        private readonly TranslatorInterface $translator
    ) {}

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/add-color', name: 'admin.add_color_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(ColorEditRequestDto $colorEditRequestDto)
    {
        $productColor = $this->requestParser->parse($colorEditRequestDto);

        $this->colorHandler->save($productColor);

        $colorEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/edit-color/{id}', name: 'admin.edit_color_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(ColorEditRequestDto $colorEditRequestDto, ProductColor $productColor)
    {
        $productColor = $this->requestParser->parse($colorEditRequestDto, $productColor);

        $this->colorHandler->save($productColor);

        $colorEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }
}
