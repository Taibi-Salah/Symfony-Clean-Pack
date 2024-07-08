<?php

namespace App\Security;

use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

class CustomUsernamePasswordFormAuthenticationListener extends UsernamePasswordFormAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        $username = $request->get($this->usernameParameter, '');
        $password = $request->get($this->passwordParameter, '');

        if (!is_string($username)) {
            throw new BadCredentialsException('The key "_username" must be a string.');
        }

        if ($username === null) {
            $username = '';
        }

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }
}
