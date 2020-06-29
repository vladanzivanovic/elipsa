<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Career;
use App\Entity\Collaborator;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\CareerRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CareerHandler
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
     * @var CareerRepository
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
     * @param CareerRepository         $repository
     * @param RouterInterface          $router
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        ValidatorHelper $validator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        CareerRepository $repository,
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
     * @param Career $career
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(Career $career): void
    {
        $errors = $this->validator->validate($career, null, "SetCareer");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->repository->persist($career);
        $this->repository->flush();

        $careerEmail = $this->prepareEmail($career);
        $event = new EmailEvent($careerEmail);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    /**
     * @param Career $career
     *
     * @return EmailModel
     * @throws \ReflectionException
     */
    private function prepareEmail(Career $career): EmailModel
    {
        $settings = $this->getSettings();

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_CAREER);
        $model->setTemplate('career');
        $model->setTo($settings['MAIN_EMAIL']);
        $model->setToName($settings['SITE_NAME']);
        $model->setSubject('Prijava za posao');
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'firstName' => $career->getFirstName(),
            'lastName' => $career->getLastName(),
            'careerEmail' => $career->getEmail(),
            'position' => $career->getPosition(),
            'accompanyingLetter' => $career->getAccompanyingLetter(),
            'cv' => null !== $career->getCv() ? $this->router->generate('app.download_doc', ['id' => $career->getCv()->getId()], RouterInterface::ABSOLUTE_URL) : '',
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