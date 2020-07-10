<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Career;
use App\Entity\Collaborator;
use App\Entity\Image;
use App\Entity\Loyalty;
use App\Repository\CareerRepository;
use App\Repository\CollaboratorRepository;
use App\Repository\LoyaltyRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CareerRequestParser
{
    /**
     * @var CareerRepository
     */
    private $repository;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @param LoyaltyRepository     $loyaltyRepository
     * @param CareerRepository      $repository
     * @param ImageService          $imageService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        LoyaltyRepository $loyaltyRepository,
        CareerRepository $repository,
        ImageService $imageService,
        ParameterBagInterface $parameterBag
    ) {
        $this->loyaltyRepository = $loyaltyRepository;
        $this->repository = $repository;
        $this->imageService = $imageService;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param ParameterBag $bag
     * @param ParameterBag $files
     *
     * @return Career
     */
    public function parse(ParameterBag $bag, ParameterBag $files): Career
    {
        $countByUser = $this->repository->count([
            'firstName' => $bag->get('first_name'),
            'lastName' => $bag->get('last_name'),
            'email' => $bag->get('email'),
        ]);

        if ($countByUser > 0) {
            throw new BadRequestHttpException('career.message.already_applied');
        }

        $career = new Career();
        $career->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setEmail($bag->get('email'))
            ->setPosition($bag->get('position'))
            ->setAccompanyingLetter($bag->get('accompanying_letter'));
        
        if ($files->has('cv')) {
            /** @var UploadedFile $file */
            $file = $files->get('cv');

            $doc = new Image();
            $doc->setName($file->getClientOriginalName());
            $doc->setOriginalName($file->getClientOriginalName());
            $doc->setFile($file);
            $doc->setDevice(0);
            $doc->setRelatedToType(Image::RELATED_TYPE_COLLABORATOR);
            $doc->setIsMain(true);

            $career->setCv($doc);
        }
        
        return $career;
    }
}