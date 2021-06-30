<?php
/**
 * AlbumFixtures
 */

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Image;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AlbumFixtures
 * @package DataFixtures
 */
class AlbumWithImagesFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, ParameterBagInterface $parameterBag)
    {
        $this->filesystem = $filesystem;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadData(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // dodawanie albumów
        for ($i = 1; $i <= 30; ++$i) {
            $album = new Album();
            $album->setName("Album {$i}");
            $album->setDescription($faker->sentence);
            $photoToCopy = $this->getImgDir() . DIRECTORY_SEPARATOR . 'php-symfony.png';

            // dodawanie zdjęć do albumów
            $coverPhoto = '';
            for ($j = 1; $j <= 3; ++$j) {
                $dest = $this->getUploadDir() . DIRECTORY_SEPARATOR . "placeholder_album_{$i}_{$j}.png";
                $this->filesystem->copy($photoToCopy, $dest, true);
                $pathInfo = pathinfo($dest);

                $img = new Image();
                $img->setTitle("Image {$i}/{$j}");
                $img->setDescription($faker->sentence);
                $img->setFilename($pathInfo['basename']);
                if($coverPhoto === '') {
                    $coverPhoto = $pathInfo['basename'];
                }

                // dodawanie komentarzy do zdjęć
                for ($k = 1; $k <= 3; ++$k) {
                    $comment = new Comment();
                    $comment->setContent($faker->sentence);
                    $comment->setEmail($faker->email);
                    $comment->setNickname($faker->userName);
                    $img->addComment($comment);
                }

                $album->addImage($img);
            }

            $album->setCover($coverPhoto);
            $album->setCreatedAt($faker->dateTimeBetween('-365 days', '-1 days'));
            $album->setUser($this->getReference('admin'));

            $manager->persist($album);
        }

        $manager->flush();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return $this->parameterBag->get('upload_dir');
    }

    /**
     * @return string
     */
    protected function getImgDir()
    {
        return $this->parameterBag->get('img_dir');
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}