<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user (ROLE_ADMIN) for the back office.',
)]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Admin e-mail')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Plain password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getOption('email');
        $plain = (string) $input->getOption('password');

        if ('' === $email || '' === $plain) {
            $io->error('Use --email= and --password= (both required).');

            return Command::FAILURE;
        }

        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            $io->error('A user with this e-mail already exists.');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plain));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created: '.$email);

        return Command::SUCCESS;
    }
}
