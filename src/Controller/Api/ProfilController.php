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
    public function updateprofil(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user=$this->getUser();
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        $values = json_decode($request->getContent());
        $user->setemail($values->username);
        $user->setNom($values->nom);
        $user->setPrenom($values->prenom);
        $user->setSexe($values->sexe);
        $user->setDateNaissance($values->date_naissance);
        $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
        $errors = $validator->validate($user);
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
