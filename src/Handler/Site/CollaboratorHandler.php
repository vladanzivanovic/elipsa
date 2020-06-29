<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Collaborator;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\CollaboratorRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CollaboratorHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var CollaboratorRepository
     */
    private $repository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ValidatorHelper          $validator
     * @param SettingsRepository       $settingsRepository
     * @param EventDispatcherInterface $dispatcher
     * @param CollaboratorRepository   $repository
     * @param RouterInterface          $router
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        ValidatorHelper $validator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        CollaboratorRepository $repository,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->validator = $validator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param Collaborator $collaborator
     *
     * @throws \Exception
     */
    public function save(Collaborator $collaborator): void
    {
        $errors = $this->validator->validate($collaborator, null, "SetCollaborator");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->repository->persist($collaborator);
        $this->repository->flush();

        $collaboratorEmail = $this->prepareEmail($collaborator);
        $event = new EmailEvent($collaboratorEmail);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    /**
     * @param Collaborator $collaborator
     *
     * @return EmailModel
     */
    private function prepareEmail(Collaborator $collaborator): EmailModel
    {
        $settings = $this->getSettings();

        $location = '';

        if (null !== $collaborator->getLocation()) {
            $location = $this->translator->trans(ConstantsHelper::getConstantName((string) $collaborator->getLocation(), 'LOCATION', Collaborator::class));
        }

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_COLLABORATOR);
        $model->setTemplate('collaborator');
        $model->setTo($settings['MAIN_EMAIL']);
        $model->setToName($settings['SITE_NAME']);
        $model->setSubject('Registracija u program za saradnike');
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'firstName' => $collaborator->getFirstName(),
            'lastName' => $collaborator->getLastName(),
            'collaboratorEmail' => $collaborator->getEmail(),
            'phone' => $collaborator->getPhone(),
            'website' => $collaborator->getWebsite(),
            'address' => $collaborator->getAddress(),
            'noFloors' => $collaborator->getNumberOfFloors(),
            'city' => $collaborator->getCity(),
            'zipCode' => $collaborator->getZipCode(),
            'country' => $collaborator->getCountry(),
            'hasSpace' => null !== $collaborator->getStore() ? $this->translator->trans(ConstantsHelper::getConstantName((string) $collaborator->getStore(), 'SPACE', Collaborator::class)) : '',
            'location' => $location,
            'shoppingMall' => $collaborator->getShoppingMall(),
            'spaceSize' => $collaborator->getSpaceSize(),
            'presentationLink' => null !== $collaborator->getPresentation() ? $this->router->generate('app.download_doc', ['id' => $collaborator->getPresentation()->getId()], RouterInterface::ABSOLUTE_URL) : '',
            'planLink' => null !== $collaborator->getPlan() ? $this->router->generate('app.download_doc', ['id' => $collaborator->getPlan()->getId()], RouterInterface::ABSOLUTE_URL) : '',
        ]);

        return $model;
    }

    /**
     * @return array
     */
    private function getSettings(): array
    {
        $settings = $this->settingsRepository->getSettingsForOrderEmail();
        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting['slug']] = $setting['value'];
        }

        return $formatted;
    }
}