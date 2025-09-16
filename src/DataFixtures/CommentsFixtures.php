<?php

/**
 * Comments fixture.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CommentsFixtures.
 */
class CommentsFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(3, 'Comments', function ($i) {
            $comments = new Comment();
            $comments->setNick($this->faker->word);
            $comments->setEmail(sprintf('admin%d@example.com', $i));
            $comments->setText($this->faker->sentence);
            $comments->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $comments->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $comments;
        });

        $manager->flush();
    }
}
