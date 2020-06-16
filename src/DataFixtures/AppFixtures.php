<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create(
                sprintf("email+%d@email.com", $i),
                sprintf("name+%d", $i)
            );
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, "password"));
            $manager->persist($user);

            $users[] = $user;
        }

        foreach ($users as $user) {
            for ($j = 1; $j <= 5; $j++) {
                $post = Post::create("Content", $user);

                shuffle($users);

                foreach (array_slice($users, 0, 5) as $userCanLike) {
                    $post->likeBy($userCanLike);
                }

                $manager->persist($post);

                for ($k = 1; $k <= 10; $k++) {
                    $comment = Comment::create(sprintf("Message %d", $k), $users[array_rand($users)], $post);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
