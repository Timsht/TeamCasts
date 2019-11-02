<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser()
    {
        $user = new User();
        $user->setUsername("Patrick");
        $dateUser = $user->getDate();
        $user->setEmail("email@user.fr");
        $this->assertTrue($user->getValide() === 1 ? true : false, "L'utilisateur n'est pas valide");
        $this->assertSame($user->getUsername(), "Patrick");
        $this->assertSame($user->getDate(), $dateUser);
        $this->assertSame($user->getEmail(), "email@user.fr");
    }
}
