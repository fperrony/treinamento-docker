<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use IXCSoft\TreinamentoDocker\Domain\Entity\Owner;
use IXCSoft\TreinamentoDocker\Domain\Entity\Pet;

final class PetDataFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('pt_BR');
        for ($i = 0; $i < 3752; $i++) {
            $pet = new Pet();
            $pet->setName($faker->firstName);
            $pet->setBreed(Pet\Breed::from($faker->numberBetween(1, 3)));
            $pet->setOwner($manager->getReference(Owner::class, $faker->numberBetween(1, 999)));
            $manager->persist($pet);
            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }
        $manager->flush();
        $manager->clear();
    }

    public function getOrder(): int
    {
        return 2;
    }

}
