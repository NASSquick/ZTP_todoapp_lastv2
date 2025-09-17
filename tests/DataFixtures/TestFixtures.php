<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * This file defines data fixtures for testing purposes.
 * It provides sample Gallery, Photo, and Comment entities
 * to be used in functional and integration tests.
 */

namespace App\Tests\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TestFixtures.
 *
 * Loads test data into the database, including:
 * - A sample Gallery
 * - A sample Photo assigned to the Gallery
 * - A sample Comment assigned to the Photo
 */
class TestFixtures extends Fixture
{
    /**
     * Loads test entities into the database.
     *
     * @param ObjectManager $manager the Doctrine ObjectManager instance used to persist entities
     *
     * @return void this method does not return a value; it persists sample data for testing
     */
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
