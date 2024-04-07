<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordRequestMailer
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

    public function sendEmail(User $user, string $locale): void
    {
        $emailModelCustomer = $this->prepareEmail($user, $locale);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(User $user, string $locale): EmailModel
    {
        $officeInfo = $this->settingsCollector->collect('email');

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_RESET_PASSWORD);
        $model->setTemplate('resetPassword');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($this->translator->trans('email.reset_password.title', ['%siteName%' => $officeInfo['settings']['site_name']->getValue()]));
        $model->setFrom($officeInfo['settings']['main_email']->getValue());
        $model->setFromName($officeInfo['settings']['site_name']->getValue());
        $model->setReplyTo($officeInfo['settings']['main_email']->getValue());
        $model->setReplyToName($officeInfo['settings']['site_name']->getValue());
        $model->setTemplateData([
            'locale' => $locale,
            'token' => $user->getResetToken(),
            'office_info' => $officeInfo
        ]);

        return $model;
    }
}
