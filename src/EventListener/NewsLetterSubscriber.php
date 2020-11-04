<?php

namespace App\EventListener;

use App\Event\NewsLetterEvent;
use App\Repository\NewsLetterRepository;
use DrewM\MailChimp\MailChimp;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NewsLetterSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var NewsLetterRepository
     */
    private $newsLetterRepository;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $listId;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param NewsLetterRepository  $newsLetterRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        NewsLetterRepository $newsLetterRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->newsLetterRepository = $newsLetterRepository;

        $this->apiKey = $this->parameterBag->get('api_key');
        $this->listId = $this->parameterBag->get('list_id');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
       return [
           NewsLetterEvent::ADD_USER => [
               ['addUserToNewsLetter', 0],
           ],
           NewsLetterEvent::UPDATE_USER => [
               ['updateUserMailChimpData', 0],
           ],
       ];
    }

    /**
     * @param NewsLetterEvent $event
     *
     * @throws \Exception
     */
    public function addUserToNewsLetter(NewsLetterEvent $event): void
    {
        $newsLetter = $event->getNewsLetter();
        $loyalty = $event->getLoyalty();

        $userRequestInfo = [];

        $mailChimp = new MailChimp($this->apiKey);

        $request = [
            'email_address' => $newsLetter->getEmail(),
            'status' => 'subscribed',
        ];

        if (null !== $loyalty) {
            $userRequestInfo = [
                'merge_fields' => [
                    'FNAME' => $loyalty->getFirstName(),
                    'LNAME' => $loyalty->getLastName(),
                    'PHONE' => $loyalty->getMobilePhone(),
                ],
            ];
        }

        $result = $mailChimp->post('/lists/'.$this->listId.'/members', $request + $userRequestInfo);

        if (false === $mailChimp->success()) {
            $newsLetter->setLastError($mailChimp->getLastError());
            $this->newsLetterRepository->flush();

            throw new BadRequestHttpException($mailChimp->getLastError());
        }

        $newsLetter->setChimpId($result['id'])
            ->setLinks($result['_links'])
            ->setStatus($result['status']);

        $this->newsLetterRepository->flush();
    }

    /**
     * @param NewsLetterEvent $event
     *
     * @throws \Exception
     */
    public function updateUserMailChimpData(NewsLetterEvent $event)
    {
        $loyalty = $event->getLoyalty();
        $newsLetter = $this->newsLetterRepository->findOneBy(['email' => $loyalty->getEmail()]);

        $mailChimp = new MailChimp($this->apiKey);
        $result = $mailChimp->patch('/lists/'.$this->listId.'/members/'.$newsLetter->getChimpId(), [
            'merge_fields' => [
                'FNAME' => $loyalty->getFirstName(),
                'LNAME' => $loyalty->getLastName(),
                'PHONE' => $loyalty->getMobilePhone(),
            ],
        ]);

        if (false === $mailChimp->success()) {
            $newsLetter->setLastError($mailChimp->getLastError());
            $this->newsLetterRepository->flush();

            throw new BadRequestHttpException($mailChimp->getLastError());
        }

    }
}