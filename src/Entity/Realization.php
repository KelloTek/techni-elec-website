<?php

namespace App\Entity;

use App\Repository\RealizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealizationRepository::class)]
class Realization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\ManyToMany(targetEntity: File::class, mappedBy: 'realizations')]
    private Collection $image;

    public function __construct()
    {
        $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(File $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->addRealization($this);
        }

        return $this;
    }

    public function removeImage(File $image): static
    {
        if ($this->image->removeElement($image)) {
            $image->removeRealization($this);
        }

        return $this;
    }
}
