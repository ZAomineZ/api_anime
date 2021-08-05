<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

final class JwtDecorator implements OpenApiFactoryInterface
{

    /**
     * JwtDecorator constructor.
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * @param array $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();
        $paths = $openApi->getPaths();

        # Login route
        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true
                ]
            ]
        ]);
        $schemas['CredentialsLogin'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'johndoe@test.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'testdetest'
                ]
            ]
        ]);
        $pathItemLogin = new PathItem(
            ref: 'JWT Token',
            post: new Operation(
                operationId: 'postCredentialsLoginItem',
                tags: ['Auth'],
                responses: [
                '200' => [
                    'description' => 'Votre JWT Token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Votre JWT Token pour vous connecter',
                requestBody: new RequestBody(
                    description: 'Génère un nouveau JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/CredentialsLogin'
                            ]
                        ]
                    ])
                )
            )
        );
        $paths->addPath('/api/login', $pathItemLogin);

        # Register Route
        $schemas['UserResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
                'email' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
                'createdAt' => [
                    'type' => 'string',
                    'readOnly' => true
                ]
            ]
        ]);
        $schemas['CredentialsRegister'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'toto'
                ],
                'email' => [
                    'type' => 'string',
                    'example' => 'toto@test.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'testdetest'
                ],
                'password_confirm' => [
                    'type' => 'string',
                    'example' => 'testdetest'
                ]
            ]
        ]);
        $pathItemRegister = new PathItem(
            ref: 'Register User',
            post: new Operation(
                operationId: 'postCredentialsRegisterItem',
                tags: ['Auth'],
                responses: [
                '200' => [
                    'description' => 'Votre utilisateur créé',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/UserResponse'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Créer un utilisateur',
                requestBody: new RequestBody(
                    description: 'Créer un utilisateur avec les champs suivants',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/CredentialsRegister'
                            ]
                        ]
                    ])
                )
            )
        );
        $paths->addPath('/api/register', $pathItemRegister);

        # Logout route
        $pathItem = new PathItem(
            get: new Operation(
                operationId: 'getApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => []
                ],
                summary: 'Déconnectez-vous'
            )
        );
        $openApi->getPaths()->addPath('/api/logout', $pathItem);

        # Refresh Token route
        $schemas['CredentialsRefreshToken'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string'
                ]
            ]
        ]);
        $pathItemRefreshToken = new PathItem(
            ref: 'JWT Token et le Refresh Token',
            post: new Operation(
                operationId: 'postCredentialsRefreshTokenItem',
                tags: ['Auth'],
                responses: [
                '200' => [
                    'description' => 'Votre JWT Token et le Refresh Token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Génèrer un JWT Token et un Refresh Token',
                requestBody: new RequestBody(
                    description: 'Refresh votre token.',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/CredentialsRefreshToken'
                            ]
                        ]
                    ])
                )
            )
        );
        $openApi->getPaths()->addPath('/api/refresh_token', $pathItemRefreshToken);

        return $openApi;
    }
}
