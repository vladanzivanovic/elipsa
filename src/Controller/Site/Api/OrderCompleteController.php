<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\OrderProduct;
use App\Entity\ShopOrder;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\OrderCompleteRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OrderCompleteController extends AbstractController
{
    /**
     * @var OrderCompleteRequestParser
     */
    private $requestParser;
    /**
     * @var OrderHandler
     */
    private $orderHandler;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @param OrderCompleteRequestParser $requestParser
     * @param OrderHandler               $orderHandler
     * @param ParameterBagInterface      $parameterBag
     * @param RouterInterface            $router
     * @param HttpClientInterface        $client
     */
    public function __construct(
        OrderCompleteRequestParser $requestParser,
        OrderHandler $orderHandler,
        ParameterBagInterface  $parameterBag,
        RouterInterface  $router,
        HttpClientInterface $client
    ) {
        $this->requestParser = $requestParser;
        $this->orderHandler = $orderHandler;
        $this->parameterBag = $parameterBag;
        $this->router = $router;
        $this->client = $client;
    }

    /**
     * @Route({
     *          "rs": "/api/order/complete",
     *          "en": "/api/order/complete"
     *      },
     *     name="site_api.complete_order",
     *     methods={"PUT"},
     *     options={"expose": true}
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function completeOrder(Request $request): JsonResponse
    {
        $locale = $request->getLocale();

        $this->validateHuman($request->request);

        $order = $this->requestParser->parse($request->request, $request->getSession()->get('order'));

        $this->orderHandler->save($order);

        return $this->json($this->prepareDataForIntesaPayment(
            $order,
            $request->request->getInt('total_amount'),
            $locale
        ));
    }

    private function prepareDataForIntesaPayment(ShopOrder $order, int $total, string $locale)
    {
        $localeIntesa = $locale === 'rs' ? 'sr' : $locale;

        $orgClientId = $this->parameterBag->get('intesa_merchant_id');
        $oid = $order->getId();
        $orgAmount = $total.'.00';
        $orgOkUrl = $this->router->generate('site.checkout_completed_successful', [], RouterInterface::ABSOLUTE_URL);
        $orgFailUrl = $this->router->generate('site.checkout_failed', [], RouterInterface::ABSOLUTE_URL);
        $cancelUrl = $this->router->generate('site.checkout_page', [], RouterInterface::ABSOLUTE_URL);
        $orgTransactionType = "PreAuth";
        $orgCurrency = "941";

        $clientId = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgClientId));
        $amount = str_replace("|", "\\|", str_replace("\\", "\\\\", number_format((float)$orgAmount, 2, '.', '')));
        $okUrl = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgOkUrl));
        $failUrl = str_replace("|", "\\|", str_replace("\\", "\\\\", $orgFailUrl));
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
            'lang' => $localeIntesa,
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
        foreach ($order->getOrderProducts()->getIterator() as $orderProduct) {

            $trans = $orderProduct->getByLocale($locale);

            $price = null !== $orderProduct->getDiscount() ? $orderProduct->getDiscount() : $orderProduct->getPrice();

            $total = $orderProduct->getQuantity() * $price;

            $data['ItemNumber'.$i] = $trans->getTitle();
            $data['ProductCode'.$i] = $orderProduct->getCode();
            $data['Qty'.$i] = $orderProduct->getQuantity();
            $data['Price'.$i] = $price.'.00';
            $data['Total'.$i] = $total.'.00';
        }

        return $data;
    }

    /**
     * @param ParameterBag $parameterBag
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function validateHuman(ParameterBag $parameterBag): void
    {
        $response = $this->client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->parameterBag->get('google_recaptcha_secret'),
                'response' => $parameterBag->get('recaptcha_response'),
            ]
        ]);

        $captchaResponse = json_decode($response->getContent(), true);

        if (
            true === $this->isCsrfTokenValid('order_complete', $parameterBag->get('_csrf_token')) &&
            true === $captchaResponse['success'] && $captchaResponse['score'] > 0.4 && $captchaResponse['action'] === 'complete_order'
        ) {
           return;
        }

        $this->createAccessDeniedException();
    }
}