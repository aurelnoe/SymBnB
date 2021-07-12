<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\Service\UserService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    /**
     * @Route("/user/{id}", name="user_show")
     * @Entity("Specialite", expr="$this.repo.findBySlug(slug)")
     */
    public function index(int $id): Response
    {
        $user = $this->service->getUserById($id);

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
