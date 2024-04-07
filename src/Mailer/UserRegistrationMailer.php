<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserRegistrationMailer
{
    private EventDispatcherInterface $dispatcher;

    private TranslatorInterface $translator;

    private SettingsCollector $settingsCollector;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        SettingsCollector $settingsCollector
    ) {
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->settingsCollector = $settingsCollector;
    }

    public function sendEmail(array $viewData, User $user): void
    {
        $emailModelCustomer = $this->prepareEmail($viewData, $user);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(array $viewData, User $user): EmailModel
    {
        $officeInfo = $this->settingsCollector->collect('email');

        $viewData['office_info'] = $officeInfo;

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_REGISTRATION);
        $model->setTemplate('registration');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($this->translator->trans('email.registration.title', ['%siteName%' => $officeInfo['settings']['site_name']->getValue()]));
        $model->setFrom($officeInfo['settings']['main_email']->getValue());
        $model->setFromName($officeInfo['settings']['site_name']->getValue());
        $model->setReplyTo($officeInfo['settings']['main_email']->getValue());
        $model->setReplyToName($officeInfo['settings']['site_name']->getValue());
        $model->setTemplateData($viewData);

        return $model;
    }
}
