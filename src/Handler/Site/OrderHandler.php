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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @var bool
     */
    private $isSuccessfulTransaction = false;

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
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \Exception
     */
    public function save(ShopOrder $order, string $group = null): int
    {
        $errors = $this->validator->validate($order, null, $group);

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
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function remove(ShopOrder $order): void
    {
        $this->orderRepository->delete($order);

        $this->orderRepository->flush();
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
        $order = $this->orderRepository->getByToken($this->session->get('order'));
        $order->setCoupon($coupon);

        $this->orderRepository->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \ReflectionException
     * @throws NonUniqueResultException
     */
    public function completeCheckoutOnSuccess(ShopOrder $order, string $locale, ParameterBag $bag): void
    {
        $this->isSuccessfulTransaction = true;

        $order->setStatus(ShopOrder::STATUS_COMPLETED);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData([ShopOrder::CARD_TYPE_PRE_AUTH => $bag->all()]);
            $order->setStatus(ShopOrder::STATUS_AWAITING_AUTHORIZATION);
        }

        $this->orderRepository->flush();

//        $settings = $this->getSettings();
//
//        $this->sendEmail($order, $locale, $bag);

//        return ['order' => $order, 'settings' => $settings];
    }

    /**
     * @param string       $orderToken
     * @param string       $locale
     * @param ParameterBag $bag
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function completeCheckoutOnFail(string $orderToken, string $locale, ParameterBag $bag): array
    {
        $this->isSuccessfulTransaction = false;

        $order = $this->orderRepository->getByToken($orderToken);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData($bag->all());
        }

        $order->setStatus(ShopOrder::STATUS_FAILED);

        $settings = $this->getSettings();

        $user = $order->getUser();
        $user->setResetToken(null);
        $user->setResetRequestAt(null);

        $this->orderRepository->flush();

        $this->sendEmail($order, $locale, $bag);

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
    private function prepareEmail(
        ShopOrder $order,
        array $settings,
        bool $isAccountCreated,
        string $locale,
        ParameterBag $parameterBag
    ): EmailModel {
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
            'isSuccessfulTransaction' => $this->isSuccessfulTransaction
        ];

        if (true === $isAccountCreated) {
            $templateData['registrationToken'] = $user->getResetToken();
            $templateData['locale'] = $locale;
        }

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $templateData['transaction_date_time'] = $parameterBag->has('EXTRA_TRXDATE') ? new \DateTime($parameterBag->get('EXTRA_TRXDATE')) : null;
            $templateData['transaction_id'] = $parameterBag->get('TransId');
            $templateData['auth_code'] = $parameterBag->get('AuthCode');
            $templateData['payment_response'] = $parameterBag->get('Response');
            $templateData['proc_return_code'] = $parameterBag->get('ProcReturnCode');
            $templateData['md_status'] = $parameterBag->get('mdStatus');
        }

        $subject = true === $this->isSuccessfulTransaction ? $this->translator->trans('email.order.data.title', ['orderId' => $order->getId()]) : $this->translator->trans('email.order.data.title_unsucessfull', ['orderId' => $order->getId()]);

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_ORDERED);
        $model->setTemplate(true === $this->isSuccessfulTransaction ? 'order' : 'failedOrder');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($subject);
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData($templateData);

        return $model;
    }

    /**
     * @param ShopOrder    $order
     * @param string       $locale
     * @param ParameterBag $bag
     *
     * @throws \ReflectionException
     */
    private function sendEmail(ShopOrder $order, string $locale, ParameterBag $bag): void
    {
        $settings = $this->getSettings();

        $isAccountCreated = $order->getUser()->getResetToken() !== null;

        $emailModelCustomer = $this->prepareEmail($order, $settings, $isAccountCreated, $locale, $bag);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);

        if (true === $this->isSuccessfulTransaction) {
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
