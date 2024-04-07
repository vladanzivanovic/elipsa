<?php

namespace App\EventListener;

use App\Event\NewsLetterEvent;
use App\Repository\NewsLetterRepository;
use DrewM\MailChimp\MailChimp;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NewsLetterSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $parameterBag;

    private NewsLetterRepository $newsLetterRepository;

    private TranslatorInterface $translator;

    private string $apiKey;

    private string $listId;

    public function __construct(
        ParameterBagInterface $parameterBag,
        NewsLetterRepository $newsLetterRepository,
        TranslatorInterface $translator
    ) {
        $this->parameterBag = $parameterBag;
        $this->newsLetterRepository = $newsLetterRepository;

        $this->apiKey = $this->parameterBag->get('api_key');
        $this->listId = $this->parameterBag->get('list_id');
        $this->translator = $translator;
    }

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

        $mergeFields = [
            'LANGUAGE' => $newsLetter->getLocale(),
            'GENDER' => $this->translator->trans($newsLetter->getGender(), [], null, $newsLetter->getLocale()),
        ];

        if ($loyalty instanceof \App\Entity\Loyalty) {
            $mergeFields['FNAME'] = $loyalty->getFirstName();
            $mergeFields['LNAME'] = $loyalty->getLastName();
            $mergeFields['PHONE'] = $loyalty->getMobilePhone();
        }

        $userRequestInfo = ['merge_fields' => $mergeFields];

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
     * @throws \Exception
     */
    public function updateUserMailChimpData(NewsLetterEvent $event): void
    {
        $loyalty = $event->getLoyalty();
        $newsLetter = $this->newsLetterRepository->findOneBy(['email' => $loyalty->getEmail()]);

        $mailChimp = new MailChimp($this->apiKey);
        $mailChimp->patch('/lists/'.$this->listId.'/members/'.$newsLetter->getChimpId(), [
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
