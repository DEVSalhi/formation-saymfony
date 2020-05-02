<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPassword
{

    /**
     * @Assert\Length(min=8,minMessage="votre mot de passe doit etre au minimum 8 caracter")
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword",message="veullez confirmer votre mot de passe")
     */
    private $ConfirmPassword;

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->ConfirmPassword;
    }

    public function setConfirmPassword(string $ConfirmPassword): self
    {
        $this->ConfirmPassword = $ConfirmPassword;

        return $this;
    }
}
