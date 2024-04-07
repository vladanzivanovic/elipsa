<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Tags;
use App\Helper\ValidatorHelper;
use App\Repository\BlogHasTagsRepository;
use App\Repository\ProductHasTagsRepository;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class TagHandler
{
    private TagsRepository $tagsRepository;

    private ValidatorHelper $validator;

    private ProductHasTagsRepository $productHasTagsRepository;

    private BlogHasTagsRepository $blogHasTagsRepository;

    public function __construct(
        TagsRepository $tagsRepository,
        ValidatorHelper $validator,
        ProductHasTagsRepository $productHasTagsRepository,
        BlogHasTagsRepository $blogHasTagsRepository
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->validator = $validator;
        $this->productHasTagsRepository = $productHasTagsRepository;
        $this->blogHasTagsRepository = $blogHasTagsRepository;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Tags $tags): void
    {
        $errors = $this->validator->validate($tags, null, "SetTag");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $tags->getId()) {
            $this->tagsRepository->persist($tags);
        }

        $this->tagsRepository->flush();
    }

    /**
     * @param string $mainSlug
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeFromProducts(Tags $tags): void
    {
        $productCount = $this->productHasTagsRepository->count(['tag' => $tags]);

        if ($productCount > 0) {
            foreach ($this->productHasTagsRepository->findBy(['tag' => $tags]) as $hasTag) {
                $this->productHasTagsRepository->delete($hasTag);
            }
        }

        $this->tagsRepository->delete($tags);

        $this->tagsRepository->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function removeFromBlog(Tags $tags): void
    {
        $blogCount = $this->blogHasTagsRepository->count(['tag' => $tags]);

        if ($blogCount > 0) {
            foreach ($this->blogHasTagsRepository->findBy(['tag' => $tags]) as $hasTag) {
                $this->blogHasTagsRepository->delete($hasTag);
            }
        }

        $this->tagsRepository->delete($tags);

        $this->tagsRepository->flush();
    }
}
