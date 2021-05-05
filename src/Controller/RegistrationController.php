<?php

namespace App\Controller;

use DateInterval;
use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class RegistrationController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct (EmailVerifier $emailVerifier){
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        UserPasswordEncoderInterface $passwordEncoder, 
        GuardAuthenticatorHandler $guardHandler, 
        LoginFormAuthenticator $authenticator, 
        TokenGeneratorInterface $tokenGenerator
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $registrationToken = $tokenGenerator->generateToken();

            $now = new \DateTimeImmutable();
            $nextDay = $now->add(new DateInterval('P1D')); //add one day

            $user->setRegistrationToken($registrationToken)
                 ->setPassword(
                        $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                )
                ->setRegisteredAt($now)
                ->setAccountMustBeVerifiedBefore($nextDay)
                ->setIsVerified(false)
            ;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => "Vérification de votre addresse email",
                'html_template'   => "registration/confirmation_email.html.twig",
                'context'         => [
                    'userID'            => $user->getId(),
                    'registrationToken' => $registrationToken,
                    'tokenLifeTime'     => $user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H:i')
                ]
            ]);

            /* connexion automatique suite a l'inscription
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );*/
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{token}", name="app_verify_account")
     */
    public function verifyUserEmail(EntityManagerInterface $manager, User $user, string $token): Response
    {
        if(($user->getRegistrationToken() === null) || ($user->getRegistrationToken() !== $token) || ($this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore()))){
            throw new AccessDeniedException();
        }
        $user->setIsVerified(true);

        $user->setAccountVerifiedAt(new \DateTimeImmutable(('now')));

        $user->setRegistrationToken(null);

        $manager->flush();

        $this->addFlash('success', 'Votre compte utilisteur est activé, vous pouvez vous connecter');

        return $this->redirectToRoute('app_login');
    }

    private function isNotRequestedInTime(\DateTimeImmutable $accountMusBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $accountMusBeVerifiedBefore);
    }
}
