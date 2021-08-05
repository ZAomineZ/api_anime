<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class SecurityController extends AbstractController
{

    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

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
                'minMessage' => 'Le champs de l\'username doit contenir au moins 3 caractères.',
                'maxMessage' => 'Le champs de l\'username doit contenir au maximum 255 caractères.'
            ]),
            'email' => new Length([
                'min' => 5,
                'max' => 255,
                'minMessage' => 'Le champs de l\'email doit contenir au moins 5 caractères.',
                'maxMessage' => 'Le champs de l\'email doit contenir au maximum 255 caractères.'
            ]),
            'password' => new Length([
                'min' => 5,
                'max' => 60,
                'minMessage' => 'Le champs du password doit contenir au moins 5 caractères.',
                'maxMessage' => 'Le champs du password doit contenir au maximum 60 caractères.'
            ]),
            'password_confirm' => new Length([
                'min' => 5,
                'max' => 60,
                'minMessage' => 'Le champs du password_confirm doit contenir au moins 5 caractères.',
                'maxMessage' => 'Le champs du password_confirm doit contenir au maximum 60 caractères.'
            ])
        ]);

        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(['error' => (string)$violations]);
        }

        $password = $data['password'] ?: null;
        $passwordConfirm = $data['password_confirm'] ?: null;
        if ($password !== $passwordConfirm) return new JsonResponse(['error' => 'Les mot de passe doivent être identiques.']);

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

    #[Route(path: '/api/logout', name: 'api_logout', methods: 'GET')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
