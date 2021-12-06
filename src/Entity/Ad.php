<?php

namespace App\Entity;

use App\Entity\Booking;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
//Validation de champs
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
//Validation de l'entity elle meme
// use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * Anntation pour gerer le cycle de vie (prepersite et autre)
 * @ORM\HasLifecycleCallbacks()
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
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
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

    /**
     * ajouter 'cascadeleRemove' vu que j'arrivais pas a le suprimer
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad",cascade={"remove"})
     */
    private $bookings;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
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
    // ________________D11-V12_________________Obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce_______________
    /**
     * Un tableau d'objets DateTime representant les jours d'occupation de l'appartement / booking.php
     *
     * @return array
     */
    public function getNotAvailableDays(){
        $notAvailableDays=[];
        # Pour chaque reservations deja faites pour cette annonce(appartement)
        # le champ "bookings" qui represente la reservation a laquelle l'annonce est liée 
        foreach ($this->bookings as $booking) {
            # Calculer les jours qui se trouvent entre la date d'arrivée et de depart grace a la fonction range()
            # tout en les convertissant en miliSecondes grace a la fonction getTimestamp()
            $resultat= range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24 * 60 * 60
            );
            $days=array_map(function($dayTimestamp){
                return new \DateTime(date("Y-m-d",$dayTimestamp));
            },$resultat);
            #
            $notAvailableDays=array_merge($notAvailableDays, $days);
        }
        return $notAvailableDays;
    }
    // ______________________________fin__________________
    
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

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }
}
