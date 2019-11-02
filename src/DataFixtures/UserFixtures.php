<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    private $name;

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->name = array(
            'Timscht',
            'Username',
            'User',
            'username2',
            'new',
            'username3',
            'username4',
            'Julie',
            'Lucie',
            'Marlene'
        );
        $this->encoder = $encoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        for ($i=0; $i < 30 ; $i++) { 
            $this->name[] = $this->faker->userName;
        }
        $this->createMany(User::class, count($this->name), function(User $user) {
            $key = array_rand($this->name, 1);
            $selected = $this->name[$key];
            unset($this->name[$key]);

            $user->setUsername($selected);
            $user->setEmail(strtolower($selected) . "@user.fr");
            $user->setPassword($this->encoder->encodePassword($user, 'useruser'));
            $user->setDate($this->faker->dateTimeBetween('-2 years', 'now'));
            $user->setValide(true);
            if ($user->getUsername() === "Timscht") {
                $user->setRoles(["ROLE_SUPER_ADMIN"]);
            }
        });
        
        $manager->flush();
    }
}