<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
     /**
    * @Route("/login", name="login")
    */
   public function login(UserPasswordEncoderInterface $encoder,\Symfony\Component\HttpFoundation\Request $request, \App\Repository\UserRepository $rep)
   {
       $user = new \App\Entity\User();
       $form = $this->createForm(\App\Form\LoginType::class, $user);
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid())
       {
           $qb = $rep->createQueryBuilder('u');
           $qb->where('u.username = :username')
              ->setParameter('username', $user->getUsername());
        
           $dbUser = $qb->getQuery()->getOneOrNullResult();
           if(null !== $dbUser)
           {
               $passwordValid = $encoder->isPasswordValid($dbUser, $user->getPassword());
               if($passwordValid)
               {
               $request->getSession()->set("utilNom", $dbUser->getUsername());
               return $this->redirectToRoute('home');
                }
                else
                {
                return $this->render('Security\login.html.twig', [
                 "monForm" => $form->createView(),
                 "error" => true
                 ]);
                }
            }
            else
            {
              return $this->render('Security\login.html.twig', [
                 "monForm" => $form->createView(),
                 "error" => true
                  ]);
            }
       }
       else
       {
         return $this->render('Security\login.html.twig', [
                 "monForm" => $form->createView(),]);   
       }
   }
   /**
    * @Route("/logout", name="logout")
    */
   public function deconnexion(\Symfony\Component\HttpFoundation\Request $request)
   {
       $request->getSession()->invalidate();
       return $this->redirectToRoute('home');
   }
}
