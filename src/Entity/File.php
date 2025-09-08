<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $originalName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $size = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $path = null;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'file')]
    private Collection $documents;

    /**
     * @var Collection<int, Request>
     */
    #[ORM\ManyToMany(targetEntity: Request::class, inversedBy: 'file')]
    private Collection $requests;

    /**
     * @var Collection<int, Certification>
     */
    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy: 'image')]
    private Collection $certifications;

    /**
     * @var Collection<int, Expertise>
     */
    #[ORM\OneToMany(targetEntity: Expertise::class, mappedBy: 'image')]
    private Collection $expertises;

    /**
     * @var Collection<int, Realization>
     */
    #[ORM\ManyToMany(targetEntity: Realization::class, inversedBy: 'image')]
    private Collection $realizations;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->expertises = new ArrayCollection();
        $this->realizations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path= $path;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setFile($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getFile() === $this) {
                $document->setFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Request>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): static
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
        }

        return $this;
    }

    public function removeRequest(Request $request): static
    {
        $this->requests->removeElement($request);

        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setImage($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getImage() === $this) {
                $certification->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expertise>
     */
    public function getExpertises(): Collection
    {
        return $this->expertises;
    }

    public function addExpertise(Expertise $expertise): static
    {
        if (!$this->expertises->contains($expertise)) {
            $this->expertises->add($expertise);
            $expertise->setImage($this);
        }

        return $this;
    }

    public function removeExpertise(Expertise $expertise): static
    {
        if ($this->expertises->removeElement($expertise)) {
            // set the owning side to null (unless already changed)
            if ($expertise->getImage() === $this) {
                $expertise->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Realization>
     */
    public function getRealizations(): Collection
    {
        return $this->realizations;
    }

    public function addRealization(Realization $realization): static
    {
        if (!$this->realizations->contains($realization)) {
            $this->realizations->add($realization);
        }

        return $this;
    }

    public function removeRealization(Realization $realization): static
    {
        $this->realizations->removeElement($realization);

        return $this;
    }
}
