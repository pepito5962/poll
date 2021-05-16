<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Answer;
use DateTimeImmutable;
use App\Entity\Question;
use App\Entity\Resultat;
use App\Form\SondageType;
use App\Form\SondageReplyType;
use App\Repository\AnswerRepository;
use App\Repository\ResultatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SondageController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(
        EntityManagerInterface $manager
        )
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/sondage/create", name="app_create_sondage", priority=1)
     */
    public function index(Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $question = new Question();

        $form = $this->createForm(SondageType::class, $question);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$this->endDateValidator($form->get('endDate')->getData())){
                throw new Exception("La date de fin de sondage est inférieur a la date du jour");
            }

            /** @var User $user */
            $user = $this->getUser();

            $question->setUser($user);
            
            for($i = 0; $i < 2; $i++){ //for the moment only 2 answers possible
                $answer = new Answer();

                $answer->setAnswer($form->get('answer'.$i)->getData())
                       ->setQuestion($question)
                ;

                $this->manager->persist($answer);
            }

            $this->manager->persist($question);

            $this->manager->flush();

            return $this->redirectToRoute('app_home'); //TODO modify route for redirect to show sondage (la page pour repondre a ce sondage quoi)
        }

        return $this->render('sondage/create_or_edit.html.twig', [
            'form' => $form->createView(),
            'editMode' => false
        ]);
    }

    /** 
     * @Route("/sondage/edit/{id<\d+>}", name="app_edit_sondage")
     */
    public function editSondage(Question $question = null, AnswerRepository $answerRepo, ResultatRepository $resultatRepo, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if(!$question){
            throw new Exception("La question du sondage n'a pas été trouvé");
        }

        if($user != $question->getUser()){
            throw new Exception("Ce sondage ne vous appartient pas. L'accès vous est refusé");
        }

        if($this->sondageHaveAlreadyAtLeastOneAnswer($resultatRepo, $question)){
            throw new Exception("IL y a déja des réponses pour ce sondage. Vous ne pouvez plus modifiez votre sondage");
        }

        $answers = $answerRepo->getAnswersByQuestion($question);

        $form = $this->createForm(SondageType::class, $question);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$this->endDateValidator($form->get('endDate')->getData())){
                throw new Exception("La date de fin de sondage est inférieur a la date du jour");
            }

            $question->setUser($user);
            
            for($i = 0; $i < 2; $i++){ //for the moment only 2 answers possible
                
                $answers[$i]->setAnswer($form->get('answer'.$i)->getData())
                            ->setQuestion($question)
                ;
            }

            $this->manager->flush();

            return $this->redirectToRoute("app_home");
        }

        return $this->render("sondage/create_or_edit.html.twig", [
            "form" => $form->createView(),
            'editMode' => true
        ]);
    }


    /**
     * @Route("/sondage/reply/{id<\d+>}", name="app_reply_sondage")
     */
    public function replySondage(Question $question, AnswerRepository $answerRepo, ResultatRepository $resultatRepo, Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        if(!$question){
            throw new Exception("La question du sondage n'a pas été trouvé");
        }

        /** @var User $user */
        $user = $this->getUser();

        if($this->UserAlreadyReplySondage($user, $question, $resultatRepo)){
            throw new Exception("Vous avez déja répondue a ce sondage");
        }

        $answers = $answerRepo->getAnswersByQuestion($question);

        $form = $this->createForm(SondageReplyType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $multipleChoice = $question->getIsMultipleChoice();

            for($i = 0; $i < 2; $i++){
                if($form->get('answer'.$i)->getData()){

                    $resultat = new Resultat();

                    $resultat->setQuestion($question)
                             ->setUser($user)
                             ->setAnswer($answers[$i])
                    ;

                    $this->manager->persist($resultat);

                    if(!$multipleChoice){
                        break;
                    }
                }
            }
            $this->manager->flush();

            return $this->redirectToRoute("app_home");
        }

        return $this->render("sondage/reply.html.twig", [
            "form" => $form->createView(),
            "question" => $question,
            "answers" => $answers
        ]);
    }

    /**
     * @Route("/sondage/delete/{id<\d+>}", name="app_delete_sondage")
     */
    public function deleteSondage(Question $question = null): RedirectResponse
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        if(!$question){
            throw new Exception("Le sondage n'existe pas. La question n'as pas été trouvé");
        }

        /** @var User $user */
        $user = $this->getUser();

        if($user != $question->getUser()){
            throw new Exception("Ce sondage ne vous appartient pas. L'accès vous est refusé");
        }

        $this->manager->remove($question);

        $this->manager->flush();

        return $this->redirectToRoute('app_home');

    }

    /**
    * @Route("/sondage/result/{id<\d+>}", name="app_result_sondage")
    */
    public function resultSondage(Question $question = null, AnswerRepository $answerRepo, ResultatRepository $resultatRepo): Response
    {

        if(!$question){
            throw new Exception("Le sondage n'existe pas. La question n'as pas été trouvé");
        }

        $answers = $answerRepo->getAnswersByQuestion($question);

        $resultats = [];

        foreach ($answers as $answer){

            $nbResult = $resultatRepo->getNbResultatForOneAnswerAndQuestion($question, $answer);

            $resultats[$answer->getAnswer()] = $nbResult;
        }

        return $this->render("sondage/result.html.twig", [
            "question" => $question,
            "answers" => $answers,
            "resultats" => $resultats
        ]);
    }


    /**
     * Check if sondage is finish
     *
     * @param DateTimeImmutable $date
     * @return boolean
     */
    private function endDateValidator(DateTimeImmutable $date): bool 
    {
        return $date > new DateTimeImmutable('now');
    }

    /**
     * Check if the user has already answered the sondage
     *
     * @param User $user
     * @param Question $question
     * @param ResultatRepository $resultatRepo
     * @return boolean
     */
    private function UserAlreadyReplySondage(User $user, Question $question, ResultatRepository $resultatRepo): bool
    {
        //chercher si il a déja repondue a une question return false si non true si vraie

        $resultat = $resultatRepo->findOneBy([
            "User" => $user,
            "Question" => $question
            ]);

        return $resultat !== null;
    }

    private function sondageHaveAlreadyAtLeastOneAnswer(ResultatRepository $resultatRepo, Question $question): bool
    {
        $resultat = $resultatRepo->findOneBy([
            "Question" => $question
        ]);

        return $resultat !== null;
    }
}