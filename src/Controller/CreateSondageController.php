<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use App\Form\CreateSondageType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateSondageController extends AbstractController
{

    /**
     * @Route("/createSondage", name="app_create_sondage")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $question = new Question();

        $form = $this->createForm(CreateSondageType::class, $question);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$this->endDateValidator($form->get('endDate')->getData())){
                throw new ErrorException("La date de fin de sondage est inférieur a la date du jour");
            }

            /** @var User $user */
            $user = $this->getUser();

            $question->setUser($user);
            
            for($i = 0; $i < 2; $i++){ //for the moment only 2 answers possible
                $answer = new Answer();

                $answer->setAnswer($form->get('answer'.$i)->getData())
                       ->setQuestion($question)
                ;

                $manager->persist($answer);
            }

            $manager->persist($question);

            $manager->flush();

            return $this->redirectToRoute('app_home'); //TODO modify route for redirect to show sondage (la page pour repondre a ce sondage quoi)
        }

        return $this->render('create_sondage/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function endDateValidator(DateTimeImmutable $date): bool 
    {
        return $date > new DateTimeImmutable('now');
    }
}