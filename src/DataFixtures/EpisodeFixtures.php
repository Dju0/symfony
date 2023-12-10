<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager) : void
    {
        $faker = Factory::create();

        // Supposons que vous avez 10 saisons
        for ($i = 0; $i < 30; $i++) {
            for ($j = 0; $j < 10; $j++) { // 10 épisodes par saison
                $episode = new Episode();
                $episode->setTitle($faker->sentence);
                $episode->setNumber($j + 1);
                $episode->setSynopsis($faker->paragraph);
                $episode->setDuration(50);
                $slug = $this->slugger->slug($episode->getTitle());
                $episode->setSlug($slug);

                // Assurez-vous que la référence 'season_' . $i existe dans SeasonFixtures
                $episode->setSeason($this->getReference('season_' . $i));
                $manager->persist($episode);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

