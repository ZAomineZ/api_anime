<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

final class JwtDecorator implements OpenApiFactoryInterface
{

    /**
     * JwtDecorator constructor.
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(private OpenApiFactoryInterface $decorated)
    {}

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
                    'example' => 'Content content'
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
                    'description' => 'Your JWT Token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Your JWT Token to connect',
                requestBody: new RequestBody(
                    description: 'Generates a new JWT Token',
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
                    'example' => 'rootroot'
                ],
                'password_confirm' => [
                    'type' => 'string',
                    'example' => 'rootroot'
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
                    'description' => 'Your user created',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/UserResponse'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Create a user',
                requestBody: new RequestBody(
                    description: 'Create a user with the following fields',
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
        $schemas['LogoutResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string'
                ]
            ]
        ]);
        $pathItem = new PathItem(
            get: new Operation(
                operationId: 'getApiLogout',
                tags: ['Auth'],
                responses: [
                '200' => [
                    'description' => 'Your user is logged out',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/LogoutResponse'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Log out your user'
            )
        );
        $openApi->getPaths()->addPath('/api/logout', $pathItem);

        # Refresh Token route
        $schemas['CredentialsRefreshToken'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true
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
                    'description' => 'Your JWT Token and Refresh Token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token'
                            ]
                        ]
                    ]
                ]
            ],
                summary: 'Generate a JWT Token and a Refresh Token',
                requestBody: new RequestBody(
                    description: 'Refresh your token.',
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
