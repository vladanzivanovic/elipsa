<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Collaborator;
use App\Entity\Image;
use App\Repository\CollaboratorRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CollaboratorRequestParser
{
    /**
     * @var CollaboratorRepository
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
     * @param CollaboratorRepository $repository
     * @param ImageService           $imageService
     * @param ParameterBagInterface  $parameterBag
     */
    public function __construct(
        CollaboratorRepository $repository,
        ImageService $imageService,
        ParameterBagInterface $parameterBag
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param ParameterBag $bag
     * @param ParameterBag $files
     *
     * @return Collaborator
     */
    public function parse(ParameterBag $bag, ParameterBag $files): Collaborator
    {
        $countByUser = $this->repository->count([
            'firstName' => $bag->get('first_name'),
            'lastName' => $bag->get('last_name'),
            'email' => $bag->get('email'),
        ]);

        if ($countByUser > 0) {
            throw new BadRequestHttpException('collaborator.message.already_applied');
        }

        $collaborator = new Collaborator();
        $collaborator->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setEmail($bag->get('email'))
            ->setZipCode($bag->getInt('zip_code'))
            ->setAddress($bag->get('address'))
            ->setCountry($bag->get('country'))
            ->setCity($bag->get('city'))
            ->setLocation($bag->getInt('location'))
            ->setNumberOfFloors($bag->getInt('no_floors'))
            ->setShoppingMall($bag->get('shopping_mall'))
            ->setSpaceSize($bag->getInt('space_size'))
            ->setStore($bag->getInt('space'))
            ->setPhone($bag->get('telephone'))
            ->setWebsite($bag->get('website'));

        if ($files->has('presentation')) {
            /** @var UploadedFile $file */
            $file = $files->get('presentation');

            $doc = new Image();
            $doc->setName($file->getClientOriginalName());
            $doc->setOriginalName($file->getClientOriginalName());
            $doc->setFile($file);
            $doc->setDevice(0);
            $doc->setRelatedToType(Image::RELATED_TYPE_COLLABORATOR);
            $doc->setIsMain(true);

            $collaborator->setPresentation($doc);
        }

        if (null !== $files->get('plan')) {
            $file = $files->get('plan');

            $doc = new Image();
            $doc->setName($file->getClientOriginalName());
            $doc->setOriginalName($file->getClientOriginalName());
            $doc->setFile($file);
            $doc->setDevice(0);
            $doc->setRelatedToType(Image::RELATED_TYPE_COLLABORATOR);
            $doc->setIsMain(true);

            $collaborator->setPlan($doc);
        }

        return $collaborator;
    }
}