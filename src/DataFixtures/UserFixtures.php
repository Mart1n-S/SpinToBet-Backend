<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        // Créer une instance de Faker pour générer des données aléatoires
        $faker = Faker::create();

        // Créer 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPseudo($faker->firstName);
            $user->setIsVerified(true);
            $user->setEmail($faker->unique()->safeEmail);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'password'
            ));
            $manager->persist($user);
        }

        // Créer un utilisateur admin
        $userAdmin = new User();
        $userAdmin->setPseudo('admin');
        $userAdmin->setIsVerified(true);
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword($this->passwordHasher->hashPassword(
            $userAdmin,
            'password'
        ));
        $manager->persist($userAdmin);

        // Création d'un utilisateur classique
        $userClassique = new User();
        $userClassique->setPseudo('userClassique');
        $userClassique->setIsVerified(true);
        $userClassique->setEmail('user@user.com');
        $userClassique->setPassword($this->passwordHasher->hashPassword(
            $userClassique,
            'password'
        ));
        $manager->persist($userClassique);

        $manager->flush();
    }
}
