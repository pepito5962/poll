<?php

namespace App\Entity;

use App\Repository\ResultatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultatsRepository::class)
 */
class Resultats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdResultat;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdUser;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdReponse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdResultat(): ?int
    {
        return $this->IdResultat;
    }

    public function setIdResultat(int $IdResultat): self
    {
        $this->IdResultat = $IdResultat;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->IdUser;
    }

    public function setIdUser(int $IdUser): self
    {
        $this->IdUser = $IdUser;

        return $this;
    }

    public function getIdReponse(): ?int
    {
        return $this->IdReponse;
    }

    public function setIdReponse(int $IdReponse): self
    {
        $this->IdReponse = $IdReponse;

        return $this;
    }
}
