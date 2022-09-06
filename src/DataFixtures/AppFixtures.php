<?php

namespace App\DataFixtures;

//use Faker;
use Faker\Factory;
use App\Entity\Phone;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
               
        $brands = [
            'Apple',
            'Samsung',
            'Nokia',
            'Huawei',
            'Sony',
            'Google',
            'Xiaomi',
        ];

        $date = $faker->dateTime();

        for ($i = 0; $i < 20; ++$i) {
            $phone = new Phone();
            $phone->setModel($faker->word)
                ->setBrand($brands[mt_rand(0, count($brands) - 1)])
                ->setDescription($faker->paragraph)
                ->setScreen($faker->randomFloat(1, 4, 7))
                ->setColor($faker->safeColorName)
                ->setPrice($faker->randomFloat(2, 800, 1500))
                ->setCreatedAt($date);

            $manager->persist($phone);
            $manager->flush();
        }
    }
    /* public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    } */
}
