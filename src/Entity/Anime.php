<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AnimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnimeRepository::class)
 * @ORM\Table(name="`anime`")
 */
#[ApiResource(
    collectionOperations: [
    'get',
    'post' => [
        'openapi_context' => [
            'summary' => 'Création d\'un anime',
            'description' => 'Vous pouvez créer votre anime avec les champs indiqué !',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'slug' => ['type' => 'string'],
                                'content' => ['type' => 'string'],
                                'type_anime' => ['type' => 'string']
                            ],
                            'example' => [
                                'name' => 'One piece',
                                'slug' => 'one-piece',
                                'content' => 'Description de test...',
                                'type_anime' => '/api/type_animes/{id}'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
],
    itemOperations: ['get', 'put', 'delete'],
    denormalizationContext: [
    'groups' => ['create:anime']
],
    normalizationContext: [
        'groups' => ['read:anime']
    ]
)]
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
        Assert\NotBlank(message: 'Ce champs doit être requis'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Ce champs doit possédait au minimum 3 caractères',
            maxMessage: 'Ce champs doit possédait au maximum 255 caractères'
        ),
        Groups(['create:anime', 'read:anime', 'read:character'])
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
        Groups(['create:anime', 'read:anime'])
    ]
    private ?string $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[
        Assert\NotBlank(message: 'Ce champs doit être requis'),
        Assert\Length(
            min: 15,
            max: 255,
            minMessage: 'Ce champs doit possédait au minimum 15 caractères',
            maxMessage: 'Ce champs doit possédait au maximum 255 caractères'
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
     * Anime constructor.
     */
    #[Pure] public function __construct()
    {
        $this->characters = new ArrayCollection();
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
}
