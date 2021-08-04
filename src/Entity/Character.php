<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\CharacterByGenre;
use App\Controller\CharacterBySlug;
use App\Repository\CharacterRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CharacterRepository::class)
 * @ORM\Table(name="`character`")
 */
#[
    ApiResource(
        collectionOperations: [
        'get',
        'post' => [
            'openapi_context' => [
                'summary' => 'Création d\'un personnage',
                'description' => 'Vous pouvez créer votre personnage d\'anime avec les champs indiqué !',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'slug' => ['type' => 'string'],
                                    'content' => ['type' => 'string'],
                                    'genre' => ['type' => 'string'],
                                    'age' => ['type' => 'int'],
                                    'height' => ['type' => 'int'],
                                    'anime' => ['type' => 'string']
                                ],
                                'example' => [
                                    'name' => 'Luffy',
                                    'slug' => 'luffy',
                                    'content' => 'Description de test...',
                                    'genre' => 'homme|femme',
                                    'age' => 20,
                                    'height' => 185,
                                    'anime' => '/api/{entity}/{param}'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'get_by_genre' => [
            'method' => 'GET',
            'path' => '/characters/genre/{genre}',
            'controller' => CharacterByGenre::class,
            'normalization_context' => ['groups' => ['read:character']],
            'read' => false
        ]
    ],
        itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:character']]
        ],
        'put',
        'delete',
        'get_by_slug' => [
            'method' => 'GET',
            'path' => '/characters/slug/{slug}',
            'controller' => CharacterBySlug::class,
            'normalization_context' => ['groups' => ['read:character']],
            'read' => false
        ]
    ],
        denormalizationContext: [
        'groups' => ['create:character']
    ],
        normalizationContext: [
        'groups' => ['read:character']
    ],
        paginationClientItemsPerPage: true,
        paginationItemsPerPage: 2
    ),
    ApiFilter(SearchFilter::class, properties: [
        'id' => 'exact',
        'slug' => 'exact',
        'name' => 'partial',
        'content' => 'partial'
    ]),
    ApiFilter(DateFilter::class, properties: ['createdAt' => DateFilter::EXCLUDE_NULL]),
    ApiFilter(OrderFilter::class, properties: ['id' => 'ASC'])
]
class Character
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:character'])]
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'Ce champs doit être requis'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Ce champs doit possédait au minimum 3 caractères',
            maxMessage: 'Ce champs doit possédait au maximum 255 caractères'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'Ce champs doit être requis'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Ce champs doit possédait au minimum 3 caractères',
            maxMessage: 'Ce champs doit possédait au maximum 255 caractères'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'Ce champs doit être requis'),
        Assert\Length(
            min: 15,
            max: 255,
            minMessage: 'Ce champs doit possédait au minimum 15 caractères',
            maxMessage: 'Ce champs doit possédait au maximum 255 caractères'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $content;

    /**
     * @ORM\Column(type="string", length=8)
     */
    #[
        Assert\Choice(['femme', 'homme'], message: 'Vous devez choisir entre "homme" ou "femme" pour ce champs.'),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $genre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Anime", inversedBy="characters", cascade={"persist"})
     */
    #[
        Groups(['read:character', 'create:character'])
    ]
    private ?Anime $anime;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:character'])]
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:character'])]
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:character'])]
    private ?DateTimeInterface $publishedAt;

    /**
     * @ORM\Column(type="integer")
     */
     #[Groups(['create:character', 'read:character'])]
    private ?int $age;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['create:character', 'read:character'])]
    private ?int $height;

    /**
     * Character constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->publishedAt = new DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTimeInterface $publishedAt
     * @return $this
     */
    public function setPublishedAt(DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Anime|null
     */
    public function getAnime(): ?Anime
    {
        return $this->anime;
    }

    /**
     * @param Anime|null $anime
     */
    public function setAnime(?Anime $anime): void
    {
        $this->anime = $anime;
    }

    /**
     * @return string|null
     */
    public function getGenre(): ?string
    {
        return $this->genre;
    }

    /**
     * @param string|null $genre
     */
    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return $this
     */
    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }
}
