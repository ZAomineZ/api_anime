<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class SecurityController extends AbstractController
{

    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $entityManager
     * @param Connection $connection
     * @param UserRepository $userRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Connection $connection,
        private UserRepository $userRepository,
        private TokenStorageInterface $tokenStorage
    )
    {}

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordEncoder
     * @return JsonResponse
     */
    #[Route(path: '/api/register', name: 'api_register', methods: 'POST')]
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $validator = Validation::createValidator();
        $constraint = new Collection([
            'username' => new Length([
                'min' => 3,
                'max' => 255,
                'minMessage' => 'The username field must contain at least 3 characters.',
                'maxMessage' => 'The username field must contain a maximum of 255 characters.'
            ]),
            'email' => new Length([
                'min' => 5,
                'max' => 255,
                'minMessage' => 'The email field must contain at least 5 characters.',
                'maxMessage' => 'The email field must contain a maximum of 255 characters.'
            ]),
            'password' => new Length([
                'min' => 5,
                'max' => 60,
                'minMessage' => 'The password field must contain at least 5 characters.',
                'maxMessage' => 'The password field must contain a maximum of 60 characters.'
            ]),
            'password_confirm' => new Length([
                'min' => 5,
                'max' => 60,
                'minMessage' => 'The password_confirm field must contain at least 5 characters.',
                'maxMessage' => 'The password_confirm field must contain a maximum of 60 characters.'
            ])
        ]);

        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(['error' => (string)$violations]);
        }

        $password = $data['password'] ?: null;
        $passwordConfirm = $data['password_confirm'] ?: null;
        if ($password !== $passwordConfirm) return new JsonResponse(['error' => 'Passwords must be identical.']);

        $user = new User();
        $user
            ->setUsername($data['username'] ?: null)
            ->setEmail($data['email'] ?: null)
            ->setPassword($passwordEncoder->hashPassword($user, $password))
            ->setRoles(['ROLE_USER']);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        return new JsonResponse([
            'username' => $user->getUserIdentifier(),
            'email' => $user->getEmail(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }


    /**
     * @return JsonResponse
     * @throws Exception
     */
    #[Route(path: '/api/logout', name: 'api_logout', methods: 'GET')]
    public function logout(): JsonResponse
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = $this->tokenStorage->getToken()->getUser();
        if (is_null($authenticatedUser)) return new JsonResponse(['error' => 'This name field already exists in this entity.']);

        // Delete last refresh_token find in the database
        $this->connection->executeStatement(sprintf(
            'DELETE FROM refresh_tokens WHERE username = "%s"'
            , $authenticatedUser->getUserIdentifier()));
        $this->tokenStorage->setToken(null);
        return new JsonResponse(['message' => 'You are now logged out']);
    }
}
