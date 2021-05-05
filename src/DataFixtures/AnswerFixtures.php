<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnswerFixtures extends Fixture implements DependentFixtureInterface
{

    private ObjectManager $manager;

    /** @var array<mixed> $answers */
    private static array $answers = [
        0 => array(
            "Tombé dans la neige avant le 31 décembre",
            "Un frizby comestible",
            "Une Kipa surgelée",
            "La réponse D"
        ),
        1 => array(
            "Qu'il n'est pas encore arrivé à Torronto",
            "Qu'il était supposé arriver à Toronto ...",
            "Qu'est ce qu'il fout ce maudit pancake, tabernacle ?",
            "La réponse D"
        ),
        2 => array(
            "l'inciter à boire à l'Open Barmitzva",
            "lui présenter Raymond Barmitzva",
            "lui offir des Malabarmitzva",
            "La réponse D"
        ),
        3 => array(
            "En 1618, pendant la  guerre des croissants au beurre",
            "En 1702, pendant le massacre du Saint Panini",
            "En 112, avant Céline Dion pendant la prise de la brioche",
            "La réponse D"
        )
    ];

    public function getDependencies(){
        return [
            QuestionFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->generateAnswer();

        $manager->flush();
    }

    public function generateAnswer(): void
    {

        $cpt = 0;

        for($i = 0; $i<4; $i++){
            for($j = 0; $j<4; $j++){
                $answerObject = new Answer();

                $answerObject->setAnswer(self::$answers[$i][$j])
                             ->setQuestion($this->getReference("question" . $i));

                $this->addReference("answer{$cpt}", $answerObject);
                $cpt++;

                $this->manager->persist($answerObject);
            }
        }
    }
}
