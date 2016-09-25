<?php

namespace CG\CuddleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CG\CuddleBundle\Entity\Cuddle;
use CG\CuddleBundle\Entity\CuddleUser;
use CG\UserBundle\Entity\User;
use CG\CuddleBundle\Form\CuddleType;
use CG\CuddleBundle\Form\CuddleEditType;

class CuddleController extends Controller
{
    const SEND_CUDDLES_KEY = '62ebe69ed032abaa7bc434664bf9e045c0f844f0bdeef84e3cb8020174fe640f';

    public function testAction()
    {
        $user = $this->getUser();
        $user->removeRole('ROLE_ADMIN');
        $user->addRole('ROLE_MODERATOR');
        $this->get('fos_user.user_manager')->updateUser($user);
        return new Response('ok');
        // $cuddles = $this->getDoctrine()
        //     ->getManager()
        //     ->getRepository('CGCuddleBundle:Cuddle')
        //     ->getUserCuddles($this->getUser())
        // ;

        // $this->sendNewCuddlesEmail($this->getUser(), $cuddles);

        // return $this->render('CGCuddleBundle:Cuddle:mine.html.twig', [
        //     'cuddles' => $cuddles
        // ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        
        $cuddlesUsers = $this->getDoctrine()
            ->getManager()
            ->getRepository('CGCuddleBundle:CuddleUser')
            ->getReceivedCuddles($this->getUser())
        ;

        return $this->render('CGCuddleBundle:Cuddle:index.html.twig', [
            'cuddlesUsers' => $cuddlesUsers
        ]);

    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function mineAction()
    {
        $cuddles = $this->getDoctrine()
            ->getManager()
            ->getRepository('CGCuddleBundle:Cuddle')
            ->getUserCuddles($this->getUser())
        ;

        return $this->render('CGCuddleBundle:Cuddle:mine.html.twig', [
            'cuddles' => $cuddles
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request)
    {
        $cuddle = new Cuddle;
        $form = $this->createForm(CuddleType::class, $cuddle);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cuddle);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Câlin bien enregistré ! Merci beaucoup !');

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cg_cuddle_add' : 'cg_cuddle_mine';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('CGCuddleBundle:Cuddle:add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function manageAction()
    {
        $cuddles = $this->getDoctrine()
            ->getManager()
            ->getRepository('CGCuddleBundle:Cuddle')
            ->getNotYetValidatedCuddles()
        ;

        return $this->render('CGCuddleBundle:Cuddle:manage.html.twig', [
            'cuddles' => $cuddles
        ]);
    }

    /**
     * @ParamConverter("cuddle", options={"mapping": {"cuddle_id": "id"}})
     * @Security("is_granted('edit', cuddle)")
     */
    public function editAction(Cuddle $cuddle, Request $request)
    {
        $form = $this->createForm(CuddleEditType::class, $cuddle);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Câlin bien modifié !');
            return $this->redirectToRoute('cg_cuddle_manage');
        }
        return $this->render('CGCuddleBundle:Cuddle:edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @ParamConverter("cuddle", options={"mapping": {"cuddle_id": "id"}})
     * @Security("is_granted('validate', cuddle)")
     */
    public function validateAction(Cuddle $cuddle, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $cuddle->setValidated(true);
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Câlin bien validé !');
            return $this->redirectToRoute('cg_cuddle_manage');
        }
        return $this->render('CGCuddleBundle:Cuddle:validate.html.twig', [
            'cuddle' => $cuddle,
            'form' => $form->createView()
        ]);
    }

    /**
     * @ParamConverter("cuddle", options={"mapping": {"cuddle_id": "id"}})
     * @Security("is_granted('delete', cuddle)")
     */
    public function deleteAction(Cuddle $cuddle, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cuddle);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Câlin bien supprimé !');
            return $this->redirectToRoute('cg_cuddle_manage');
        }
        return $this->render('CGCuddleBundle:Cuddle:delete.html.twig', [
            'cuddle' => $cuddle,
            'form' => $form->createView()
        ]);
    }

    public function sendCuddlesAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->sendCuddles();
            $request->getSession()->getFlashBag()->add('notice', 'Câlins bien envoyés !');
            return $this->render('CGCuddleBundle:Cuddle:send.html.twig');
        }
        
        if ($request->query->get('token') === self::SEND_CUDDLES_KEY) {
            $this->sendCuddles();
            return new Response("ok");
        }

        throw $this->createAccessDeniedException();

    }

    private function sendCuddles() {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        $em = $this->getDoctrine()->getManager();

        foreach ($users as $user) {
            $cuddle = $em
                ->getRepository('CGCuddleBundle:Cuddle')
                ->getCuddleForSubscriptionOfUser($user)
            ;
            if ($cuddle !== null) {
                $cuddleUser = new CuddleUser;
                $cuddleUser->setCuddle($cuddle);
                $cuddleUser->setUser($user);
                $em->persist($cuddleUser);

                $this->sendNewCuddlesEmail($user, [$cuddle]);
            }
        }

        $em->flush();
    }

    private function sendNewCuddlesEmail(User $user, array $cuddles)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('De nouveaux câlins sont arrivés pour vous !')
            ->setFrom(['noreply@cuddle-generator.com' => 'Générateur de Câlin'])
            ->setTo([$user->getEmail() => $user->getUsername()])
            ->setBody(
                $this->renderView('CGCuddleBundle:Email:new_cuddles.html.twig', [
                    'user' => $user,
                    'cuddles' => $cuddles
                ]),
                'text/html'
            )
            ->addPart(
                $this->renderView('CGCuddleBundle:Email:new_cuddles.txt.twig', [
                    'user' => $user,
                    'cuddles' => $cuddles
                ]),
                'text/plain'
            )
        ;

        $this->get('mailer')->send($message);
    }
}