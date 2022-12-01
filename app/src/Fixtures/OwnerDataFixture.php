<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use IXCSoft\TreinamentoDocker\Domain\Entity\Owner;
use IXCSoft\TreinamentoDocker\Domain\Entity\Pet;

final class OwnerDataFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('pt_BR');
        for ($i = 0; $i < 1000; $i++) {
            $owner = new Owner();
            $owner->setName($faker->name);
            $owner->setEmail($faker->email);
            $manager->persist($owner);
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
        return 1;
    }
}
