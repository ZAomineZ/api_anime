<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TypeAnimeRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TypeAnimeRepository::class)
 */
#[ApiResource(
    collectionOperations: [
    'get' => [
        'access_control' => "is_granted('ROLE_USER')"
    ],
    'post' => [
        'access_control' => "is_granted('ROLE_ADMIN')"
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
    denormalizationContext: ['groups' => ['create:type_anime']],
    normalizationContext: ['groups' => ['read:type_anime']]
)]
class TypeAnime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:type_anime'])]
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
        Groups(['read:type_anime', 'read:anime', 'create:type_anime'])
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
        Groups(['read:type_anime', 'create:type_anime'])
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
            minMessage: 'This field must contain at least 3 characters',
            maxMessage: 'This field must contain a maximum of 255 characters'
        ),
        Groups(['read:type_anime', 'create:type_anime'])
    ]
    private ?string $content;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:type_anime'])]
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Anime::class, mappedBy="type_anime", cascade={"persist"})
     */
    private $animes;

    /**
     * TypeAnime constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->animes = new ArrayCollection();
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
     * @return Collection|Anime[]
     */
    public function getAnimes(): array|ArrayCollection|Collection
    {
        return $this->animes;
    }

    /**
     * @param Anime $anime
     * @return $this
     */
    public function addAnime(Anime $anime): self
    {
        if (!$this->animes->contains($anime)) {
            $this->animes[] = $anime;
            $anime->setTypeAnime($this);
        }

        return $this;
    }

    /**
     * @param Anime $anime
     * @return $this
     */
    public function removeAnime(Anime $anime): self
    {
        if ($this->animes->removeElement($anime)) {
            // set the owning side to null (unless already changed)
            if ($anime->getTypeAnime() === $this) {
                $anime->setTypeAnime(null);
            }
        }

        return $this;
    }
}
