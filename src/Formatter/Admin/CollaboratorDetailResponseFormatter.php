<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Collaborator;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CollaboratorDetailResponseFormatter
{
    use DataTableResponseTrait;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param Collaborator $collaborator
     *
     * @return array
     * @throws \ReflectionException
     */
    public function formatResponse(Collaborator $collaborator): array
    {
        $plan = $collaborator->getPlan();
        $presentation = $collaborator->getPresentation();

        return [
            'id' => $collaborator->getId(),
            'fullName' => $collaborator->getFirstName().' '.$collaborator->getLastName(),
            'email' => $collaborator->getEmail(),
            'phone' => $collaborator->getPhone(),
            'address' => $collaborator->getAddress(),
            'city' => $collaborator->getCity(),
            'zipCode' => $collaborator->getZipCode(),
            'country' => $collaborator->getCountry(),
            'applyingDate' => $collaborator->getCreatedAt()->format('d.m.Y'),
            'website' => $collaborator->getWebsite(),
            'shoppingMall' => $collaborator->getShoppingMall(),
            'spaceSize' => $collaborator->getSpaceSize(),
            'noFloors' => $collaborator->getNumberOfFloors(),
            'hasStore' => $this->translator->trans(ConstantsHelper::getConstantName((string) $collaborator->getStore(), 'SPACE', Collaborator::class)),
            'location' => $this->translator->trans(ConstantsHelper::getConstantName((string) $collaborator->getLocation(), 'LOCATION', Collaborator::class)),
            'plan_doc' => null !== $plan ? $this->router->generate('app.download_doc', ['id' => $plan->getName()], RouterInterface::ABSOLUTE_URL) : null,
            'presentation_doc' => null !== $presentation ? $this->router->generate('app.download_doc', ['id' => $presentation->getName()], RouterInterface::ABSOLUTE_URL) : null,
        ];
    }
}