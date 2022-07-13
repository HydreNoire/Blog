<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Factory\PostFactory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // $manager->flush();

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
