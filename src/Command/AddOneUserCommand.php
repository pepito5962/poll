<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\CustomValidatorForCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddOneUserCommand extends Command
{
    protected static $defaultName = 'app:add-one-user';
    /** @var string $defaultDescription */
    protected static $defaultDescription = 'Crée un utilisateur en base de données';

    private CustomValidatorForCommand $validator;
    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $encoder;
    private UserRepository $userRepository;

    public function __construct(
        CustomValidatorForCommand $validator,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository){

            parent::__construct();
            $this->validator = $validator;
            $this->entityManager = $entityManager;
            $this->encoder = $encoder;
            $this->userRepository = $userRepository;

    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'L\'email de l\'utilisateur')
            ->addArgument('firstName', InputArgument::REQUIRED, 'Prénom de l\'utilisateur')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Nom de l\'utilisater')
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'Le mot de passe de l\'utilisateur')
            ->addArgument('role', InputArgument::REQUIRED, 'Le role de l\'utilisateur')
            ->addArgument('isVerified', InputArgument::REQUIRED, 'Le statut du compte utilisateur (actif ou non)')
        ;
    }

    /**
     * Executed after configure() to initialize  properties based on the input arguments and options
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * Executed after initialize() and before execute()
     * Check if some of the options/arguments are missing and ask the user for those values
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
       $this->io->section("AJOUT D'UN UTILISATEUR EN BASE DE DONNEES");
       
       $this->enterEmail($input, $output);

       $this->enterLastName($input, $output);

       $this->enterFirstName($input, $output);

       $this->enterPassword($input, $output);

       $this->enterRole($input, $output);

       $this->enterIsVerified($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $firstName */
        $firstName = $input->getArgument('firstName');

        /** @var string $lastName */
        $lastName = $input->getArgument('lastName');

        /** @var string $plainPassword */
        $plainPassword = $input->getArgument('plainPassword');

        /** @var bool $isVerified */
        $isVerified = $input->getArgument('isVerified') === "INACTIF" ? false : true;

        /** @var array<string> $role */
        $role = [$input->getArgument('role')];

        $user = new User();

        $user->setEmail($email)
             ->setLastName($lastName)
             ->setFirstName($firstName)
             ->setPassword($this->encoder->encodePassword($user, $plainPassword))
             ->setIsVerified($isVerified)
             ->setRoles($role)
             ->setRegisteredAt(new \DateTimeImmutable())
             ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable())     
        ;

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $this->io->success("UN NOUVELLE UTILISATEUR EST PRESENT EN BASE DE DONNEES !");

        return Command::SUCCESS;
    }

    /**
     * Set user email
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterEmail(InputInterface $input, OutputInterface $output): void 
    {
        $helper = $this->getHelper('question');

        $emailQuestion = new Question("EMAIL DE L'UTILISATEUR : ");

        $emailQuestion->setValidator([$this->validator, 'validateEmail']);

        $email = $helper->ask($input, $output, $emailQuestion);

        if($this->userAlreadyExist($email)){
            throw new RuntimeException(sprintf("UTILISATEUR DEJA PRESENT EN BDD AVEC L'EMAIL SUIVANT: %s", $email));
        }

        $input->setArgument('email', $email);
    }

    /**
     * Set firstName user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterFirstName(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $firstNameQuestion = new Question("PRENOM DE L'UTILISATEUR : ");

        $firstNameQuestion->setValidator([$this->validator, 'validateName']);

        $firstName = $helper->ask($input, $output, $firstNameQuestion);

        $input->setArgument('firstName', $firstName);
    }

    /**
     * set lastName user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterLastName(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $lastNameQuestion = new Question("NOM DE L'UTILISATEUR : ");

        $lastNameQuestion->setValidator(([$this->validator, 'validateName']));

        $lastName = $helper->ask($input, $output, $lastNameQuestion);

        $input->setArgument('lastName', $lastName);
    }

    /**
     * Sets the password entered in $input variable if is valid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterPassword(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $passwordQuestion = new Question("PASSWORD DE L'UTILISATEUR EN CLAIRE (algorithme de hashage argon2id) :");

        $passwordQuestion->setValidator([$this->validator, 'validatePassword']);

        $passwordQuestion->setHidden(true)
                         ->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $passwordQuestion);

        $input->setArgument('plainPassword', $password);
    }

    /**
     * Sets the role choice in $input variable if is valid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterRole(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $roleQuestion = new ChoiceQuestion(
            "SELECTION DU ROLE DE L'UTILISATEUR : ",
            [
                'ROLE_USER', 'ROLE_ADMIN'
            ],
            'ROLE_USER'
        );

        $roleQuestion->setErrorMessage('ROLE UTILISATEUR INVALIDE');

        $role = $helper->ask($input, $output, $roleQuestion);

        $output->writeln("<info>ROLE UTILISATEUR PRIS EN COMPTE : {$role}</info>");
        
        $input->setArgument('role', $role);
    }

    /**
     * Sets the isVerified choice entered in $input variable if is valid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function enterIsVerified(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $isVerifiedQuestion = new ChoiceQuestion(
            "SELECTION DU STATUT D'ACTIVATION DU COMPTE UTILISATEUR : ",
            [
                'INACTIF', 'ACTIF'
            ],
            'ACTIF'
        );

        $isVerifiedQuestion->setErrorMessage("STATUT D'ACTIVATION DU COMPTE UTILISATEUR INVALIDE.");

        $isVerified = $helper->ask($input, $output, $isVerifiedQuestion);

        $output->writeln("<info>STATUT D'ACTIVATION COMPTE UTILISATEUR PRIS EN COMPTE : {$isVerified}</info>");

        $input->setArgument('isVerified', $isVerified);

    }

    /**
     * Check if an user already exists in database with the email entered by the user in the CLI
     *
     * @param string $email
     * @return User|null
     */
    private function userAlreadyExist(string $email): ?User
    {
        return $this->userRepository->findOneBy([
            'email' => $email
        ]);
    }
}
