<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VerificationRequestStatus;
use App\Repository\VerificationRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=VerificationRequestRepository::class)
 * @ORM\Table(name="verification_requests")
 * @Vich\Uploadable()
 */
class VerificationRequest implements AuthoredEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="identity_documents", fileNameProperty="identityDocumentFileName")
     */
    private $identityDocumentFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identityDocumentFileName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $status = VerificationRequestStatus::VERIFICATION_REQUESTED;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rejectReason;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }

    public function setRejectReason(?string $rejectReason): self
    {
        $this->rejectReason = $rejectReason;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIdentityDocumentFileName()
    {
        return $this->identityDocumentFileName;
    }

    public function setIdentityDocumentFileName($identityDocumentFileName): self
    {
        $this->identityDocumentFileName = $identityDocumentFileName;

        return $this;
    }

    public function getIdentityDocumentFile(): ?File
    {
        return $this->identityDocumentFile;
    }

    public function setIdentityDocumentFile(?File $identityDocumentFile = null): self
    {
        $this->identityDocumentFile = $identityDocumentFile;

        if (null !== $identityDocumentFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
