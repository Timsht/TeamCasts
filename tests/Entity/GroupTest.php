<?php

namespace App\Tests\Entity;

use App\Entity\Group;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testPost()
    {
        $post = new Group();
        $post->setName("Groupe");
		$datePost = $post->getDate();
		$author = new User();
        $post->setAuthor($author);
        $this->assertTrue($post->getValide() === true ? true : false, "Le Groupe n'est pas valide");
        $this->assertSame($post->getName(), "Groupe");
        $this->assertSame($post->getDate(), $datePost);
        $this->assertSame($post->getAuthor(), $author);
    }
}
