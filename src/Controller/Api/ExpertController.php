<?php

namespace App\Controller\Api;
use App\Entity\User;
use App\Repository\UserRepository;
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
class ExpertController extends AbstractController
{
  /**
     * @Route("/experts/add_expert", name="add_expert", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $expert=$request->getContent();
        $expert = $serializer->deserialize($request->getContent(), User::class, 'json');
        $expert->setCreatedAt(new \DateTime());
        $expert->setUpdatedAt(new \DateTime());
        $expert->setRoles(['ROLE_EXPERT']);
        $errors = $validator->validate($expert);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($expert);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'expert a bien été ajouté'
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/experts/update_expert/{id}", name="update_expert", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $expertUpdate = $entityManager->getRepository(User::class)->find((User::class)->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $expertUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($expertUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'expert a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/experts/delete_expert/{id}", name="delete_expert", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager)
    {
        //a tester
        $entityManager->remove(User::class);
        $entityManager->flush();
        return new Response(null, 204);
    }
        /**
     * @Route("/experts", name="list_experts", methods={"GET"})
     */
    public function getExperts(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $role='ROLE_EXPERT';
        $qb = $entityManager->createQueryBuilder();
        $qb->select('u')
        ->from(User::class, 'u')
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%"'.$role.'"%')
        ;
        $ok= $qb->getQuery()->getResult();
        $jsonContent = $serializer->serialize($ok, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object;
             }
         ]);
        return new Response($jsonContent, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
  
}
