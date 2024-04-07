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
    private \App\Helper\ValidatorHelper $validator;

    private \App\Repository\SettingsRepository $settingsRepository;

    private \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher;

    private \App\Repository\CareerRepository $repository;
    private \Symfony\Component\Routing\RouterInterface $router;

    /**
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        ValidatorHelper $validator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        CareerRepository $repository,
        RouterInterface $router
    ) {
        $this->validator = $validator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->router = $router;
    }

    /**
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
     * @throws \ReflectionException
     */
    private function prepareEmail(Career $career): EmailModel
    {
        $settings = $this->getSettings();

        $position = $career->getPosition()->getTranslationByLocale('rs')->getTitle();

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
            'position' => $position,
            'accompanyingLetter' => $career->getAccompanyingLetter(),
            'cv' => $career->getCv() instanceof \App\Entity\Image ? $this->router->generate('app.download_doc', ['id' => $career->getCv()->getId()], RouterInterface::ABSOLUTE_URL) : '',
        ]);

        return $model;
    }

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