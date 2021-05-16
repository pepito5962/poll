<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 * @Table(name="questions")
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $question;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isMultipleChoice;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="question")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question", cascade={"remove"})
     * @var ArrayCollection<int, Answer> $answer
     */
    private $answer;

    /**
     * @ORM\OneToMany(targetEntity=Resultat::class, mappedBy="Question")
     */
    private $resultats;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->resultats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getIsMultipleChoice(): ?bool
    {
        return $this->isMultipleChoice;
    }

    public function setIsMultipleChoice(bool $isMultipleChoice): self
    {
        $this->isMultipleChoice = $isMultipleChoice;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswer(): Collection
    {
        return $this->answer;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answer->contains($answer)) {
            $this->answer[] = $answer;
            //$answer->setAnswer($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answer->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getAnswer() === $this) {
                $answer->setAnswer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Resultat[]
     */
    public function getResultats(): Collection
    {
        return $this->resultats;
    }

    public function addResultat(Resultat $resultat): self
    {
        if (!$this->resultats->contains($resultat)) {
            $this->resultats[] = $resultat;
            $resultat->setQuestion($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getQuestion() === $this) {
                $resultat->setQuestion(null);
            }
        }

        return $this;
    }

}
