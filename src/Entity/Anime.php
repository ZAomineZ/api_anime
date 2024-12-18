<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\AnimeByAuthor;
use App\Controller\AnimeByTag;
use App\Controller\AnimeByYearToFirstBroadcast;
use App\Repository\AnimeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnimeRepository::class)
 * @ORM\Table(name="`anime`")
 */
#[ApiResource(
    collectionOperations: [
    'get' => [
        'access_control' => "is_granted('ROLE_USER')"
    ],
    'post' => [
        'access_control' => "is_granted('ROLE_ADMIN')",
        'openapi_context' => [
            'summary' => 'Creating an anime',
            'description' => 'You can create your anime with the fields indicated !',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'slug' => ['type' => 'string'],
                                'content' => ['type' => 'string'],
                                'type_anime' => ['type' => 'string'],
                                'tag' => [
                                    'type' => 'string',
                                    'items' => [
                                        'type' => 'string',
                                        'example' => '/api/tags/{id}'
                                    ]
                                ],
                                'author' => ['type' => 'string'],
                                'first_broadcast' => ['type' => 'string'],
                                'episodes' => ['type' => 'string']
                            ],
                            'example' => [
                                'name' => 'One piece',
                                'slug' => 'one-piece',
                                'content' => 'Content content...',
                                'type_anime' => '/api/type_animes/{id}',
                                'tag' => ['/api/tag/{id}'],
                                'author' => '/api/author/{id}',
                                'first_broadcast' => '2020-08-15',
                                'episodes' => 24
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'get_by_tag' => [
        'method' => 'GET',
        'access_control' => "is_granted('ROLE_USER')",
        'path' => '/animes/{tag}/tag',
        'controller' => AnimeByTag::class,
        'normalization_context' => ['groups' => ['read:anime']],
        'read' => false
    ],
    'get_by_author' => [
        'method' => 'GET',
        'access_control' => "is_granted('ROLE_USER')",
        'path' => '/animes/{author}/author',
        'controller' => AnimeByAuthor::class,
        'normalization_context' => ['groups' => ['read:anime']],
        'read' => false
    ],
    'get_by_firstBroadcast' => [
        'method' => 'GET',
        'access_control' => "is_granted('ROLE_USER')",
        'path' => '/animes/{year}/firstBroadcast',
        'controller' => AnimeByYearToFirstBroadcast::class,
        'normalization_context' => ['groups' => ['read:anime']],
        'read' => false
    ]
],
    itemOperations: [
        'get' => [
            'access_control' => "is_granted('ROLE_USER')"
        ],
        'put' => [
            'access_control' => "is_granted('ROLE_ADMIN')"
        ],
        'delete' => [
            'access_control' => "is_granted('ROLE_ADMIN')"
        ]
    ],
    denormalizationContext: [
    'groups' => ['create:anime']
],
    normalizationContext: [
        'groups' => ['read:anime']
    ]
),
    ApiFilter(OrderFilter::class, properties: ['episodes' => 'ASC'])
]
class Anime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:anime'])]
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
        Groups(['create:anime', 'read:anime', 'read:character'])
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
        Groups(['create:anime', 'read:anime'])
    ]
    private ?string $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Assert\Length(
            min: 15,
            max: 255,
            minMessage: 'This field must contain at least 15 characters',
            maxMessage: 'This field must contain a maximum of 255 characters'
        ),
        Groups(['create:anime', 'read:anime'])
    ]
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeAnime", inversedBy="animes", cascade={"persist"})
     */
    #[
        Groups(['create:anime', 'read:anime', 'read:character'])
    ]
    private ?TypeAnime $type_anime = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Character", mappedBy="character", cascade={"persist"})
     */
    #[Groups(['read:anime'])]
    private $characters;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="animes")
     */
    #[Groups(['create:anime', 'read:anime'])]
    private $tag;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="animes", cascade={"persist"})
     */
    #[Groups(['create:anime', 'read:anime'])]
    private ?Author $author;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Groups(['create:anime', 'read:anime']),
        SerializedName('first_broadcast')
    ]
    private ?DateTime $firstBroadcast;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    #[
        Assert\NotBlank(message: 'This field must be required'),
        Assert\Positive(message: 'This field must be a positive number'),
        Groups(['create:anime', 'read:anime'])
    ]
    private ?string $episodes;

    /**
     * Anime constructor.
     */
    #[Pure] public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->tag = new ArrayCollection();
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
     * @return TypeAnime|null
     */
    public function getTypeAnime(): ?TypeAnime
    {
        return $this->type_anime;
    }

    /**
     * @param TypeAnime|null $type_anime
     * @return $this
     */
    public function setTypeAnime(?TypeAnime $type_anime): self
    {
        $this->type_anime = $type_anime;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function removeTag(Tag $tag): self
    {
        $this->tag->removeElement($tag);

        return $this;
    }

    /**
     * @return Author|null
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author|null $author
     * @return $this
     */
    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFirstBroadcast(): ?DateTime
    {
        return $this->firstBroadcast;
    }

    /**
     * @param DateTime $first_broadcast
     * @return $this
     */
    public function setFirstBroadcast(DateTime $first_broadcast): self
    {
        $this->firstBroadcast = $first_broadcast;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEpisodes(): ?string
    {
        return $this->episodes;
    }

    /**
     * @param string $episodes
     * @return $this
     */
    public function setEpisodes(string $episodes): self
    {
        $this->episodes = $episodes;

        return $this;
    }
}
