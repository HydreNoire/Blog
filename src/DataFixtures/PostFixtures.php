<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Factory\PostFactory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Zenstruck\Foundry\create;
use function Zenstruck\Foundry\faker;

class PostFixtures extends Fixture
{

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
     $this->hasher = $hasher;   
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // $manager->flush();

        for($i = 0; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail(faker()->email())
                ->setUsername(faker()->unique()->sentence(1))
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setName(faker()->firstName())
                ->setLastname(faker()->lastName());

                $manager->persist($user);
        }

        for($i = 0; $i <= 3; $i++) {
            $category = new Category();
            $category->setName(faker()->unique()->sentence(2));
            $manager->persist($category);

            for($j = 0; $j <= mt_rand(3, 8); $j++) {
                $post = new Post();

                $post->setTitle(faker()->unique()->sentence())
                    ->setDescription(faker()->text())
                    ->setContent(faker()->text(1000))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setCategory($category);

                $manager->persist($post);
            }
        }

        $manager->flush();

        // PostFactory::createMany(5);
    }
}
