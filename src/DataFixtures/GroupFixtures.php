<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GroupFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Group::class, 16, function(Group $group) {
            $author = $this->getRandomReference(User::class);
            $group->setAuthor($author);
            $group->addMember($author);
            $group->setName($this->faker->firstName());
            $group->setDate($this->faker->dateTimeBetween('-2 years', 'now'));
			for ($i=0; $i < $this->faker->numberBetween(3, 15); $i++) { 
				$user = $this->getRandomReference(User::class);
				while($group->getMember()->contains($user)) {
					$user = $this->getRandomReference(User::class);
				}
				$group->addMember($user);
			}
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}