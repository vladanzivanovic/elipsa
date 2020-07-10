<?php

declare(strict_types=1);

namespace App\Handler;

use App\Helper\ValidatorHelper;
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
     * TagHandler constructor.
     *
     * @param TagsRepository  $tagsRepository
     * @param ValidatorHelper $validator
     */
    public function __construct(
        TagsRepository $tagsRepository,
        ValidatorHelper $validator
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->validator = $validator;
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
     * @param string $mainSLug
     * @return void
     */
    public function remove(string $mainSLug): void
    {
        $this->tagsRepository->remove($mainSLug);
    }
}