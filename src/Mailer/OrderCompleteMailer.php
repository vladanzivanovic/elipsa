<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\Settings;
use App\Entity\ShopOrder;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Model\EmailModel;
use App\View\OrderFinishView;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCompleteMailer
{
    private SettingsCollector $settingsCollector;

    private OrderFinishView $orderEmailView;

    private EventDispatcherInterface $dispatcher;

    private TranslatorInterface $translator;

    public function __construct(
        SettingsCollector $settingsCollector,
        OrderFinishView $orderEmailView,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator
    ) {
        $this->settingsCollector = $settingsCollector;
        $this->orderEmailView = $orderEmailView;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }
    public function sendEmail(
        array $viewData,
        ShopOrder $order
    ): void {
//        dd($viewData);
//        $settings = $this->settingsCollector->collect('email');
        $isSuccessfulTransaction = $viewData['is_successful_transaction'];

        $isAccountCreated = $order->getUser()->getResetToken() !== null;

        $emailModelCustomer = $this->prepareEmail($viewData, $order);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

        if (true === $isSuccessfulTransaction) {
            $emailModelAdmin = $emailModelCustomer;
            $emailModelAdmin->setTo($emailModelCustomer->getFrom());
            $emailModelAdmin->setToName($emailModelCustomer->getFromName());

            $templateData = $emailModelAdmin->getTemplateData();
            $templateData['accountCreated'] = false;
            $emailModelAdmin->setTemplateData($templateData);

            $event = new EmailEvent($emailModelAdmin);
            $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
        }
    }

    private function prepareEmail(
        array $viewData,
        ShopOrder $order
    ): EmailModel {
        $user = $order->getUser();
        $settings = $viewData['settings'];
        $isSuccessfulTransaction = $viewData['is_successful_transaction'];

        $subject = true === $isSuccessfulTransaction ?
            $this->translator->trans('email.order.data.title', ['orderId' => $order->getId()]) :
            $this->translator->trans('email.order.data.title_unsucessfull', ['orderId' => $order->getId()]);

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_ORDERED);
        $model->setTemplate(true === $isSuccessfulTransaction ? 'order' : 'failedOrder');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($subject);
        $model->setFrom($settings['main_email']->getValue());
        $model->setFromName($settings['site_name']->getValue());
        $model->setReplyTo($settings['main_email']->getValue());
        $model->setReplyToName($settings['site_name']->getValue());
        $model->setTemplateData($viewData);

        return $model;
    }
}
