<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//Validation de champs
use Symfony\Component\Validator\Constraints as Assert;
//Validation de l'entity elle meme
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * Anntation pour gerer le cycle de vie
 * @ORM\HasLifecycleCallbacks
 * le title et le slug doivent etre unique
 * @UniqueEntity(
 * fields={"title","slug"}
 * )
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * validation de champs "title" /contrainte sur sa longueur min & max 
     * @Assert\Length(min=10,max=225, minMessage=" L' introduction doit faire plus de 10 caracteres", maxMessage="Le Titre ne peux pas faire plus se 225 Carractere")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, minMessage="Le titre doit faire plus de 20 caracteres")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * validation de champs "introduction" /contrainte sur sa longueur min & max avec le message d'erreure
     * @Assert\Length(min=10, minMessage="Votre description ne peut pas faire moins de 100 caracteres")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url() 
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer") 
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    // ___________________on aimerait que les slugs soit generés automatiquement_________________________
        /**
         * permet d'initialiser le slug
         * @ORM\PrePersist
         * @ORM\PreUpdate
         * @return void
         */

        //  generation et enregistrement automatique du slug qui genere sans qu'on entre le champs
    public function creationdeSlug()
    {
        if(empty($this->slug)) {
            $slugify = new Slugify();
            // en remplacant par ("$slug=$slugify->slugify($title)") dans AppFixtures.php (Ligne 34-38-7-21)
            $this->slug=$slugify->slugify($this->title);
        }
    }
    // ________________________________________________
    
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;
 
        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
