<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\AskUs;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AskUsMailer
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

    public function sendEmail(AskUs $askUs)
    {
        $emailModelCustomer = $this->prepareEmail($askUs);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(AskUs $askUs): EmailModel
    {
        $settings = $this->settingsCollector->collect('email');

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_CONTACT_US);
        $model->setTemplate('askUs');
        $model->setTo($settings['main_email']->getValue());
        $model->setToName($settings['site_name']->getValue());
        $model->setSubject($askUs->getSubject());
        $model->setFrom($settings['main_email']->getValue());
        $model->setFromName($settings['site_name']->getValue());
        $model->setReplyTo($settings['main_email']->getValue());
        $model->setReplyToName($settings['site_name']->getValue());
        $model->setTemplateData([
            'firstName' => $askUs->getFirstName(),
            'lastName' => $askUs->getLastName(),
            'telephone' => $askUs->getTelephone(),
            'contactVia' => $askUs->getContactVia(),
            'note' => $askUs->getNote(),
            'subject' => $askUs->getSubject(),
            'settings' => $settings,
        ]);

        return $model;
    }
}
