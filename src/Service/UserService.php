<?php
/**
 * Class UserService
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 */
class UserService
{
    /** @var \App\Repository\UserRepository */
    private $userRepository;

    /** @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserService constructor.
     * @param \App\Repository\UserRepository                                        $userRepository
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param int $id
     *
     * @return \App\Entity\User|null
     */
    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * Zapisuje lub aktualizuje użytkownika
     * @param \App\Entity\User $user
     * @param string|null      $newPlainPassword
     */
    public function save(User $user, ?string $newPlainPassword = null)
    {
        if ($newPlainPassword) {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $user,
                $newPlainPassword
            );

            $user->setPassword($encodedPassword);
        }

        $this->userRepository->save($user);
    }
}
