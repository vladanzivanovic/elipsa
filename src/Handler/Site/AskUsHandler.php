<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\AskUs;
use App\Entity\Loyalty;
use App\Event\EmailEvent;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\AskUsRepository;
use App\Repository\LoyaltyRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class AskUsHandler
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
     * @var AskUsRepository
     */
    private $askUsRepository;

    /**
     * @param ValidatorHelper          $validator
     * @param SettingsRepository       $settingsRepository
     * @param EventDispatcherInterface $dispatcher
     * @param LoyaltyRepository        $loyaltyRepository
     * @param AskUsRepository          $askUsRepository
     */
    public function __construct(
        ValidatorHelper $validator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        LoyaltyRepository $loyaltyRepository,
        AskUsRepository $askUsRepository
    ) {
        $this->validator = $validator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->loyaltyRepository = $loyaltyRepository;
        $this->askUsRepository = $askUsRepository;
    }

    /**
     * @param AskUs $askUs
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(AskUs $askUs): void
    {
        $errors = $this->validator->validate($askUs, null, "SetAskUs");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->loyaltyRepository->persist($askUs);
        $this->loyaltyRepository->flush();

        $askUsEmail = $this->prepareEmail($askUs);
        $event = new EmailEvent($askUsEmail);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    /**
     * @param AskUs $askUs
     *
     * @return EmailModel
     */
    private function prepareEmail(AskUs $askUs): EmailModel
    {
        $settings = $this->getSettings();


        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_CONTACT_US);
        $model->setTemplate('askUs');
        $model->setTo($settings['MAIN_EMAIL']);
        $model->setToName($settings['SITE_NAME']);
        $model->setSubject($askUs->getSubject());
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'firstName' => $askUs->getFirstName(),
            'lastName' => $askUs->getLastName(),
            'loyalty_email' => $askUs->getEmail(),
            'note' => $askUs->getNote(),
            'subject' => $askUs->getSubject(),
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