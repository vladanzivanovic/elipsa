<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Career;
use App\Entity\Collaborator;
use App\Entity\Image;
use App\Entity\Loyalty;
use App\Repository\CareerDescriptionRepository;
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
     * @var CareerDescriptionRepository
     */
    private $descriptionRepository;

    /**
     * @param CareerRepository            $repository
     * @param ImageService                $imageService
     * @param ParameterBagInterface       $parameterBag
     * @param CareerDescriptionRepository $descriptionRepository
     */
    public function __construct(
        CareerRepository $repository,
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        CareerDescriptionRepository $descriptionRepository
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
        $this->parameterBag = $parameterBag;
        $this->descriptionRepository = $descriptionRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param ParameterBag $files
     *
     * @return Career
     */
    public function parse(ParameterBag $bag, ParameterBag $files): Career
    {
        $position = $this->descriptionRepository->find($bag->get('position'));

        $career = new Career();
        $career->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setEmail($bag->get('email'))
            ->setAccompanyingLetter($bag->get('accompanying_letter'))
            ->setPosition($position)
            ->setCity($bag->get('city'))
            ->setAddress($bag->get('address'))
            ->setMobilePhone($bag->get('mobile_phone'))
            ->setBirthDate(new \DateTime($bag->get('birth_date')))
            ->setSchool($bag->get('school'))
            ->setSchoolLevel($bag->get('school_level'))
            ->setSchoolTitle($bag->get('school_title'));
        
        if ($files->has('cv')) {
            /** @var UploadedFile $file */
            $file = $files->get('cv');

            $doc = new Image();
            $doc->setName($file->getClientOriginalName());
            $doc->setOriginalName($file->getClientOriginalName());
            $doc->setFile($file);
            $doc->setDevice(0);
            $doc->setRelatedToType(Image::RELATED_TYPE_CAREER);
            $doc->setIsMain(true);

            $career->setCv($doc);
        }
        
        return $career;
    }
}