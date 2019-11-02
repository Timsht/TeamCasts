<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Friendship;
use PHPUnit\Framework\TestCase;

class FriendshipTest extends TestCase
{
    public function testPost()
    {
        $post = new Friendship();
		$ask = new User();
        $post->setAsk($ask);
		$receive = new User();
        $post->setReceive($receive);
		$datePost = $post->getDate();
        $this->assertTrue($post->getValide() === 0 ? true : false, "Le post n'est pas valide");
        $this->assertSame($post->getAsk(), $ask);
        $this->assertSame($post->getReceive(), $receive);
		$this->assertSame($post->getDate(), $datePost);
    }
}
