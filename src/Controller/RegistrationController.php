<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AuthAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
	/**
	 * @Route("/register", name="app_register")
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param GuardAuthenticatorHandler $guardHandler
	 * @param AuthAuthenticator $authenticator
	 * @param SluggerInterface $slugger
	 * @return Response
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AuthAuthenticator $authenticator, SluggerInterface $slugger): Response
	{
		$user = new User();
		$form = $this -> createForm ( RegistrationFormType::class, $user );
		$form -> handleRequest ( $request );

		if ($form -> isSubmitted () && $form -> isValid ()) {
			// encode the plain password
			$user -> setPassword (
				$passwordEncoder -> encodePassword (
					$user,
					$form -> get ( 'plainPassword' ) -> getData ()
				)

			);
			$image = $form -> get ( 'photo' ) -> getData ();


			if ($image) {
				$originalFilename = pathinfo ( $image -> getClientOriginalName (), PATHINFO_FILENAME );
				$safeFilename = $slugger -> slug ( $originalFilename );
				$newFilename = $safeFilename . '-' . uniqid () . '.' . $image -> guessExtension ();


				try {
					$image -> move (
						$this -> getParameter ( 'upload_directory' ),
						$newFilename
					);
				} catch ( FileException $e ) {

					$user -> setPhoto ( $newFilename );
				}
			$entityManager = $this -> getDoctrine () -> getManager ();
			$entityManager -> persist ( $user );
			$entityManager -> flush ();




			return $this -> redirect ( $this -> generateUrl ( 'upload_directory' ) );
		}
		return $guardHandler -> authenticateUserAndHandleSuccess (
			$user,
			$request,
			$authenticator,
			'main' // firewall name in security.yaml
		);
	}
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
