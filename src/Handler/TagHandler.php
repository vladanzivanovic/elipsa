<?php

declare(strict_types=1);

namespace App\Handler;

use App\Helper\ValidatorHelper;
use App\Repository\BlogHasTagsRepository;
use App\Repository\ProductHasTagsRepository;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class TagHandler
{
    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var ProductHasTagsRepository
     */
    private $productHasTagsRepository;

    /**
     * @var BlogHasTagsRepository
     */
    private $blogHasTagsRepository;

    /**
     * TagHandler constructor.
     *
     * @param TagsRepository           $tagsRepository
     * @param ValidatorHelper          $validator
     * @param ProductHasTagsRepository $productHasTagsRepository
     * @param BlogHasTagsRepository    $blogHasTagsRepository
     */
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
     * @param ArrayCollection $tags
     * @param bool            $isEdit
     *
     * @throws \Exception
     */
    public function save(ArrayCollection $tags, bool $isEdit = false): void
    {
        $errors = $this->validator->validate($tags, null, "SetTag");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (false === $isEdit) {
            foreach ($tags as $tag) {
                $this->tagsRepository->persist($tag);
            }
        }

        $this->tagsRepository->flush();
    }

    /**
     * @param string $mainSlug
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeFromProducts(string $mainSlug): void
    {
        $productCount = $this->productHasTagsRepository->count(['tag' => $mainSlug]);

        if ($productCount > 0) {
            foreach ($this->productHasTagsRepository->findBy(['tag' => $mainSlug]) as $hasTag) {
                $this->productHasTagsRepository->delete($hasTag);
            }
        }

        $this->remove($mainSlug);

    }

    /**
     * @param string $mainSlug
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeFromBlog(string $mainSlug): void
    {
        $blogCount = $this->blogHasTagsRepository->count(['tag' => $mainSlug]);

        if ($blogCount > 0) {
            foreach ($this->blogHasTagsRepository->findBy(['tag' => $mainSlug]) as $hasTag) {
                $this->blogHasTagsRepository->delete($hasTag);
            }
        }

        $this->remove($mainSlug);

    }

    /**
     * @param string $mainSlug
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function remove(string $mainSlug): void
    {
        foreach ($this->tagsRepository->findBy(['mainSlug' => $mainSlug]) as $tag) {
            $this->tagsRepository->delete($tag);
        }

        $this->tagsRepository->flush();
    }
}