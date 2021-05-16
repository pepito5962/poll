<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Answer>
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Return answers for one question
     *
     * @param Question $question
     * @return array<Answer>
     */
    public function getAnswersByQuestion(Question $question): array
    {
        return $this->createQueryBuilder('goq')
                    ->select('goq')
                    ->where('goq.question = :question')
                    ->setParameter('question', $question)
                    ->getQuery()
                    ->getResult();
    }
}
