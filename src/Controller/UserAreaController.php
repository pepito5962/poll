<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use App\Form\RemoveAccountReasonType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserAreaController extends AbstractController
{

    /**
     * @Route("/user-area", name="app_user_area")
     */
    public function index(QuestionRepository $questionRepo): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            throw new Exception("J'ai pas d'utilisateur");
        }

        return $this->render('user/user_area.html.twig', [
            "user" => $user,
            "nbSondage" => $questionRepo->getCountOfQuestionsByOneUser($user),
            "nbCurrentSondage" => $questionRepo->getCountOfQuestionsNotEndByOneUser($user),
            "currentQuestions" => $questionRepo->getCurrentQuestionByOneUser($user),
            "oldQuestions" => $questionRepo->getOldQuestionByOneUser($user)
        ]);
    }

    /** 
     * @Route("/user-admin", name="app_user_admin")
     */
    public function admin(UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepo->findAll();

        return $this->render('user/admin.html.twig', [
            "users" => $users
        ]);
    }

    /**
     * @Route("/user/admin/delete/{id<\d+>}", name="app_user_admin_delete")
     */
    public function deleteUser(User $user = null, EntityManagerInterface $manager, EmailVerifier $emailVerifier, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if(!$user){
            throw new Exception("L'utilisateur n'as pas été trouvé");
        }

        $form = $this->createForm(RemoveAccountReasonType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $emailVerifier->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => "Suppresion de votre compte",
                'html_template'   => "user/delete_email.html.twig",
                'context'         => [
                    'reason' => $form->get('reason')->getdata()
                ]
            ]);
    
            $manager->remove($user);
    
            $manager->flush();

            return $this->redirectToRoute('app_user_admin');
        }

        return $this->render("user/delete_form.html.twig", [
            "form" => $form->createView()
        ]);
        
    }

}