<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\DriverException;
use App\Service\Exception\UserServiceException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $userRepository;
    private $userManager;
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository,
                                EntityManagerInterface $userManager, 
                                UserPasswordEncoderInterface $passwordEncoder) 
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function addUser(User $user, $form)
    {
        try {   
            
            // encode the plain password
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->userManager->persist($user);
            $this->userManager->flush(); 

        } 
        catch (DriverException $e) {
            throw new UserServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function editUser(User $user)
    {
        try {  
            $this->userManager->persist($user);
            $this->userManager->flush(); 
        } 
        catch (DriverException $e) {
            throw new UserServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function getUsers()
    {
        try {
            $users = $this->userRepository->findAll();
            return $users;     
        } 
        catch (DriverException $e) {
            throw new UserServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function getUserById(int $id)
    {
        try {
            $user = $this->userRepository->find($id);
            return $user; 
        } 
        catch (DriverException $e) {
            throw new UserServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function updatePassword(Object $user) {

        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    public function deleteUser(Object $id)
    {
        try {
            $user = $this->userRepository->find($id);
            $this->userManager->remove($user);
            $this->userManager->flush();
        } 
        catch (DriverException $e) {
            throw new UserServiceException("Un problème technique est survenu", $e->getCode());
        }
    }
}