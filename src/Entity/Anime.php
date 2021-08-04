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
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
