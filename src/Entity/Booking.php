<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * cycle de vie / a different evenements de son cycle de on a relié des fonctions
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
// _______________________________Gestion de La date de creation et le prix du sejour___________
/**
 * Callback appelé a chaque fois qu'on crée une reservation
 * @ORM\PrePersist
 *
 * @return void
 */
    public function persist(){

        // si la date de creation dans la table est vide, alors ajouter la date d'aujourd'huit
        if(empty($this->createdAt)){
            $this->createdAt=new \DateTime();
        }

        // prix du sejour
        if(empty($this->amount)){
            // multiplier le prix de l'annonce par le nombre de jours du sejour 
            $prix=$this->ad->getPrice()*$this->getDuration();
            $this->amount=$prix;
        }
    }

     // a l'aide de la fonction "diff" determinons le nbre de jour entre la date de fin et celle du debut
    public function getDuration() {
       
        $dureeSejour=$this->endDate->diff($this->startDate);
        return $dureeSejour->days;
    }
// _________________________________________fin______________________________

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
