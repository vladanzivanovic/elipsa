<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use App\Event\EmailEvent;
use App\Formatter\SettingsFormatter;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserRegistrationMailer
{
    private EventDispatcherInterface $dispatcher;

    private TranslatorInterface $translator;

    private SettingsFormatter $settingsFormatter;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        SettingsFormatter $settingsFormatter
    ) {
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->settingsFormatter = $settingsFormatter;
    }

    public function sendEmail(array $viewData, User $user)
    {
        $emailModelCustomer = $this->prepareEmail($viewData, $user);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    /**
     * @param User   $user
     * @param string $locale
     *
     * @return EmailModel
     */
    private function prepareEmail(array $viewData, User $user): EmailModel
    {
        $settings = $viewData['settings'];

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_REGISTRATION);
        $model->setTemplate('registration');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($this->translator->trans('email.registration.title', ['%siteName%' => $settings['site_name']->getValue()]));
        $model->setFrom($settings['main_email']->getValue());
        $model->setFromName($settings['site_name']->getValue());
        $model->setReplyTo($settings['main_email']->getValue());
        $model->setReplyToName($settings['site_name']->getValue());
        $model->setTemplateData($viewData);

        return $model;
    }
}
