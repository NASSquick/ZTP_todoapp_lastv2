<?php

namespace App\Tests\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery');
        $manager->persist($gallery);

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setText('This is a test photo description.');
        $photo->setFilename('test.jpg');
        $photo->setGallery($gallery);
        $manager->persist($photo);

        $manager->flush();

        $comment = new Comment();
        $comment->setText('This is a test comment.');
        $comment->setNick('Tester');
        $comment->setEmail('test@example.com');
        $comment->setCreatedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());
        $comment->setPhoto($photo);
        $manager->persist($comment);

        $manager->flush();
    }
}

