<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
//Validation de l'entity elle meme, Ajouter des contraintes sur les champs du formulaire
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * Annotation pour gerer le cycle de vie
 * @ORM\HasLifecycleCallbacks
 * 
 * Ajouter les contraintes,on veux que "les users soit unique" avec la fonction "UniqueEntity" qui prend en parametre (les "field surlesquels on veux faire en sorte que xa soit unique )
 * le field qui nous permet de voir qu'un utilisateur doit etre unique c'est son "email"
 * 
 * @UniqueEntity(
 *  fields={"email"},
 * message="Un autre utilisateur s'est deja inscrit avec cette adresse mail, Veuillez ressayer avec un autre"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * le firstName ne doit pas etre null
     * @Assert\NotBlank(message="Veuillez renseigner votre prenom!")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * le lastName ne doit pas etre null
     *  @Assert\NotBlank(message="Veuillez renseigner votre nom de famille!")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * verifier la validité de l'email
     * @Assert\Email(message="Veuillez renseigner un email valide!")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez donner une Url valide pour votre avatar !")
     */
    private $picture;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    //On veux ajouter une confirmation de mot de pass,le probleme posé est que le champs
    // n'existe pas dans la table, mais doit apparaitre sur notre formulaire d'enregistrement vu que la verification se fait coté client,
    //Donc créons une nouvelle proprieté("passwordConfirm") dans l'entity ("user") sans toute fois le faire preceder des paramettre ("ORM et autre") 
    //vu que c'est pas un champs de la table ("user") ensuite ajoutons le fameux champs a notre formulaire ("RegistrationType.php")
    //Les annotation (ORM/colum) sont faites pour faire le lien entre les donnée qui sont dans ma class "user.php" et champs qui sont dans ma table
    /**
     *@Assert\EqualTo(propertyPath="password",message="Verifier que les deux mots de pass sont identiques !")
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     * validation de champs "introduction" /contrainte sur sa longueur min & ajouter le message d'erreure
     * @Assert\Length(min=10, minMessage="Votre introduction ne peut pas faire moins de 10 caracteres")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, minMessage="Votre description doit faire moins de 20 caracteres")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="author")
     */
    private $ads;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $userRoles;

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    // _____________________creeons une fonction [getFullName()] pour  simplifier l'ecriiture du [a.firstName && a.lastName] __________________________

        public function getFullName() {
            return "{$this->firstName} {$this->lastName}";
        }
    // ____________________________________

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
        $this->slug=$slugify->slugify($this->firstName.'  '.$this->lastName);
    }
}
// ____________________________________


    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }



    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * __________________________________________Roles des Utilisateurs_____________
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // voir le role de l'utilisateur de la table "Role" grace a la proprieté "userRole" de "user"/ xa ramene un tableau 
        // $roles = $this->userRoles->toArray();
        // dump($roles);
        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray();
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        // dump(array_unique($roles));
        // die();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->removeElement($userRole)) {
            $userRole->removeUser($this);
        }

        return $this;
    }
}
