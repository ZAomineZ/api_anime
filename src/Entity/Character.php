<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\CharacterByGenre;
use App\Controller\CharacterBySlug;
use App\Controller\Media\CreateMediaCharacter;
use App\Repository\CharacterRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CharacterRepository::class)
 * @ORM\Table(name="`character`")
 * @Vich\Uploadable()
 */
#[
    ApiResource(
        collectionOperations: [
        'get' => [
            'access_control' => "is_granted('ROLE_USER')"
        ],
        'post' => [
            'access_control' => "is_granted('ROLE_ADMIN')",
            'openapi_context' => [
                'summary' => 'Creating a character',
                'description' => 'You can create your anime character with the fields indicated !',
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
                                    'content' => 'Content content...',
                                    'genre' => 'man|woman',
                                    'age' => 20,
                                    'height' => 185,
                                    'anime' => '/api/animes/{id}'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'post_image' => [
            'access_control' => "is_granted('ROLE_ADMIN')",
            'method' => 'POST',
            'path' => '/characters/{id}/image',
            'deserialize' => false,
            'read' => false,
            'controller' => CreateMediaCharacter::class,
            'validation_groups' => ['media:character_object:create'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'get_by_genre' => [
            'access_control' => "is_granted('ROLE_USER')",
            'method' => 'GET',
            'path' => '/characters/gender/{gender}',
            'controller' => CharacterByGenre::class,
            'normalization_context' => ['groups' => ['read:character']],
            'read' => false
        ]
    ],
        itemOperations: [
        'get' => [
            'access_control' => "is_granted('ROLE_USER')",
            'normalization_context' => ['groups' => ['read:character', 'read:image']]
        ],
        'put' => [
            'access_control' => "is_granted('ROLE_ADMIN')"
        ],
        'delete' => [
            'access_control' => "is_granted('ROLE_ADMIN')"
        ],
        'get_by_slug' => [
            'method' => 'GET',
            'access_control' => "is_granted('ROLE_USER')",
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
    #[Groups(['read:character', 'read:image'])]
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'This field must contain at least 3 characters',
            maxMessage: 'This field must contain a maximum of 255 characters'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'This field must contain at least 3 characters',
            maxMessage: 'This field must contain a maximum of 255 characters'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Assert\Length(
            min: 15,
            max: 255,
            minMessage: 'This field must contain at least 15 characters',
            maxMessage: 'This field must contain a maximum of 255 characters'
        ),
        Groups(['read:character', 'create:character'])
    ]
    private ?string $content;

    /**
     * @ORM\Column(type="string", length=8)
     */
    #[
        Assert\Choice(['woman', 'man'], message: 'You must choose between “man” or “woman” for this field.'),
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
     * @var File|null
     *
     * @Vich\UploadableField(mapping="character_object", fileNameProperty="filePath")
     */
    #[
        Groups(['create:character']),
        Assert\NotNull(groups: ['media:character_object:create'])
    ]
    private ?File $file = null;

    /**
     * @var string|null
     */
    private ?string $filePath = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:character', 'read:image'])]
    private ?string $fileUrl = null;

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

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return Character
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     * @return Character
     */
    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * @param string|null $fileUrl
     * @return Character
     */
    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }
}
