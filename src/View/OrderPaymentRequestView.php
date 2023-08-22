<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\OrderProduct;
use App\Entity\ShopOrder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class OrderPaymentRequestView
{
    private ParameterBagInterface $parameterBag;

    private RouterInterface $router;

    public function __construct(
        ParameterBagInterface $parameterBag,
        RouterInterface $router
    ) {

        $this->parameterBag = $parameterBag;
        $this->router = $router;
    }

    public function view(ShopOrder $order, string $locale): array
    {
        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_ON_DELIVERING) {
            return [];
        }

        return $this->intesaPayment($order, $locale);
    }

    private function intesaPayment(ShopOrder $order, string $locale): array
    {
        $orgClientId = $this->parameterBag->get('intesa_merchant_id');
        $oid = $order->getToken();
        $orgAmount = $order->getTotal().'.00';
        $orgOkUrl = $this->router->generate('site.checkout_completed_successful', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $orgFailUrl = $this->router->generate('site.checkout_failed', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $orgCancelUrl = $this->router->generate('site.checkout_page', ['_locale' => $locale, ], UrlGeneratorInterface::ABSOLUTE_URL);
        $orgTransactionType = "PreAuth";
        $orgCurrency = "941";

        $clientId = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgClientId));
        $amount = str_replace("|", "\\|", str_replace("\\", "\\\\", number_format((float)$orgAmount, 2, '.', '')));
        $okUrl = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgOkUrl));
        $failUrl = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgFailUrl));
        $cancelUrl = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgCancelUrl));
        $transactionType = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgTransactionType));
        $rnd = strtoupper(str_replace("|", "\\|", str_replace("\\", "\\\\", bin2hex(openssl_random_pseudo_bytes(10)))));

        $currency = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgCurrency));
        $storeKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $this->parameterBag->get('intesa_store_key')));

        $plainText = $clientId . "|" . $oid . "|" . $amount . "|" . $okUrl . "|" . $failUrl . "|" . $transactionType . "||" . $rnd . "||||" . $currency . "|" . $storeKey;

        $hashValue = hash('sha512', $plainText);
        $hash = base64_encode (pack('H*',$hashValue));

        $billingAddress =  $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $data = [
            'currency' => $currency,
            'trantype' => $transactionType,
            'okUrl' => $okUrl,
            'failUrl' => $failUrl,
            'amount' => $amount,
            'oid' => $oid,
            'clientid' => $clientId ,
            'storetype' => '3d_pay_hosting',
            'lang' => $locale === 'rs' ? 'sr' : $locale,
            'hashAlgorithm' => 'ver2',
            'rnd' => $rnd,
            'encoding' => 'utf-8',
            'hash' => $hash,
            'shopurl' => $cancelUrl,
            'tel' => $billingAddress->getPhone(),
            'email' => $billingAddress->getEmail(),
            'ShipToName' => $shippingAddress->getFirstName().' '.$shippingAddress->getLastName(),
            'ShipToStreet1' => $shippingAddress->getAddress(),
            'ShipToCity' => $shippingAddress->getCity(),
            'ShipToPostalCode' => $shippingAddress->getZipCode(),
        ];

        $i = 1;

        /** @var OrderProduct $orderProduct */
        foreach ($order->getOrderProducts() as $orderProduct) {

            $trans = $orderProduct->getByLocale($locale);

            $data['ItemNumber'.$i] = $trans->getTitle();
            $data['ProductCode'.$i] = $orderProduct->getCode();
            $data['Qty'.$i] = $orderProduct->getQuantity();
            $data['Price'.$i] = $orderProduct->getRealPrice().'.00';
            $data['Total'.$i] = $orderProduct->getTotal().'.00';

            $i++;
        }

        return $data;
    }
}
