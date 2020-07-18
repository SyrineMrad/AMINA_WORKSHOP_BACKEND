<?php

namespace App\Controller\Api;
use App\Entity\Papier;
use App\Repository\PapierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api")
 */
class PapierController extends AbstractController
{
  /**
     * @Route("/papier", name="add_papier", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $papier = $serializer->deserialize($request->getContent(), Papier::class, 'json');
        $errors = $validator->validate($papier);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($phone);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'papier a bien été ajoutée'
        ];
        return new JsonResponse($data, 201);
    }
}