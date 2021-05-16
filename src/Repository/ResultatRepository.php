<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Resultat;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Resultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultat[]    findAll()
 * @method Resultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Resultat>
 */
class ResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultat::class);
    }


    public function getNbResultatForOneAnswerAndQuestion(Question $question, Answer $answer): int
    {
        return $this->createQueryBuilder('nrfoaaq')
                    ->select('COUNT(nrfoaaq)')
                    ->where('nrfoaaq.Answer = :answer')
                    ->andWhere('nrfoaaq.Question = :question')
                    ->setParameters(new ArrayCollection([
                        new Parameter('answer', $answer),
                        new Parameter('question', $question)
                    ]))
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
