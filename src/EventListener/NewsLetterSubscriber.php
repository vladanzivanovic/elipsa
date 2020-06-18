<?php

namespace App\EventListener;

use App\Entity\Email;
use App\Event\EmailEvent;
use App\Event\NewsLetterEvent;
use App\Helper\RandomCodeGenerator;
use App\Model\EmailModel;
use App\Repository\EmailRepository;
use App\Repository\NewsLetterRepository;
use App\Repository\SettingsRepository;
use DrewM\MailChimp\MailChimp;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

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
     * @param ParameterBagInterface $parameterBag
     * @param NewsLetterRepository  $newsLetterRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        NewsLetterRepository $newsLetterRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->newsLetterRepository = $newsLetterRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
       return [
           NewsLetterEvent::ADD_USER => [
               ['addToNewsLetter', 0],
           ],
       ];
    }

    /**
     * @param NewsLetterEvent $event
     */
    public function addToNewsLetter(NewsLetterEvent $event): void
    {
        $newsLetter = $event->getNewsLetter();
        $apiKey = $this->parameterBag->get('api_key');
        $listId = $this->parameterBag->get('list_id');

        $mailChimp = new MailChimp($apiKey);
        $result = $mailChimp->post('/lists/'.$listId.'/members', [
            'email_address' => $newsLetter->getEmail(),
            'status' => 'subscribed',
        ]);

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
}