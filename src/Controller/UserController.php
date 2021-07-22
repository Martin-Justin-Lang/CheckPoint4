<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/user",  name="user_")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param User $user
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    public function show(User $user, PictureRepository $pictureRepository): Response
    {

        return $this->render('/user/show.html.twig', [
            'user' => $user,
            'picture' => $pictureRepository->findOneByUser($user)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
