<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    
    /**
     * @Route("/home", name="app_home")
     */
    public function index(QuestionRepository $questionRepo): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'questions' => $questionRepo->findAll(),
            'nbTotalSondage' => $questionRepo->getCountOfQuestions(),
            'nbSondageEnCour' => $questionRepo->getCountOfQuestionsNotEnd(),
            'currentQuestions' => $questionRepo->getCurrentQuestion(),
            'oldQuestions' => $questionRepo->getOldQuestion()
        ]);
    }
}
