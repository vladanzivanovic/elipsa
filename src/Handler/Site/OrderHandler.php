<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\SettingsRepository;
use App\Repository\ShopOrderRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderHandler
{
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * @var ValidatorHelper
     */
    private $validator;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ValidatorHelper          $validator
     * @param ShopOrderRepository      $orderRepository
     * @param SessionInterface         $session
     * @param TranslatorInterface      $translator
     * @param SettingsRepository       $settingsRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ValidatorHelper $validator,
        ShopOrderRepository $orderRepository,
        SessionInterface $session,
        TranslatorInterface $translator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->validator = $validator;
        $this->session = $session;
        $this->translator = $translator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ShopOrder $order
     * @param bool      $shouldSendEmail
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(ShopOrder $order, bool $shouldSendEmail = false): int
    {
        $errors = $this->validator->validate($order, null, "SetOrder");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null == $order->getId()) {
            $this->orderRepository->persist($order);
        }

        $this->orderRepository->flush();

        if (true === $shouldSendEmail) {
            $emailModelCustomer = $this->prepareEmail($order);
            $event = new EmailEvent($emailModelCustomer);
            $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

            $emailModelAdmin = $emailModelCustomer;
            $emailModelAdmin->setTo($emailModelCustomer->getFrom());
            $emailModelAdmin->setToName($emailModelCustomer->getFromName());

            $event = new EmailEvent($emailModelAdmin);
            $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

            $user = $order->getUser();

//            if ($user->)
        }

        return $order->getId();
    }

    /**
     * @param PromotionCoupon $coupon
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setCoupon(PromotionCoupon $coupon): void
    {
        $order = $this->orderRepository->find($this->session->get('order'));
        $order->setCoupon($coupon);

        $this->orderRepository->flush();
    }

    /**
     * @param ShopOrder $order
     *
     * @return EmailModel
     * @throws \ReflectionException
     */
    private function prepareEmail(ShopOrder $order): EmailModel
    {
        $user = $order->getUser();
        $address = $order->getShippingAddress();
        $settings = $this->getSettings();
        $products = $order->getOrderProducts();

        $paymentType = ConstantsHelper::getConstantName((string) $order->getPaymentType(), 'PAYMENT_TYPE', ShopOrder::class);

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_ORDERED);
        $model->setTemplate('order');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($this->translator->trans('email.order.data.title', ['orderId' => $order->getId()]));
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'seller' => [
                'name'      => $settings['SITE_NAME'],
                'pib'       => $settings['PIB'],
                'account'   => $settings['ACCOUNT_NUMBER'],
                'email'     => $settings['MAIN_EMAIL'],
                'telephone' => $settings['TELEPHONE'],
                'mobile'    => $settings['MOBILE_PHONE'],
                'address'   => $settings['STREET'].', '.$settings['ZIP_CODE'].' '.$settings['CITY'],
            ],
            'buyer' => [
                'firstName'     => $address->getFirstName(),
                'lastName'      => $address->getLastName(),
                'city'          => $address->getCity(),
                'street'        => $address->getAddress(),
                'mobile'        => $address->getPhone(),
                'email'         => $address->getEmail(),
                'paymentType'   => $this->translator->trans('payment_type.'.$paymentType),
            ],
            'products'          => $products,
            'shippingPrice'     => $settings['SHIPPING_PRICE'],
            'promotion'         => $order->getCoupon()
        ]);

        return $model;
    }

    /**
     * @return array
     */
    private function getSettings(): array
    {
        $settings = $this->settingsRepository->getSettingsForOrderEmail();
        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting['slug']] = $setting['value'];
        }

        return $formatted;
    }
}