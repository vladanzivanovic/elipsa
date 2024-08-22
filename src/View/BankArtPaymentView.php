<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use Ixopay\Client\Client;
use Ixopay\Client\Data\Customer;
use Ixopay\Client\Transaction\Debit;
use Ixopay\Client\Transaction\Result;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class BankArtPaymentView
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {}

    public function view(ShopOrder $order, string $locale)
    {
        $client = new Client('API3004051002ELIPSAMP', 'Elipsabih123!', '3004051002IB350030-SIM', 'a16daba95307e28560bdb34407e4285e30b8f9e20f5c348728');

//        $customer = $this->setCustomer($order);

        // define your unique transaction ID, e.g.
//        $merchantTransactionId = uniqid('myId', true) . '-' . date('YmdHis');

//        $debit = new Debit();
//        $debit->setMerchantTransactionId($merchantTransactionId)
//            ->setSuccessUrl($this->router->generate('site.checkout_completed_successful', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL))
//            ->setCancelUrl($this->router->generate('site.checkout_failed', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL))
//            ->setCallbackUrl($this->router->generate('site.checkout_completed_successful', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL))
//            ->setAmount(10.00)
//            ->setCurrency('BAM')
//            ->setCustomer($customer);

// send the transaction
        $result = $client->debit($this->debit($order, $locale));

// handle the result
        if ($result->isSuccess()) {

            // store the uuid you receive from the gateway for future references
            $gatewayReferenceId = $result->getUuid();

            // handle result based on it's returnType
            if ($result->getReturnType() == Result::RETURN_TYPE_ERROR) {

                // read errors on error handling
                $errors = $result->getErrors();

                // handle the error
                // e.g. cancelCart();

            } elseif ($result->getReturnType() == Result::RETURN_TYPE_REDIRECT) {

                return ['redirect_url' => $result->getRedirectUrl()];
                // redirect the user

            } elseif ($result->getReturnType() == Result::RETURN_TYPE_PENDING) {

                // payment is pending: wait for callback to complete

                // handle pending
                // e.g. setCartToPending();

            } elseif ($result->getReturnType() == Result::RETURN_TYPE_FINISHED) {

                //payment is finished, update your cart/payment transaction
                // e.g. finishCart();
            }
        }
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
        $merchantTransactionId = uniqid('myId', true) . '-' . date('YmdHis');

        $debit = new Debit();
        $debit->setMerchantTransactionId($merchantTransactionId);
        $debit->setSuccessUrl($this->router->generate('site.checkout_completed_successful', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setCancelUrl($this->router->generate('site.checkout_failed', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setCallbackUrl($this->router->generate('site.checkout_completed_card_callback', ['_locale' => $locale, 'token' => $order->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $debit->setAmount((string)($order->getTotal() + $order->getShippingPrice())/100);
        $debit->setCurrency('BAM');
        $debit->setCustomer($this->customer($order));

        return $debit;
    }
}
