<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Entity\Pet;

enum Breed: int
{
    case dog = 1;
    case cat = 2;
    case fish = 3;
}
