<?php

declare(strict_types=1);

namespace App\Provider;

use App\Entity\ShopOrder;
use App\Exception\PaymentRequestException;
use Ixopay\Client\Client;
use Ixopay\Client\Data\Customer;
use Ixopay\Client\Data\ThreeDSecureData;
use Ixopay\Client\Exception\GeneralErrorException;
use Ixopay\Client\Exception\RateLimitException;
use Ixopay\Client\Http\Exception\ClientException;
use Ixopay\Client\Transaction\Debit;
use Ixopay\Client\Transaction\Preauthorize;
use Ixopay\Client\Transaction\Result;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class BankArtPaymentProvider
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly array $countries,
    ) {}

    /**
     * @throws ClientException
     * @throws \Ixopay\Client\Exception\ClientException
     * @throws GeneralErrorException
     * @throws PaymentRequestException
     * @throws RateLimitException
     */
    public function getRequestDataForPayment(ShopOrder $order, string $locale): Result
    {
        $cardCredentials = $this->countries['ba']['card_payment'];

        $client = new Client($cardCredentials['username'], $cardCredentials['password'], $cardCredentials['apiKey'], $cardCredentials['sharedSecret']);

        $result = $client->debit($this->debit($order, $locale));

        if ($result->isSuccess() && $result->getReturnType() == Result::RETURN_TYPE_REDIRECT)
        {
            return $result;
        }

        $paymentException = new PaymentRequestException('generic_error');
        $paymentException->setResult($result);

        throw $paymentException;
    }

    private function customer(ShopOrder $order): Customer
    {
        $billingAddress = $order->getBillingAddress();

        $customer = new Customer();

        $customer->setFirstName($billingAddress->getFirstName());
        $customer->setLastName($billingAddress->getLastName());
        $customer->setBillingCountry('BA');
        $customer->setEmail($billingAddress->getEmail());
        $customer->setBillingAddress1($billingAddress->getAddress());
        $customer->setBillingCity($billingAddress->getCity());
        $customer->setBillingPostcode((string) $billingAddress->getZipCode());

        return $customer;
    }

    private function debit(ShopOrder $order, string $locale): Debit
    {
        $debit = new Debit();
        $debit->setMerchantTransactionId(uniqid((string) $order->getId()));
        $debit->setSuccessUrl($this->router->generate('site.checkout_completed_card_return.bank-art', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setCancelUrl($this->router->generate('site.checkout_page', ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setErrorUrl($this->router->generate('site.checkout_completed_card_return.bank-art', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setCallbackUrl($this->router->generate('site.checkout_completed_card_callback', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setAmount((string)($order->getTotal() + $order->getShippingPrice())/100);
        $debit->setCurrency('BAM');
        $debit->setCustomer($this->customer($order));

        $threeDSecureData = new ThreeDSecureData();
        $threeDSecureData->setThreeDSecure('MANDATORY');
        $debit->setThreeDSecureData($threeDSecureData);

        return $debit;
    }
}
