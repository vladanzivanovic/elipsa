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
    private \App\Repository\ShopOrderRepository $orderRepository;

    private \App\Repository\UserRepository $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * OrderCompleteRequestParser constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface        $parameterBag
     */
    public function __construct(
        ShopOrderRepository $orderRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function parse(ParameterBag $bag, string $orderToken): ShopOrder
    {
        $order = $this->orderRepository->getByToken($orderToken);

        $order->setPaymentType($bag->getInt('payment_type'));
        $order->setNote($bag->get('order_note'));
        $order->setCompletedAt(new \DateTime());

        $this->setUser($bag, $order);
        $this->setAddress($bag, $order);
        $this->setProductsTranslation($order);

        return $order;
    }

    /**
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

        if ($bag->get('create_account')) {
            $encodedPwd = $this->passwordEncoder->encodePassword($user, $bag->get('password'));
            $user->setPassword($encodedPwd);
            $user->setResetToken(bin2hex(openssl_random_pseudo_bytes(10)));
            $user->setResetRequestAt(new \DateTimeImmutable());
            $user->setRoles(['ROLE_USER']);
        }

        $user->addShopOrder($order);

        $order->setUser($user);
    }

    
    private function setAddress(ParameterBag $bag, ShopOrder $order): void
    {
        if(!($address = $order->getUser()->getAddress()) instanceof \App\Entity\Address) {
            $address = new Address();
            $address->setFirstName($bag->get('first_name'));
            $address->setLastName($bag->get('last_name'));
            $address->setEmail($bag->get('email'));
            $address->setCountry($bag->get('country'));
            $address->setCity($bag->get('city'));
            $address->setAddress($bag->get('address'));
            $address->setPhone($bag->get('mobile_phone'));
            $address->setZipCode((int) $bag->get('zip_code'));
        }

        $order->setBillingAddress($address);
        $order->setShippingAddress($address);
    }

    /**
     *
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
