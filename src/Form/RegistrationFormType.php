<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder
		    ->add('email', EmailType::class, [
			    'constraints' => [
				    new Email(['message' => 'Please enter a valid email address.']),
				    new Regex(
	                    [
		                    'pattern' => '/^[a-zA-Z.]*@deloitte\.com/',
		                    'match' => false,
		                    'message' => 'Please Enter a valid email address',
	                    ]
				    )
			    ]
		    ])
		    ->add('plainPassword', PasswordType::class, [
			    // instead of being set onto the object directly,
			    // this is read and encoded in the controller
				    'mapped' => false,
				    'constraints' => [
					    new NotBlank([
						    'message' => 'Please enter a password',
					    ]),
					    new Length([
						    'min' => 8,
						    'minMessage' => 'Your password should be at least {{ limit }} characters',
						    'max' => 32,
						    'maxMessage' => 'Your password should be at maximum {{ limit }} characters',
					    ]),
					    new Regex([
						    'pattern' => '/(?=.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])*$/',
						    'match' => false,
						    'message' => 'Merci de respecter les contraintes de MDP (1maj/1min/1nb/8char)'
				              ]
				    )
				    ]
		    ])
		    ->add('nom')
		    ->add('prenom')
		    ->add('departement', ChoiceType::class,[
			    'choices'=> [
				    'IT'=>'IT',
				    'compta'=>'compta',
				    'recrutement'=> 'recrutement',
			    ]
		    ])
		    ->add('photo',  FileType::class, [
			    'label' => 'Images (JPG/PNG file)',

			    'mapped' => false,
			    'required' => false,
			    'constraints' => [
				    new File([
					    'maxSize' => '10024k',
					    'mimeTypes' => [
						    'image/*'
					    ],
					    'mimeTypesMessage' => 'Please upload a valid JPG/PNG Image',
				    ])
			    ],
		    ])
	    ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
