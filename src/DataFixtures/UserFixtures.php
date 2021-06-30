<?php
/**
 * UserFixtures
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserData;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 */
class UserFixtures extends AbstractBaseFixtures
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadData(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);

        $profile = new UserData();
        $profile->setEmail('admin@localhost');
        $profile->setLastName('Admin');
        $profile->setFirstName('Admin');

        $admin->setProfile($profile);

        $manager->persist($admin);
        $manager->flush();

        $this->addReference('admin', $admin);
    }
}
