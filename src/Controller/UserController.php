<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class UserController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    /**
     * @Route("/signin" , name="signin")
     */
    public function signin(UserPasswordEncoderInterface $encoder,\App\Repository\UserRepository $rep, \Symfony\Component\HttpFoundation\Request $request)
    {
        $dto = new \App\Entity\User();
        $form = $this->createForm(\App\Form\UserSigninType::class, $dto);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
            {
            $errorSignin = [];
            $qb = $rep->createQueryBuilder('u');
            $qb->where('u.username LIKE :username')
                ->setParameter('username', $dto->getUsername());
            $result = $qb->getQuery()->getOneOrNullResult();
            if(null !== $result)
                {
                $errorSignin['username'] = "exist";
                }
            $qb = $rep->createQueryBuilder('u');
            $qb->where('u.username LIKE :email')
                ->setParameter('email', $dto->getEmail());
            $result = $qb->getQuery()->getOneOrNullResult();
            if(null !== $result)
            {
                $errorSignin['email'] = "exist";
            }
            $encoded = $encoder->encodePassword($dto, $dto->getPassword());
            $dto->setPassword($encoded);
            if(count($errorSignin) === 0)
            {
            $em=$this->getDoctrine()->getManager();
            $em->persist($dto);
            $em->flush();
            return $this->redirectToRoute("login");
            }
            else
            {
                return $this->render(
                "user/signin.html.twig",
                ["monForm"=>$form->createView(),
                 "errorSignin"=>$errorSignin]
                );
            }
        }
        return $this->render("user/signin.html.twig", ["monForm"=>$form->createView()]);
    }
   
}
