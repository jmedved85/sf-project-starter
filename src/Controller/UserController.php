<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user_list')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findBy([], ['userName' => 'ASC']);

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_user_edit')]
    public function edit(
        int $id,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Security $security,
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($id);
        $currentUser = $security->getUser();

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'isEdit' => true,
            'currentUser' => $currentUser,
            'currentRole' => $user->getRoles()[0] ?? null,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            if ($form->get('roleSelection')->getData()) {
                $user->setRoles([$form->get('roleSelection')->getData()]);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('flash_user_updated'));

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'isEdit' => true,
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/admin/user/toggle-active/{id}', name: 'app_user_toggle_active', methods: ['POST'])]
    public function toggleActive(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        /** @var User $loggedInUser */
        $loggedInUser = $this->getUser();

        if ($user->getId() === $loggedInUser->getId()) {
            $this->addFlash('danger', $translator->trans('flash_cannot_deactivate_yourself'));
        } else {
            $user->setActive('on' === $request->request->get('active'));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('flash_user_status_updated'));
        }

        return $this->redirectToRoute('app_user_list', ['id' => $user->getId()]);
    }

    #[Route('/admin/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUserAction(
        int $id,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('user_deleted'));

        $logger->info('User deleted', ['id' => $id]);

        return $this->redirectToRoute('app_user_list');
    }
}
