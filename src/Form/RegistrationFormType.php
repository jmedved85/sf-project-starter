<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $minChars = 8;
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('firstName', TextType::class, [
                'label' => $this->translator->trans('first_name'),
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->translator->trans('last_name'),
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('email'),
                'invalid_message' => $this->translator->trans('invalid_email_message'),
            ])
            ->add('userName', HiddenType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $this->translator->trans('password'),
                'mapped' => false,
                'required' => !$isEdit,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => $isEdit ? [] : [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => $minChars,
                        'minMessage' => $this->translator->trans('password_limit_message', ['{{ min }}' => $minChars]),
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => $this->translator->trans('user_type'),
                'choices' => [
                    'Administrator' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (isset($data['email'])) {
                $email = $data['email'];
                $userName = strstr($email, '@', true);
                $data['userName'] = $userName;
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
