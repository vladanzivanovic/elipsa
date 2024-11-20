<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\ShopOrder;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Formatter\Site\UserRegistrationFormatter;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCompleteMailer
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly TranslatorInterface $translator,
        private readonly UserRegistrationFormatter $userRegistrationFormatter,
        private readonly UserRegistrationMailer $userRegistrationMailer,
        private readonly SettingsCollector $settingsCollector,
    ) {}

    public function sendEmail(
        array $viewData,
        ShopOrder $order,
        string $locale
    ): void {
        $user = $order->getUser();
        $isSuccessfulTransaction = $viewData['is_successful_transaction'];

        $emailModelCustomer = $this->prepareEmail($viewData, $order, $locale);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

        $emailModelAdmin = $emailModelCustomer;
        $emailModelAdmin->setTo($emailModelCustomer->getFrom());
        $emailModelAdmin->setToName($emailModelCustomer->getFromName());

        $templateData = $emailModelAdmin->getTemplateData();
        $templateData['accountCreated'] = false;
        $emailModelAdmin->setTemplateData($templateData);

        $event = new EmailEvent($emailModelAdmin);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(
        array $viewData,
        ShopOrder $order,
        string $locale
    ): EmailModel {
        $officeInfo = $this->settingsCollector->collect('email');

        $viewData['locale'] = $locale;
        $viewData['office_info'] = $officeInfo;

        $user = $order->getUser();
        $settings = $officeInfo['settings'];
        $isSuccessfulTransaction = $viewData['is_successful_transaction'];

        $subject = true === $isSuccessfulTransaction ?
            $this->translator->trans('email.order.data.title', ['%orderId%' => '#'. $order->getId()]) :
            $this->translator->trans('email.order.data.title_unsuccessful', ['%orderId%' => '#'. $order->getId()]);

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_ORDERED);
        $model->setTemplate(true === $isSuccessfulTransaction ? 'order' : 'failedOrder');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($settings['site_name']['value'] .' - '. $subject);
        $model->setFrom($settings['main_email']['value']);
        $model->setFromName($settings['site_name']['value']);
        $model->setReplyTo($settings['main_email']['value']);
        $model->setReplyToName($settings['site_name']['value']);
        $model->setTemplateData($viewData);

        return $model;
    }

    private function sendUserRegistrationEmail(User $user, string $locale): void
    {
        $userFormattedData = $this->userRegistrationFormatter->formatResponse($user, $locale);

        $this->userRegistrationMailer->sendEmail($userFormattedData, $user);
    }
}
