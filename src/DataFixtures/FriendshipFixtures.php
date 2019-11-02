<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Friendship;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FriendshipFixtures extends BaseFixture implements DependentFixtureInterface
{
	private $friendship = [];

	protected function loadData(ObjectManager $manager)
	{
		$this->createMany(Friendship::class, 326, function(Friendship $friendship){
			$ask = $this->getRandomReference(User::class);
			$receive = $this->getRandomReference(User::class);
			
			while ($ask === $receive || in_array([$ask, $receive], $this->friendship) || in_array([$receive, $ask], $this->friendship)) {
				$ask = $this->getRandomReference(User::class);
				$receive = $this->getRandomReference(User::class);
			}

			$friendship->setAsk($ask);
			$friendship->setReceive($receive);
            $friendship->setDate($this->faker->dateTimeBetween('-2 years', 'now'));
			if($this->faker->boolean(80)) {
				$friendship->setValide(true);
				$ask->addFriend($receive);
			}
			$ask->addFriendship($friendship);
			$receive->addFriendship($friendship);
			
			$this->friendship[] = [$ask, $receive];
			$this->friendship[] = [$receive, $ask];
		});

		$manager->flush();
	}

	public function getDependencies() {
		return array(
			UserFixtures::class,
		);
	}
}