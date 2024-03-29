<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{_locale}/{id}/edit', name: 'app_user_index', methods: ['GET', 'POST'], requirements: ['_locale' => 'fr|en'])]
    public function edit(Request $request, UserRepository $userRepository, User $user): Response
    {

        $locale = $request->getLocale();
        $request->setLocale('fr');

        $user = $this->getUser(); // Obtenez l'utilisateur courant

        $form = $this->createForm(UserType::class,$user); // Créez le formulaire et préremplissez les champs avec les informations de l'utilisateur

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true); //save bdd

            //$this->addFlash('success', 'Votre profil a été mis à jour.');

        }
        return $this->render('user/index.html.twig', [
            'locale' => $locale,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

  //  #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
   // public function edit(Request $request, User $user, UserRepository $userRepository): Response
    //{
      //  $form = $this->createForm(UserType::class, $user);
        //$form->handleRequest($request);

       // if ($form->isSubmitted() && $form->isValid()) {
         //   $userRepository->save($user, true);

           // return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        //}

       // return $this->renderForm('user/edit.html.twig', [
         //   'user' => $user,
           // 'form' => $form,
        //]);
   // }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
