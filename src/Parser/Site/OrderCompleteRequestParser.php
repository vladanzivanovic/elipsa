<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Entity\User;
use App\Repository\ShopOrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class OrderCompleteRequestParser
{
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * OrderCompleteRequestParser constructor.
     *
     * @param ShopOrderRepository          $orderRepository
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface        $parameterBag
     */
    public function __construct(
        ShopOrderRepository $orderRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        ParameterBagInterface $parameterBag
    ) {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param ParameterBag $bag
     * @param int          $orderId
     *
     * @return ShopOrder
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, int $orderId): ShopOrder
    {
        $order = $this->orderRepository->find($orderId);

        $order->setStatus(ShopOrder::STATUS_COMPLETED);
        $order->setPaymentType((int) $bag->get('payment_type'));
        $order->setNote($bag->get('order_note'));
        $order->setCompletedAt(new \DateTime());

        $this->setUser($bag, $order);
        $this->setAddress($bag, $order);
        $this->setProductsTranslation($order);

        return $order;
    }

    /**
     * @param ParameterBag $bag
     * @param ShopOrder    $order
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function setUser(ParameterBag $bag, ShopOrder $order): void
    {
        $user = $this->userRepository->findOneBy(['email' => $bag->get('email')]);

        if (!$user instanceof User) {
            $user = new User();
            $user->setEmail($bag->get('email'));
            $user->setStatus(User::STATUS_PENDING);
        }

        $user->setFirstName($bag->get('first_name'));
        $user->setLastName($bag->get('last_name'));

        if ($bag->get('create_account') && null === $user->getPassword()) {
            $encodedPwd = $this->passwordEncoder->encodePassword($user, $bag->get('password'));
            $user->setPassword($encodedPwd);
            $user->setRoles(['ROLE_USER']);
        }

        $user->addShopOrder($order);

        $order->setUser($user);
    }

    /**
     * @param ParameterBag $bag
     * @param ShopOrder    $order
     *
     * @return void
     */
    private function setAddress(ParameterBag $bag, ShopOrder $order): void
    {
        if(null === $address = $order->getUser()->getAddress()) {
            $address = new Address();
            $address->setFirstName($bag->get('first_name'));
            $address->setLastName($bag->get('last_name'));
            $address->setEmail($bag->get('email'));
            $address->setCountry($bag->get('country'));
            $address->setCity($bag->get('city'));
            $address->setAddress($bag->get('address'));
            $address->setPhone($bag->get('mobile_phone'));
            $address->setZipCode($bag->get('zip_code'));
        }

        $order->setBillingAddress($address);
        $order->setShippingAddress($address);
    }

    /**
     * @param ShopOrder $order
     *
     * @return void
     *
     * @throws \Exception
     */
    private function setProductsTranslation(ShopOrder $order): void
    {
        $orderProducts = $order->getOrderProducts();

        /** @var OrderProduct $orderProduct */
        foreach ($orderProducts->getIterator() as $orderProduct) {
            $productTrans = $orderProduct->getProduct()->getProductTranslations();

            /** @var ProductTranslation $trans */
            foreach ($productTrans->getIterator() as $trans) {
                $orderProductTrans = new OrderProductTranslation();

                $orderProductTrans->setTitle($trans->getTitle());
                $orderProductTrans->setSlug($trans->getSlug());
                $orderProductTrans->setLocale($trans->getLocale());
                $orderProductTrans->setOrderProduct($orderProduct);

                $orderProduct->addOrderProductTranslation($orderProductTrans);
            }
        }
    }
}