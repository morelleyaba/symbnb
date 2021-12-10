<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{
   
    private $oldPassword;

    /**
     * @Assert\Length(min=8, minMessage="Votre Mot de pass doit faire moins de 8 caracteres")
     */
    private $newPassword;

    /**
     * la proprieté "propertyPath" fais intervenir un autre champ qui va servir de comparaison (d'egalité grace a "EqualTo")
     * @Assert\EqualTo(propertyPath="newPassword",message="Vous n'aviez pas correctement entré votre nouveau mot de pass !")
     */
    private $confirmPassword;

    

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
