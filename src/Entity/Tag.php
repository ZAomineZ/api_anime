<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
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
    denormalizationContext: ['groups' => ['create:tag']],
    normalizationContext: ['groups' => ['read:tag']]
)]
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:tag'])]
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
        Groups(['create:tag', 'read:tag', 'read:anime'])
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
        Groups(['create:tag', 'read:tag', 'read:anime'])
    ]
    private ?string $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Anime::class, mappedBy="tag")
     */
    #[Groups(['read:tag'])]
    private $animes;

    /**
     * Tag constructor.
     */
    #[Pure] public function __construct()
    {
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
     * @return Collection|Anime[]
     */
    public function getAnimes(): Collection
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
            $anime->addTag($this);
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
            $anime->removeTag($this);
        }

        return $this;
    }
}
