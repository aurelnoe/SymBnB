<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        require_once 'vendor/autoload.php';

        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        $adminUser = new User();
        $roleAdmin[] = 'ROLE_ADMIN';
        $adminUser->setRoles($roleAdmin)
                ->setFirstName('Aurélien')
                ->setLastName('Montaye')
                ->setEmail('aurel@mail.com')
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('<p></p>', $faker->paragraphs(3)) . '</p>')
                ->setPassword($this->encoder->encodePassword($adminUser, 'Password'))
                ->setPicture("http://randomuser.me/api/portraits/");
        $manager->persist($adminUser);
        
        //Nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female']; //Thermes choisi pour utilisation API avec faker
        for ($i=1; $i <= 10; $i++) { 
            $user = new User();

            $genre = $faker->randomElement($genres);
            //Utilisation API 
            $picture = 'http://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99);

            $picture .= $picture . ($genre = 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('<p></p>', $faker->paragraphs(3)) . '</p>')
                ->setPassword($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        //Nous gérons les annonces
        for ($i=1; $i <= 30; $i++) { 
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->text();
            $description = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setcoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setDescription($description)
                ->setPrice(mt_rand(40,200))
                ->setRoom(mt_rand(1,5))
                ->setAuthor($user);

            for ($j=1; $j <= mt_rand(2, 5) ; $j++) { 
                $image = new Image();

                $image->setUrl($faker->imageUrl(1000,350))
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);
            }

            //Gestion de reservation :
            for ($j=1; $j <= mt_rand(0, 10); $j++) { 
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                //Gestion de la date de fin
                $duration  = mt_rand(3, 10);
                $endDate   = (clone $startDate)->modify("+$duration days");
                
                $amount    = $ad->getPrice() * $duration;
                $booker    = $users[mt_rand(0, count($users) - 1)];
                $comment   = $faker->paragraph();

                $booking->setAd($ad)
                        ->setBooker($booker)
                        ->setCreatedAt($createdAt)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setAmount($amount)
                        ->setComment($comment);
                $manager->persist($booking);
            }

            $manager->persist($ad);
    
        }

        $manager->flush();
    }
}
