<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAreaController extends AbstractController
{

    /**
     * @Route("/userArea", name="app_user_area")
     */
    public function index(QuestionRepository $questionRepo): Response
    {

        $user = $this->getUser();

        return $this->render('user/user_area.html.twig', [
            "user" => $user,
            "nbSondage" => $questionRepo->getCountOfQuestionsByOneUser($user),
            "nbCurrentSondage" => $questionRepo->getCountOfQuestionsNotEndByOneUser($user),
            "currentQuestions" => $questionRepo->getCurrentQuestionByOneUser($user),
            "oldQuestions" => $questionRepo->getOldQuestionByOneUser($user)
        ]);
    }

}