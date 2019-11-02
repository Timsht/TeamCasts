<?php

namespace App\Tests\Entity;

use App\Entity\Posts;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testPost()
    {
        $post = new Posts();
        $post->setPost("Post");
		$datePost = $post->getDate();
		$user = new User();
        $post->setUser($user);
		$author = new User();
        $post->setAuthor($author);
        $this->assertTrue($post->getValide() === 1 ? true : false, "Le post n'est pas valide");
        $this->assertSame($post->getPost(), "Post");
        $this->assertSame($post->getDate(), $datePost);
        $this->assertSame($post->getUser(), $user);
        $this->assertSame($post->getAuthor(), $author);
    }
}
