<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('user_deleted'));

        return $this->redirectToRoute('app_user_list');
    }
}
