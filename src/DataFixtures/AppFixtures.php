<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use App\Entity\User;
use App\Entity\Categories;
use App\Entity\Posts;
use App\Entity\Commentaires;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        // Create 10 users

        $suers = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);
            $user->setPpImg('https://picsum.photos/100/150');
            $user->setType('1');
            $user->setDangerous(0);
            $manager->persist($user);
            $suers[] = $user;
        }

        // Create 2 categories
        $categories = [];
        $categoryIcons = ['Camera', 'BookOpen', 'Users', 'Settings', 'Heart', 'Star'];
        for ($i = 0; $i < 2; $i++) {
            $category = new Categories();
            $category->setName($faker->word);
            $category->setDescription($faker->sentence(10));
            $category->setIcon($categoryIcons[array_rand($categoryIcons)]);
            $category->setDangerous(0); // Set dangerous to 0 for categories
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create 10 posts

        $posts = [];
        for ($i = 0; $i < 10; $i++) {
            $post = new Posts();
            $post->setTitle($faker->sentence(6, true));
            $post->setDate($faker->dateTimeBetween('-1 year', 'now'));
            $post->setUserID($suers[array_rand($suers)]);
            $post->setCat($categories[array_rand($categories)]);
            $post->setContenu($faker->paragraph(3, true));
            $post->setPhoto('https://picsum.photos/200/300'); // Random image URL
            $post->setDangerous(0); // Set dangerous to 0 for posts
            $manager->persist($post);
            $posts[] = $post;
        }

        // Create 10 comments
        $comments = [];
        for ($i = 0; $i < 10; $i++) {
            $comment = new Commentaires();
            $comment->setPost($posts[array_rand($posts)]);
            $comment->setUserID($suers[array_rand($suers)]);
            $comment->setContenu($faker->sentence(6, true));
            $comment->setCreationDate($faker->dateTimeBetween('-1 year', 'now'));
            $manager->persist($comment);
            $comments[] = $comment;
        }


        $manager->flush();
    }
}
