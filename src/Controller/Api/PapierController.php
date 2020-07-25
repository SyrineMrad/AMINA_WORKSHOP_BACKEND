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
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api")
 */
class PapierController extends AbstractController
{
  /**
     * @Route("/add_papier", name="add_papier", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator,Security $security)
    {
        $user = $security->getUser();// A faire : enregister user_id avec le papier
        $papier = $serializer->deserialize($request->getContent(), Papier::class, 'json');
        $errors = $validator->validate($papier);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($papier);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'papier a bien été ajoutée'
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/papiers", name="list_papier", methods={"GET"})
     */
    public function getSendedPapiers(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $papiers = $entityManager->getRepository(Papier::class)->findBy(array('etat' => true));
        $data = $serializer->serialize($papiers, 'json');
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/draftpapiers", name="listdraft_papier", methods={"GET"})
     */
    public function getDraftPapiers(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $papiers = $entityManager->getRepository(Papier::class)->findBy(array('etat' => false));
        $data = $serializer->serialize($papiers, 'json');
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/update_papier/{id}", name="update_papier", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, Papier $papier, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $papierUpdate = $entityManager->getRepository(Papier::class)->find($papier->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $papierUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($papierUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'papier a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/delete_papier/{id}", name="delete_papier", methods={"DELETE"})
     */
    public function delete(Papier $papier, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($papier);
        $entityManager->flush();
        return new Response(null, 204);
    }

}