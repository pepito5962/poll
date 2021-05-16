<?php

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Question;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function getCountOfQuestions(): int
    {
        return $this->createQueryBuilder('cq')
                    ->select('COUNT(cq)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getCountOfQuestionsByOneUser(User $user): int
    {
        return $this->createQueryBuilder('cqbou')
                    ->select('COUNT(cqbou)')
                    ->where('cqbou.user = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getCountOfQuestionsNotEnd(): int
    {
        return $this->createQueryBuilder('cqne')
                    ->select('COUNT(cqne)')
                    ->where('cqne.endDate > :now')
                    ->setParameter('now', new DateTimeImmutable('now'))
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getCountOfQuestionsNotEndByOneUser(User $user): int
    {
        return $this->createQueryBuilder('cqnebou')
                    ->select('COUNT(cqnebou)')
                    ->where('cqnebou.user = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    /**
     * Return current sondage
     *
     * @return array<Question>
     */
    public function getCurrentQuestion(): array
    {
        return $this->createQueryBuilder('gcq')
                    ->select('gcq')
                    ->where('gcq.endDate > :now')
                    ->setParameter('now', new DateTimeImmutable('now'))
                    ->getQuery()
                    ->getResult();

    }

    /**
     * Return end sondage
     * 
     * @return array<Question>
     */
    public function getOldQuestion(): array
    {
        return $this->createQueryBuilder('goq')
                    ->select('goq')
                    ->where('goq.endDate < :now')
                    ->setParameter('now', new DateTimeImmutable('now'))
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Return current sondage created by one user
     *
     * @param User $user
     * @return array<Question>
     */
    public function getCurrentQuestionByOneUser(User $user): array
    {
        return $this->createQueryBuilder('gcqbou')
                    ->select('gcqbou')
                    ->where('gcqbou.user = :user')
                    ->andWhere('gcqbou.endDate > :now')
                    ->setParameters(new ArrayCollection([
                        new Parameter('user', $user),
                        new Parameter('now', new DateTimeImmutable('now'))
                    ]))
                    ->getQuery()
                    ->getResult();

    }

    /**
     * Return end sondage created by one user
     *
     * @param User $user
     * @return array<Question>
     */
    public function getOldQuestionByOneUser(User $user): array
    {
        return $this->createQueryBuilder('goqbou')
                    ->select('goqbou')
                    ->where('goqbou.user = :user')
                    ->andWhere('goqbou.endDate < :now')
                    ->setParameters(new ArrayCollection([
                        new Parameter('user', $user),
                        new Parameter('now', new DateTimeImmutable('now'))
                    ]))
                    ->getQuery()
                    ->getResult();
    }
}
