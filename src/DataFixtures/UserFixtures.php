<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private \Faker\Generator $faker;
    private ObjectManager $manager;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->generateUsers(2);

        $this->manager->flush();
    }

    public function generateUsers(int $number): void
    {

        $isVerified = [true, false];

        $cpt = 0;

        for($i = 0; $i < $number; $i++){
            $user = new User();

            $date = new \DateTimeImmutable('2021-01-01');
            $dateMustBeVerified = new \DateTimeImmutable('2021-01-02');

            $user->setEmail($this->faker->email)
                 ->setPassword($this->passwordEncoder->encodePassword($user, "badpassword"))
                 ->setIsVerified($isVerified[$i])
                 ->setLastName($this->faker->lastName)
                 ->setFirstName($this->faker->firstName)
                 ->setRegisteredAt($date)
                 ->setAccountMustBeVerifiedBefore($dateMustBeVerified)
            ;

            $this->addReference("user{$cpt}", $user);
            $cpt++;

            $this->manager->persist($user);
        }
    }


}
