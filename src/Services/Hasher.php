<?php

namespace App\Services;

use App\Entity\Participant;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Hasher
{
    private $passwordHasher;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
    }

    public function hash(
        string $password,
        Participant $participant
    ) : string {
        return $this->passwordHasher->hashPassword($participant, $password);
    }

}