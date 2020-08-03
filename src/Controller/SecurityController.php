<?php

namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{

   /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator,\Swift_Mailer $mailer)
    {
        $values = json_decode($request->getContent());
            if($values->confirmpassword!=$values->password) {
                $data = [
                    'status' => 500,
                    'message' => 'Confirmez le mot de passe '
                ];
            }
            else{
            $user = new User();
            $user->setemail($values->username);
            $user->setNom($values->nom);
            $user->setPrenom($values->prenom);
            $user->setSexe($values->sexe);
            $user->setDateNaissance($values->date_naissance);
            $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
            $user->setRoles(['ROLE_ADMIN']);
            $errors = $validator->validate($user);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            
            $message = (new \Swift_Message('Nouveau contact'))
            // On attribue l'expéditeur
            ->setFrom('mraadsyrine@gmail.com')
        
            // On attribue le destinataire
            ->setTo('mraadsyrine@gmail.com')
        
            // On crée le texte avec la vue
            ->setBody ( 'text/html');

        ;
        $mailer->send($message);
            $data = [
                'status' => 201,
                'message' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        return new JsonResponse($data, 500);
    }
 /**
     * @Route(name="api_login", path="/api/login_check")
     * @return JsonResponse
     */
    public function api_login(): JsonResponse
    {
        $user = $this->getUser();

        return new Response([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}