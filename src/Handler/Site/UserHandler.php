<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Settings;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Formatter\SettingsFormatter;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserHandler
{
    private ValidatorHelper $validator;

    private TranslatorInterface $translator;

    private SettingsRepository $settingsRepository;

    private EventDispatcherInterface $dispatcher;

    private UserRepository $userRepository;

    private UserPasswordHasherInterface $userPasswordHasher;

    private SettingsFormatter $settingsFormatter;

    public function __construct(
        ValidatorHelper $validator,
        TranslatorInterface $translator,
        SettingsRepository $settingsRepository,
        EventDispatcherInterface $dispatcher,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        SettingsFormatter $settingsFormatter
    ) {
        $this->validator = $validator;
        $this->translator = $translator;
        $this->settingsRepository = $settingsRepository;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->settingsFormatter = $settingsFormatter;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \Exception
     */
    public function save(User $user, ?string $group = null, bool $shouldUpdatePassword = false): void
    {
        if (null !== $group) {
            $errors = $this->validator->validate($user, null, $group);

            if ($errors->count() > 0) {
                throw new BadRequestHttpException(json_encode($this->validator->parseErrors($errors)));
            }
        }

        if (true === $shouldUpdatePassword) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        }

        if (null == $user->getId()) {
            $this->userRepository->persist($user);
        }

        $this->userRepository->flush();
    }
}
