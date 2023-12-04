<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $program = new Program();
        $program->setTitle('The Walking Dead');
        $program->setSynopsis('Des zombies envahissent la terre');
        $program->setCategory($this->getReference('category_Action'));
        $manager->persist($program);
        $this->addReference('program_0', $program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Stranger Things');
        $program->setSynopsis('Les personnages de Stranger Things');
        $program->setCategory($this->getReference('category_Fantastique'));
        $manager->persist($program);
        $this->addReference('program_1', $program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Star Wars : Ahsoka');
        $program->setSynopsis('Les aventures d\'Ahsoka');
        $program->setCategory($this->getReference('category_Science Fiction'));
        $manager->persist($program);
        $this->addReference('program_2', $program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Black Mirror');
        $program->setSynopsis('Différentes histoires sur le thème des nouvelles technologies');
        $program->setCategory($this->getReference('category_Thriller'));
        $manager->persist($program);
        $this->addReference('program_3', $program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Doctor Who');
        $program->setSynopsis('Les aventures de Doctor Who');
        $program->setCategory($this->getReference('category_Science Fiction'));
        $manager->persist($program);
        $this->addReference('program_4', $program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Arcane');
        $program->setSynopsis('Synopsis de Arcane...');
        $program->setPoster('build\images\poster-arcane.jpeg');
        $program->setCategory($this->getReference('category_Animation'));
        //... set other program's properties
        $manager->persist($program);
        $this->addReference('program_5', $program);
        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
