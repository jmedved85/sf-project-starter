<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Security $security,
    ): Response {
        $user = new User();
        $currentUser = $security->getUser();

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'isEdit' => false,
            'currentUser' => $currentUser,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $selectedRole = $form->get('roleSelection')->getData();

            if ($selectedRole) {
                $user->setRoles([$selectedRole]);
            }

            // encode the plain password
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setActive(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $this->addFlash('success', $translator->trans('flash_registration_successful'));

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'isEdit' => false,
        ]);
    }
}
