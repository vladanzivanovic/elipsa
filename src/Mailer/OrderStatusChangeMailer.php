<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\ShopOrder;
use App\Event\EmailEvent;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderStatusChangeMailer
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

    public function sendEmail(
        array $viewData,
        ShopOrder $order,
        string $locale
    ): void {
        $emailModelCustomer = $this->prepareEmail($viewData, $order, $locale);
        $event = new EmailEvent($emailModelCustomer);
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

        $subject = $this->translator->trans('email.order_status.title', ['%id%' => $order->getId()]);

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_ORDER_STATUS_CHANGE);
        $model->setTemplate('changeOrderStatus');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($settings['site_name']->getValue() .' - '. $subject);
        $model->setFrom($settings['main_email']->getValue());
        $model->setFromName($settings['site_name']->getValue());
        $model->setReplyTo($settings['main_email']->getValue());
        $model->setReplyToName($settings['site_name']->getValue());
        $model->setTemplateData($viewData);

        return $model;
    }
}
