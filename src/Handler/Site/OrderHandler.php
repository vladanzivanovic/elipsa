<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\OrderProductRepository;
use App\Repository\SettingsRepository;
use App\Repository\ShopOrderRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
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
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * @param ValidatorHelper          $validator
     * @param ShopOrderRepository      $orderRepository
     * @param SessionInterface         $session
     * @param TranslatorInterface      $translator
     * @param SettingsRepository       $settingsRepository
     * @param EventDispatcherInterface $dispatcher
     * @param OrderProductRepository   $orderProductRepository
     */
    public function __construct(
        ValidatorHelper $validator,
        ShopOrderRepository $orderRepository,
        SessionInterface $session,
        TranslatorInterface $translator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        OrderProductRepository $orderProductRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->validator = $validator;
        $this->session = $session;
        $this->translator = $translator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->orderProductRepository = $orderProductRepository;
    }

    /**
     * @param ShopOrder $order
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(ShopOrder $order): int
    {
        $errors = $this->validator->validate($order, null, "SetOrder");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null == $order->getId()) {
            $this->orderRepository->persist($order);
        }

        $this->orderRepository->flush();

        return $order->getId();
    }

    /**
     * @param OrderProduct $orderProduct
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeProduct(OrderProduct $orderProduct): void
    {
        $order = $orderProduct->getOrderId();

        $products = $order->getOrderProducts();

        if ($products->count() > 1) {
            $this->orderProductRepository->removeWithFlush($orderProduct);

            return;
        }

        $this->orderRepository->removeWithFlush($order);

        $this->session->remove('order');
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
     * @param int      $orderId
     * @param string   $locale
     *
     * @param InputBag $bag
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function completeCheckoutOnSuccess(int $orderId, string $locale, ParameterBag $bag): array
    {
        $order = $this->orderRepository->find($orderId);
        $order->setStatus(ShopOrder::STATUS_COMPLETED);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData([ShopOrder::CARD_TYPE_PRE_AUTH => $bag->all()]);
            $order->setStatus(ShopOrder::STATUS_AWAITING_AUTHORIZATION);
        }

        $this->orderRepository->flush();

        $settings = $this->getSettings();

        $isAccountCreated = $order->getUser()->getResetToken() !== null;

        $emailModelCustomer = $this->prepareEmail($order, $settings, $isAccountCreated, $locale, $bag);
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

        return ['order' => $order, 'settings' => $settings];
    }

    /**
     * @param int      $orderId
     * @param string   $locale
     *
     * @param InputBag $bag
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function completeCheckoutOnFail(int $orderId, ParameterBag $bag): array
    {
        $order = $this->orderRepository->find($orderId);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData($bag->all());
        }

        $order->setStatus(ShopOrder::STATUS_FAILED);

        $settings = $this->getSettings();

        $user = $order->getUser();
        $user->setResetToken(null);
        $user->setResetRequestAt(null);

        $this->orderRepository->flush();

        return ['order' => $order, 'settings' => $settings];
    }

    /**
     * @param ShopOrder    $order
     * @param array        $settings
     * @param bool         $isAccountCreated
     * @param string       $locale
     * @param ParameterBag $parameterBag
     *
     * @return EmailModel
     * @throws \ReflectionException
     */
    private function prepareEmail(ShopOrder $order, array $settings, bool $isAccountCreated, string $locale, ParameterBag $parameterBag): EmailModel
    {
        $user = $order->getUser();
        $address = $order->getShippingAddress();
        $products = $order->getOrderProducts();

        $paymentType = ConstantsHelper::getConstantName((string) $order->getPaymentType(), 'PAYMENT_TYPE', ShopOrder::class);

        $templateData = [
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
            'freeShipping'      => $settings['FREE_SHIPPING'],
            'promotion'         => $order->getCoupon(),
            'accountCreated'    => $isAccountCreated,
            'paymentType'       => $order->getPaymentType(),
            'orderId'           => $order->getId(),
        ];

        if (true === $isAccountCreated) {
            $templateData['registrationToken'] = $user->getResetToken();
            $templateData['locale'] = $locale;
        }

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $templateData['transaction_date_time'] = new \DateTime($parameterBag->get('EXTRA_TRXDATE'));
            $templateData['transaction_id'] = $parameterBag->get('TransId');
            $templateData['auth_code'] = $parameterBag->get('AuthCode');
            $templateData['payment_response'] = $parameterBag->get('Response');
            $templateData['proc_return_code'] = $parameterBag->get('ProcReturnCode');
            $templateData['md_status'] = $parameterBag->get('mdStatus');
        }

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
        $model->setTemplateData($templateData);

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