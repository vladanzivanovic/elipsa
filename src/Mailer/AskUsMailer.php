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

    private SettingsCollector $settingsCollector;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        SettingsCollector $settingsCollector
    ) {
        $this->dispatcher = $dispatcher;
        $this->settingsCollector = $settingsCollector;
    }

    public function sendEmail(AskUs $askUs, string $locale): void
    {
        $emailModelCustomer = $this->prepareEmail($askUs, $locale);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(AskUs $askUs, string $locale): EmailModel
    {
        $officeInfo = $this->settingsCollector->collect('email');

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_CONTACT_US);
        $model->setTemplate('askUs');
        $model->setTo($officeInfo['settings']['main_email']->getValue());
        $model->setToName($officeInfo['settings']['site_name']->getValue());
        $model->setSubject($askUs->getSubject());
        $model->setFrom($officeInfo['settings']['main_email']->getValue());
        $model->setFromName($officeInfo['settings']['site_name']->getValue());
        $model->setReplyTo($askUs->getEmail());
        $model->setReplyToName($askUs->getFirstName().' '. $askUs->getLastName());
        $model->setTemplateData([
            'firstName' => $askUs->getFirstName(),
            'lastName' => $askUs->getLastName(),
            'telephone' => $askUs->getTelephone(),
            'contactEmail' => $askUs->getEmail(),
            'contactVia' => $askUs->getContactVia(),
            'note' => $askUs->getNote(),
            'subject' => $askUs->getSubject(),
            'office_info' => $officeInfo,
            'locale' => $locale,
        ]);

        return $model;
    }
}
