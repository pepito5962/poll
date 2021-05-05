<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{

    private ObjectManager $manager;

    /** @var array<string> $questions */
    private static array $questions = [
        "Lorsqu'un pancake tombe dans la neige avant le 31 décembre, on dit qu'il est : ",
        "Lorsqu'un pancake prend l'avion a destination de Toronto et qu'il fait une escale technique a St Claude, on dit :",
        "Lorsqu'on invite un pancake à une Barmitzva les convives doivent",
        "Au cours de quel évènement historique fut crée le pancake ?",
    ];

    public function getDependencies(){
        return [
            UserFixtures::class
        ];
    }


    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->generateQuestion();

        $manager->flush();
    }

    private function generateQuestion(): void
    {
        $isMultipleChoice = [true, false];

        $cpt = 0;
        
        foreach(self::$questions as $key => $question){
            $questionObject = new Question();

            [
                'dateObject' =>$dateObject,
                'dateString' => $dateString
            ] = $this->generateRandomDateBetweenRange('01/05/2021', '31/12/2021');

            $questionObject->setQuestion($question)
                           ->setIsMultipleChoice($isMultipleChoice[random_int(0,1)])
                           ->setEndDate($dateObject)
                           ->setUser($this->getReference("user" . mt_rand(0,1)));
            
            $this->addReference("question{$cpt}", $questionObject);
            $cpt++;

            $this->manager->persist($questionObject);
        }
    }

    /**
     * Generate a random DateTimeImmutable object and related date string between a start date and a end date
     *
     * @param string $start Date with format 'd/m/Y'
     * @param string $end Date with format 'd/m/Y'
     * @return array{dateObject: \DateTimeImmutable, dateString: string} String with 'd-m-Y'
     */
    private function generateRandomDateBetweenRange(string $start, string $end) : array
    {
        
        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate = \DateTime::createFromFormat('d/m/Y', $end);

        if(!$startDate || !$endDate){
            throw new HttpException(400, "La date doit etre sous le format 'd/m/Y' pour les deux dates");
        }

        $randomTimestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());

        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);
        
        return [
            'dateObject' => $dateTimeImmutable,
            'dateString' => $dateTimeImmutable->format('d-m-Y')
        ];
    }
}
