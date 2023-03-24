<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
  private $manager;

  private $user;

  public function __construct(EntityManagerInterface $manager, UserRepository $user)
  {
    $this->manager = $manager;

    $this->user = $user;
  }

  // create user
  #[Route('/user', name: 'app_user', methods: ['POST'])]
  public function index(Request $request): JsonResponse
  {
    $data = $request->toArray();

    $emailAlreadyExists = $this->user->findByOneByEmail($data['email']);

    if ($emailAlreadyExists) {
      return $this->json([
        'status' => false,
        'message' => 'Address e-mail already exists.',
      ],Response::HTTP_BAD_REQUEST);
    }

    $user = new User();
    $user->setEmail($data['email'])
        ->setPassword($data['password']);
    $this->manager->persist($user);
    $this->manager->flush();

    return $this->json([
      'status' => true,
      'message' => 'Account created.',
    ],Response::HTTP_CREATED);
  }

  #[Route('/user', name: 'get_allUsers', methods: ['GET'])]
  public function getAllUsers(Request $request): JsonResponse
  {
    return $this->json([
      'status' => true,
      'data' => $this->user->findAll(),
    ]);
  }
}