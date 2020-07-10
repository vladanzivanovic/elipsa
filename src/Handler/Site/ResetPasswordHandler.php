<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Helper\ConstantsHelper;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\OrderProductRepository;
use App\Repository\SettingsRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param ValidatorHelper              $validator
     * @param TranslatorInterface          $translator
     * @param SettingsRepository           $settingsRepository
     * @param EventDispatcherInterface     $dispatcher
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        ValidatorHelper $validator,
        TranslatorInterface $translator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->validator = $validator;
        $this->translator = $translator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User   $user
     * @param string $locale
     * @param string $group
     * @param bool   $shouldSendEmail
     * @param bool   $shouldUpdatePassword
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function askForResetPassword(User $user, string $locale): void
    {
        $this->userRepository->flush();

        $emailModelCustomer = $this->prepareEmail($user, $locale);
        $event = new EmailEvent($emailModelCustomer);
        $this->dispatcher->dispatch($event, EmailEvent::SEND_EMAIL);
    }

    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetPassword(User $user): void
    {
        $errors = $this->validator->validate($user, null, "ResetPasswordUser");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

        $this->userRepository->flush();
    }

    /**
     * @param User   $user
     * @param string $locale
     *
     * @return EmailModel
     */
    private function prepareEmail(User $user, string $locale): EmailModel
    {
        $settings = $this->getSettings();

        $model = new EmailModel();
        $model->setScript(EmailModel::SCRIPT_USER_RESET_PASSWORD);
        $model->setTemplate('resetPassword');
        $model->setTo($user->getEmail());
        $model->setToName($user->getFirstName().' '.$user->getLastName());
        $model->setSubject($this->translator->trans('email.reset_password.title', ['%siteName%' => $settings['SITE_NAME']]));
        $model->setFrom($settings['MAIN_EMAIL']);
        $model->setFromName($settings['SITE_NAME']);
        $model->setReplyTo($settings['MAIN_EMAIL']);
        $model->setReplyToName($settings['SITE_NAME']);
        $model->setTemplateData([
            'locale' => $locale,
            'token' => $user->getResetToken(),
            'siteName' => $settings['SITE_NAME'],
        ]);

        return $model;
    }

    /**
     * @return array
     */
    private function getSettings(): array
    {
        $settings = $this->settingsRepository->getSettingsForUserRegistrationEmail();
        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting['slug']] = $setting['value'];
        }

        return $formatted;
    }
}