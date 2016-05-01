<?php

namespace CG\CuddleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CuddleController extends Controller
{
    public function indexAction()
    {
        $cuddles = $this->getDoctrine()
            ->getManager()
            ->getRepository('CGCuddleBundle:Cuddle')
            ->findAll()
        ;

        return $this->render('CGCuddleBundle:Cuddle:index.html.twig', [
            'cuddles' => $cuddles
        ]);
    }
}