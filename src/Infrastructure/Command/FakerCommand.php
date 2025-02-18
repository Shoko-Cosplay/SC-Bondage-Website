<?php

namespace App\Infrastructure\Command;

use App\Database\Auth\User;
use App\Database\Cosplayers\Entity\Cosplayers;
use App\Database\Cosplayers\Entity\CosplayersSocial;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:faker', description: 'Faker command',hidden: true)]
class FakerCommand extends Command
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher,private readonly EntityManagerInterface $entityManager,?string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        $faker = Factory::create('fr');
        $faker->addProvider(new \Xvladqt\Faker\LoremFlickrProvider($faker));





        foreach (range(1,5) as $cosI) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user,'1234'));
            $user->setRoles(['ROLE_MASTER']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setCountry($faker->countryCode());
            $path = $faker->image(sys_get_temp_dir(), 300, 300, ['cats']);
            $uploaded = new UploadedFile($path,"avatar-".$cosI.".jpg","image/jpeg",null,true);
            $user->setAvatarFile($uploaded);

            $this->entityManager->persist($user);


        }
        foreach (range(1,5) as $cosI) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user,'1234'));
            $user->setRoles(['ROLE_SLAVE']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setCountry($faker->countryCode());
            $path = $faker->image(sys_get_temp_dir(), 300, 300, ['cats']);
            $uploaded = new UploadedFile($path,"avatar-".$cosI.".jpg","image/jpeg",null,true);
            $user->setAvatarFile($uploaded);

            $this->entityManager->persist($user);

        }
        foreach (range(1,5) as $cosI) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user,'1234'));
            $user->setRoles(['ROLE_BONDAGE']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setCountry($faker->countryCode());
            $path = $faker->image(sys_get_temp_dir(), 300, 300, ['cats']);
            $uploaded = new UploadedFile($path,"avatar-".$cosI.".jpg","image/jpeg",null,true);
            $user->setAvatarFile($uploaded);

            $this->entityManager->persist($user);


        }

        $this->entityManager->flush();

        return  Command::SUCCESS;
    }
}
