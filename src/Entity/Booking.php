<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
     * le champ ad qui represente l'annonce a laquelle la reservation est liée
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention ! la date d'arrivée doit etre au bon format")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit etre ulterieur a la date d'aujourdh'huit !")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention ! la date de depart doit etre au bon format")
     * la proprieté "propertyPath" fais intervenir un autre champ qui va servir de comparaison (de superiorité grace a "GreaterThan")
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de depart ne peut etre anterieure a la date d'arrivée !")
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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    // ____________________Gestion de La date de creation de l'annonce et le prix du sejour______
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

    //______________________ a l'aide de la fonction "diff" determinons le nbre de jour entre la date de fin et celle du debut
    
    public function getDuration() {
       
            $dureeSejour=$this->endDate->diff($this->startDate);
            return $dureeSejour->days;
    }
    // ________________________________________D11-V12__Gestion de la disponibilité d'une annonce lors de la reservation_

    public function isBookabbleDates(){
        // recuperer les jours deja occupés et les comparer avec les jours choisis pour cette reservation
        // creer une fonction ("getNotAvailableDays") au sein de l'entity Ad
        // 1) Connaitre les dates impossibles pour l'annonces ("getNotAvailableDays()")
            # le champ "ad" qui represente l'annonce a laquelle la reservation est liée
        $notAvailableDays=$this->ad->getNotAvailableDays();
        // 2) comparer les dates choisies avec les dates impossibles
        #Date choisies
        $bookingDays=$this->getDays();
                                         #_________________

        // 2 Tableaux des chaines de caracteres de mes journées / Les reconvertir en chaines de caractere ("sous formes string") en date vu qu'ils etaient sous forme de ("objet dateTime") 
                    #factoriser pour eviter les repetitions
                    $formatDays=function($day){
                        return $day->format("Y-m-d");
                    };

        $days         = array_map($formatDays, $bookingDays);

        $notAvailable = array_map($formatDays,$notAvailableDays);
                                         #_________________
        foreach ($days as $day) {
            # On regarde pour chaque journée de mon sejour choisis, est-ce qu'elle est presente dans le tableau des jours impossible grace a la fonction 
            if (array_search($day,$notAvailable)!== false)return false;
        }
        return true;
    }
    // ________________________________Un tableau d'objets DateTime representant les jours de la reservation
    /**
     * Permet de recuperer un tableau des journées qui correspondent a ma reservation
     * 
     * @return array
     */
    public function getDays(){ 
        
        # Pour la reservation faites pour cette annonce(appartement)
        
            # Calculer les jours qui se trouvent entre la date d'arrivée et de depart grace a la fonction range()
            # tout en les convertissant en miliSecondes grace a la fonction getTimestamp()
            $resultat= range(
                $this->getStartDate()->getTimestamp(),
                $this->getEndDate()->getTimestamp(),
                24*60*60
            );

            $days=array_map(function($dayTimestamp){
                return new \DateTime(date("Y-m-d",$dayTimestamp));
            },$resultat);
            #
            // $getDays=array_merge($getDays, $days);
      
        return $days;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
