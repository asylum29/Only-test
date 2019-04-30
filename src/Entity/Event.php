<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @Vich\Uploadable
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Заголовок новости не должен превышать {{ limit }} символов."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=10000)
     * @Assert\Length(
     *      max = 10000,
     *      maxMessage = "Текст новости не должен превышать {{ limit }} символов."
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "Максимальный размер изображения составляет 5 мегабайт.",
     *     mimeTypesMessage = "Необходимо загрузить изображение."
     * )
     * @Vich\UploadableField(mapping="event_image", fileNameProperty="imageName")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\IpAddress", mappedBy="event", orphanRemoval=true)
     */
    private $ipAddresses;

    public function __construct()
    {
        $this->ipAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return Collection|IpAddress[]
     */
    public function getIpAddresses(): Collection
    {
        return $this->ipAddresses;
    }

    public function addIpAddress(IpAddress $ipAddress): self
    {
        if (!$this->ipAddresses->contains($ipAddress)) {
            $this->ipAddresses[] = $ipAddress;
            $ipAddress->setEvent($this);
        }

        return $this;
    }

    public function removeIpAddress(IpAddress $ipAddress): self
    {
        if ($this->ipAddresses->contains($ipAddress)) {
            $this->ipAddresses->removeElement($ipAddress);
            // set the owning side to null (unless already changed)
            if ($ipAddress->getEvent() === $this) {
                $ipAddress->setEvent(null);
            }
        }

        return $this;
    }

    public function getRating(): int
    {
        $positive = $this
            ->getIpAddresses()
            ->filter(function(IpAddress $ipAddress) {
                return $ipAddress->getUp();
            })
            ->count()
        ;
        $negative = $this
            ->getIpAddresses()
            ->filter(function(IpAddress $ipAddress) {
                return !$ipAddress->getUp();
            })
            ->count()
        ;

        return $positive - $negative;
    }
}
