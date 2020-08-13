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
class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil", methods={"GET"})
     */
    public function profil(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $user=$this->getUser();
        $profil= $entityManager->getRepository(User::class)->find($user->getId());
        $data = $serializer->serialize($profil, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
             }
         ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/profil/update_profil/{id}", name="update_profil", methods={"PUT"})
     */
    public function updateprofil(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user=$this->getUser();
        $userUpdate = $entityManager->getRepository(User::class)->find($user->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $userUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($userUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'profil a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
}
