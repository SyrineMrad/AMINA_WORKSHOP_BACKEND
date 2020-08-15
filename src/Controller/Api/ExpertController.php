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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class ExpertController extends AbstractController
{
  /**
     * @Route("/experts/add_expert", name="add_expert", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator,UserPasswordEncoderInterface $passwordEncoder)
    {
        $values = json_decode($request->getContent());
        $expert = new User();
        $expert->setemail($values->username);
        $expert->setNom($values->nom);
        $expert->setPrenom($values->prenom);
        $expert->setSexe($values->sexe);
        $expert->setDateNaissance($values->date_naissance);
        $expert->setPassword($passwordEncoder->encodePassword($expert, $values->password));
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
    public function update(Request $request, SerializerInterface $serializer, User $expert, ValidatorInterface $validator, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder)
    {
        $expertUpdate = $entityManager->getRepository(User::class)->find($expert->getId());
        $values = json_decode($request->getContent());
        $expertUpdate->setemail($values->username);
        $expertUpdate->setNom($values->nom);
        $expertUpdate->setPrenom($values->prenom);
        $expertUpdate->setSexe($values->sexe);
        $expertUpdate->setDateNaissance($values->date_naissance);
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
    public function delete(User $expert, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($expert);
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
