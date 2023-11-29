<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Collector\SettingsCollector;
use App\Entity\Notification;
use App\Event\EmailEvent;
use App\Model\EmailModel;
use App\Repository\ProductRepository;
use App\View\NotificationView;
use App\View\ProductView;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SizeAvailableMailer
{
    private EventDispatcherInterface $dispatcher;

    private SettingsCollector $settingsCollector;

    private TranslatorInterface $translator;

    private ProductRepository $productRepository;

    private ProductView $productView;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        SettingsCollector $settingsCollector,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        ProductView $productView
    ) {
        $this->dispatcher = $dispatcher;
        $this->settingsCollector = $settingsCollector;
        $this->translator = $translator;
        $this->productRepository = $productRepository;
        $this->productView = $productView;
    }

    public function sendEmail(
        Notification $notification
    ): void {
        $viewData = $this->getViewData($notification);

        $emailModel = $this->prepareEmail($viewData);

        $event = new EmailEvent($emailModel);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    private function prepareEmail(
        array $viewData
    ): EmailModel {
        $settings = $this->settingsCollector->collect('email');

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_ORDERED);
        $model->setTemplate('sizeAvailable');
        $model->setTo($viewData['email_address']);
        $model->setSubject($this->translator->trans('email.notification.size.subject'));
        $model->setFrom($settings['main_email']->getValue());
        $model->setFromName($settings['site_name']->getValue());
        $model->setReplyTo($settings['main_email']->getValue());
        $model->setReplyToName($settings['site_name']->getValue());
        $model->setTemplateData($viewData+['settings' => $settings]);

        return $model;
    }

    private function getViewData(Notification $notification): array
    {
        $viewData = (new NotificationView())->view($notification);

        $product = $this->productRepository->find($notification->getPayload()['product']);

        $productView = $this->productView->view($product, $notification->getLocale());

        $viewData['product'] = $productView;

        return $viewData;
    }
}
