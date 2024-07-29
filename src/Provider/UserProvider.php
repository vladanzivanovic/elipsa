<?php

declare(strict_types=1);

namespace App\Provider;

use App\Handler\Site\UserHandler;
use App\Parser\Site\RegistrationRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserProvider implements OAuthAwareUserProviderInterface
{
    private RegistrationRequestParser $registrationRequestParser;

    private UserHandler $userHandler;

    public function __construct(
        RegistrationRequestParser $registrationRequestParser,
        UserHandler $userHandler
    ){
        $this->registrationRequestParser = $registrationRequestParser;
        $this->userHandler = $userHandler;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $user = $this->registrationRequestParser->parseFromSocial($response);

        $this->userHandler->save($user, 'UserSocial');

        return $user;
    }
}
