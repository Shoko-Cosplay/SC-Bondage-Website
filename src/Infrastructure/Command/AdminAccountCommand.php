<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Database\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:admin_account', description: 'Create new admin account',hidden: true)]
class AdminAccountCommand extends Command
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher,private EntityManagerInterface $entityManager,?string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);
        $io->title("Created Admin Account");


        $username = new Question("Username ?");
        $username = $io->askQuestion($username);
        $email = new Question("Email ?");
        $email = $io->askQuestion($email);
        $password = new Question("Password ?");
        $password = $io->askQuestion($password);

        $account = new User();
        $account->setUsername($username);
        $account->setEmail($email);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));
        $account->setRoles(["ROLE_ADMIN"]);
        $account->setCountry("FR");
        $account->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($account);
        $this->entityManager->flush();


        return Command::SUCCESS;
    }
}
