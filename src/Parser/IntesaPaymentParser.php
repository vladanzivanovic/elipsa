<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ShopOrder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IntesaPaymentParser
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param HttpClientInterface   $client
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        HttpClientInterface $client
    ) {

        $this->parameterBag = $parameterBag;
        $this->client = $client;
    }

    /**
     * @param ShopOrder $order
     * @param string    $type
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return void
     */
    public function parse(ShopOrder $order, string $type): void
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><CC5Request></CC5Request>');

        $xml->addChild('Name', $this->parameterBag->get('intesa_user_name'));
        $xml->addChild('Password', $this->parameterBag->get('intesa_password'));
        $xml->addChild('ClientId', $this->parameterBag->get('intesa_merchant_id'));
        $xml->addChild('Type', $type);
        $xml->addChild('OrderId', (string) $order->getToken());

        $response = $this->client->request(
            'POST',
            $this->parameterBag->get('intesa_api_gateway'),
            [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=UTF8',
                ],
                'body' => $xml->asXML(),
            ]
        );

        $responseArray = $this->parseXmlReportToArray(simplexml_load_string($response->getContent()));

        if ($responseArray['Response'] === 'Approved') {
            $order->setStatus(ShopOrder::CARD_STATUS_MAPPER[$type]);
            $order->setTransactionData(array_merge($order->getTransactionData(), [$type => $responseArray]));

            return;
        }

        throw new BadRequestHttpException($responseArray['ErrMsg']);
    }

    /**
     * @param \SimpleXMLElement $simpleXMLElement
     *
     * @return array<int, array<string, string>>
     */
    private function parseXmlReportToArray(\SimpleXMLElement $simpleXMLElement): array
    {
        $arrayData = [];
//        dd((array) $xml, $xmlString);
        foreach ((array) $simpleXMLElement as $name => $item) {
            if ($item instanceof \SimpleXMLElement) {
                $childArray = $this->parseXmlReportToArray($item);
                $arrayData[$name] = count($childArray) > 0 ? $childArray : null;

                continue;
            }
            $arrayData[$name] = $item;
        }

        return $arrayData;
    }
}