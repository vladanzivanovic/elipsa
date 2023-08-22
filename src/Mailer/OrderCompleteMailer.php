<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\ShopOrder;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Formatter\Site\UserRegistrationFormatter;
use App\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCompleteMailer
{
    private EventDispatcherInterface $dispatcher;

    private TranslatorInterface $translator
    ;
    private UserRegistrationFormatter $userRegistrationFormatter;

    private UserRegistrationMailer $userRegistrationMailer;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        UserRegistrationFormatter $userRegistrationFormatter,
        UserRegistrationMailer $userRegistrationMailer
    ) {
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->userRegistrationFormatter = $userRegistrationFormatter;
        $this->userRegistrationMailer = $userRegistrationMailer;
    }
    public function sendEmail(
        array $viewData,
        ShopOrder $order,
        string $locale
    ): void {
        $user = $order->getUser();
        $isSuccessfulTransaction = $viewData['is_successful_transaction'];

        $emailModelCustomer = $this->prepareEmail($viewData, $order);
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

        if ( null !== $user->getResetToken() && true === $isSuccessfulTransaction) {
            $this->sendUserRegistrationEmail($user, $locale);
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

    private function sendUserRegistrationEmail(User $user, string $locale)
    {
        $userFormattedData = $this->userRegistrationFormatter->formatResponse($user, $locale);

        $this->userRegistrationMailer->sendEmail($userFormattedData, $user);
    }
}
