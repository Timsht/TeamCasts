<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Posts;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostsFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Posts::class, 36, function(Posts $post) {
            $post->setAuthor($this->getRandomReference(User::class)); 
            $post->setUser($this->getRandomReference(User::class));
            $post->setPost($this->faker->paragraph(rand(1,4), true));
            $post->setDate($this->faker->dateTimeBetween('-2 years', 'now'));
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