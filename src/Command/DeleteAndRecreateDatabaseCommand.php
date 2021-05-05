<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteAndRecreateDatabaseCommand extends Command
{
    protected static $defaultName = 'app:clean-db';
    /** @var string $defaultDescription */
    protected static $defaultDescription = 'delete and recreate database withe structure';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln('<comment>Delete Database</comment>');
        
        $this->executeConsoleFunction($input, $output, "doctrine:database:drop", true);

        $output->writeln('<info>The database has been deleted successfully</info>');

        $output->writeln('<comment>Create database</comment>');

        $this->executeConsoleFunction($input, $output, "doctrine:database:create");

        $output->writeln('<info>The database was created successfully </info>');

        $output->writeln('<comment>Make migrations</comment>');

        $this->executeConsoleFunction($input, $output, "doctrine:migrations:migrate");

        $output->writeln('<info>The migration was successful </info>');

        $io->success('Database has been recreated with success.');

        return Command::SUCCESS;
    }

    private function executeConsoleFunction(InputInterface $input, OutputInterface $output, string $command, bool $force = false): void
    {
        $application = $this->getApplication();

        if(!$application){
            throw new \LogicException("No application");
        }

        $command = $application->find($command);

        if ($force) {
            $input = new ArrayInput([
                '--force' => true
            ]);
        }
        
        $input->setInteractive(false);

        $command->run($input, $output);
    }
}
