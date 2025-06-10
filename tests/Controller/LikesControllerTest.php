<?php

namespace App\Tests\Controller;

use App\Entity\Likes;
use App\Entity\Posts;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LikesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testToggleSafeLike(): void
    {
        $user = $this->createUser();
        $post = $this->createPost($user);

        $this->client->loginUser($user);

        $this->client->request('POST', '/posts/' . $post->getId() . '/toggle-like', [
            'type' => 0
        ]);

        $this->assertResponseIsSuccessful();

        $likes = $post->getLikes()->filter(fn(Likes $like) => $like->isSafe());
        $this->assertCount(1, $likes);
    }

    public function testToggleDangerousLike(): void
    {
        $user = $this->createUser();
        $post = $this->createPost($user);

        $this->client->loginUser($user);

        $this->client->request('POST', '/posts/' . $post->getId() . '/toggle-like', [
            'type' => 1
        ]);

        $this->assertResponseIsSuccessful();

        $likes = $post->getLikes()->filter(fn(Likes $like) => $like->isDangerous());
        $this->assertCount(1, $likes);
    }

    public function testRemoveLike(): void
    {
        $user = $this->createUser();
        $post = $this->createPost($user);

        $like = new Likes();
        $like->setUserID($user);
        $like->setPost($post);
        $like->setType(0);

        $this->entityManager->persist($like);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        $this->client->request('POST', '/posts/' . $post->getId() . '/toggle-like', [
            'type' => 0
        ]);

        $this->assertResponseIsSuccessful();

        $likes = $post->getLikes()->filter(fn(Likes $like) => $like->isSafe());
        $this->assertCount(0, $likes);
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setPassword('password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createPost(User $user): Posts
    {
        $post = new Posts();
        $post->setTitle('Test Post');
        $post->setContenu('This is a test post.');
        $post->setDate(new \DateTime());
        $post->setUserID($user);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }
}
