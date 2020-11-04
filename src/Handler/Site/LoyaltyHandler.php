<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Loyalty;
use App\Entity\NewsLetter;
use App\Event\EmailEvent;
use App\Event\NewsLetterEvent;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\LoyaltyRepository;
use App\Repository\NewsLetterRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class LoyaltyHandler
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
     * @var LoyaltyRepository
     */
    private $loyaltyRepository;
    /**
     * @var NewsLetterRepository
     */
    private $newsLetterRepository;

    /**
     * @param ValidatorHelper          $validator
     * @param SettingsRepository       $settingsRepository
     * @param EventDispatcherInterface $dispatcher
     * @param LoyaltyRepository        $loyaltyRepository
     * @param NewsLetterRepository     $newsLetterRepository
     */
    public function __construct(
        ValidatorHelper $validator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        LoyaltyRepository $loyaltyRepository,
        NewsLetterRepository $newsLetterRepository
    ) {
        $this->validator = $validator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->loyaltyRepository = $loyaltyRepository;
        $this->newsLetterRepository = $newsLetterRepository;
    }

    /**
     * @param Loyalty    $loyalty
     * @param NewsLetter $newsLetter
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(Loyalty $loyalty, NewsLetter $newsLetter): void
    {
        $errors = $this->validator->validate($loyalty, null, "SetLoyalty");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->loyaltyRepository->persist($loyalty);
        $this->loyaltyRepository->flush();

        $loyaltyEmail = $this->prepareEmail($loyalty);
        $event = new EmailEvent($loyaltyEmail);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

        $newsLetterEvent = new NewsLetterEvent($newsLetter, $loyalty);
        $eventName = NewsLetterEvent::UPDATE_USER;

        if (null === $newsLetter->getId()) {
            $eventName = NewsLetterEvent::ADD_USER;

            $this->newsLetterRepository->persist($newsLetter);
        }

        $this->dispatcher->dispatch($newsLetterEvent, $eventName);
    }

    /**
     * @param Loyalty $loyalty
     *
     * @return EmailModel
     * @throws \ReflectionException
     */
    private function prepareEmail(Loyalty $loyalty): EmailModel
    {
        $settings = $this->getSettings();

        $birthDate = $loyalty->getBirthDate() instanceof \DateTime ? $loyalty->getBirthDate()->format('d.m.Y') : '';

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_LOYALTY);
        $model->setTemplate('loyalty');
        $model->setTo($settings['MAIN_EMAIL']);
        $model->setToName($settings['SITE_NAME']);
        $model->setSubject('Registracija u loyalty program');
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'firstName' => $loyalty->getFirstName(),
            'lastName' => $loyalty->getLastName(),
            'loyalty_email' => $loyalty->getEmail(),
            'birthDate' => $birthDate,
            'occupation' => $loyalty->getOccupation(),
            'note' => $loyalty->getNote(),
            'mobilePhone' => $loyalty->getMobilePhone(),
            'rate' => $loyalty->getRate(),
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