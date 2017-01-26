<?php

namespace GenjioBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface
{
    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->headers->get('Genjio-API-Key');
        $username = $request->headers->get('Genjio-API-Username');

        if (!$apiKey) return;

        $token = new PreAuthenticatedToken('anon.', $apiKey, $providerKey);
        $token->setAttribute('username', $username);
        return $token;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();
        $username = $token->getAttribute('username');
        $user = $userProvider->loadUserByUsername($username);

        if (!$user) {
            throw new AuthenticationException('User does not exist');
        }

        if (!password_verify($apiKey, $user->getPassword())) {
            throw new BadCredentialsException('Invalid Token');
        }

        return new PreAuthenticatedToken($user, $apiKey, $providerKey, ['ROLE_API']);
    }
}
