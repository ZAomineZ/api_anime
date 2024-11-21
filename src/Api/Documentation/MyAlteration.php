<?php

namespace App\Api\Documentation;

use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MyAlteration implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $normalizer;

    /**
     * MyAlteration constructor.
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|ArrayObject|bool|float|int|string|void|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var array $docs */
        $docs = $this->normalizer->normalize($object, $format, $context);

        // Add docs for path to api
        $pathGetCharacterBySlug = $docs['paths']['/api/characters/slug/{slug}']['get'];
        $customDocsGetCharacterBySlug = [
            'parameters' => [
                [
                    'name' => 'slug',
                    'in' => 'path',
                    'description' => 'Find your character with his slug.',
                    'type' => 'string',
                    'required' => true,
                    'example' => 'luffy'
                ]
            ],
            'responses' => [
                200 => [
                    'description' => 'This is your character',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Character-read.character'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/characters/slug/{slug}']['get'] = array_merge($pathGetCharacterBySlug, $customDocsGetCharacterBySlug);

        $pathGetCharacterByGenre = $docs['paths']['/api/characters/genre/{genre}']['get'];
        $customDocsGetCharacterByGenre = [
            'parameters' => [
                [
                    'name' => 'genre',
                    'in' => 'path',
                    'description' => "Find your character's gender.",
                    'type' => 'string',
                    'required' => true,
                    'example' => 'man|woman'
                ]
            ],
            'responses' => [
                200 => [
                    'description' => 'This is your character',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Character-read.character'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/characters/genre/{genre}']['get'] = array_merge($pathGetCharacterByGenre, $customDocsGetCharacterByGenre);

        $pathGetAnimesByTag = $docs['paths']['/api/animes/{tag}/tag']['get'];
        $customDocsGetAnimesByTag = [
            'parameters' => [
                [
                    'name' => 'tag',
                    'in' => 'path',
                    'description' => 'Find anime according to your tag.',
                    'type' => 'string',
                    'required' => true,
                    'example' => 'combat'
                ]
            ],
            'responses' => [
                200 => [
                    'description' => 'Here are the animes according to your tag',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Anime-read.character'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/animes/{tag}/tag']['get'] = array_merge($pathGetAnimesByTag, $customDocsGetAnimesByTag);

        $pathGetAnimesByAuthor = $docs['paths']['/api/animes/{author}/author']['get'];
        $customDocsGetAnimesByAuthor = [
            'parameters' => [
                [
                    'name' => 'author',
                    'in' => 'path',
                    'description' => 'Find anime by author.',
                    'type' => 'string',
                    'required' => true,
                    'example' => 'eiichiro-oda'
                ]
            ],
            'responses' => [
                200 => [
                    'description' => 'Here are the animes according to your author',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Anime-read.character'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/animes/{author}/author']['get'] = array_merge($pathGetAnimesByAuthor, $customDocsGetAnimesByAuthor);

        $pathGetAnimesByFirstBroadcast = $docs['paths']['/api/animes/{year}/firstBroadcast']['get'];
        $customDocsGetAnimesByFirstBroadcast = [
            'parameters' => [
                [
                    'name' => 'year',
                    'in' => 'path',
                    'description' => 'Find anime by year of creation.',
                    'type' => 'string',
                    'required' => true,
                    'example' => '2020'
                ]
            ],
            'responses' => [
                200 => [
                    'description' => 'Here are the anime by year of creation',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Anime-read.anime'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/animes/{year}/firstBroadcast']['get'] = array_merge($pathGetAnimesByFirstBroadcast, $customDocsGetAnimesByFirstBroadcast);

        $pathPostCharacterImage = $docs['paths']['/api/characters/{id}/image']['post'];
        $customDocPostCharacterImage = [
            'responses' => [
                200 => [
                    'description' => "Here's the image of the current character",
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#components/schemas/Anime-read.character_read.image'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $docs['paths']['/api/characters/{id}/image']['post'] = array_merge($pathPostCharacterImage, $customDocPostCharacterImage);

        // Sort the schemas and the endpoints, because that's nicer
        ksort($docs['components']['schemas']);
        ksort($docs['paths']);
        return $docs;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }
}
