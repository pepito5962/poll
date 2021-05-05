<?php

namespace App\Entity;

use App\Repository\ReponsesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReponsesRepository::class)
 */
class Reponses
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
    private $IdReponse;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdQuestion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reponse;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdQuestion(): ?int
    {
        return $this->IdQuestion;
    }

    public function setIdQuestion(int $IdQuestion): self
    {
        $this->IdQuestion = $IdQuestion;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }
}
