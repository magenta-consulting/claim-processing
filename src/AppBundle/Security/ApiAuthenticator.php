<?php
/**
 * Created by PhpStorm.
 * User: mikedu
 * Date: 18/08/2016
 * Time: 18:04
 */
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $container;

    public function __construct(EntityManager $em,ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if (!$username = $request->headers->get('x-username')) {
            return;
        }
        if (!$password = $request->headers->get('x-password')) {
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        return array(
            'username' => $username,
            'password' => $password,
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];

        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $this->em->getRepository('AppBundle:User')
            ->findOneBy(array('username' => $username));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);

        if (!($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt()))) {
            throw new AuthenticationException(
                'AUTHENTICATION_FAILED.', 401
            );
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}