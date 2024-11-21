<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * JwtAuthenticator constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContainerBagInterface $param
     * @param JWTEncoderInterface $JWTEncoder
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContainerBagInterface $param,
        private JWTEncoderInterface $JWTEncoder
    ) {}

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'Authentication Required',
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getCredentials(Request $request): string
    {
        return $request->headers->get('Authorization');
    }

    /**
     * @param string $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        try {
            return $this->user($credentials);
        } catch (Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    /**
     * @param $credentials
     * @return UserInterface
     * @throws JWTDecodeFailureException
     */
    protected function user($credentials): UserInterface
    {
        $credentials = str_replace('Bearer ', '', $credentials);
        $jwt = (array)$this->JWTEncoder->decode($credentials);

        /** @var UserInterface $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $jwt['username']]);
        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     * @throws JWTDecodeFailureException
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $userCredential = $this->user($credentials);
        return $userCredential->getUserIdentifier() === $user->getUserIdentifier();
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): void
    {
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
