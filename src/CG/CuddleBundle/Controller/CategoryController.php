<?php

namespace CG\CuddleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CG\CuddleBundle\Entity\Category;
use CG\CuddleBundle\Form\CategoryType;
use CG\CuddleBundle\Form\SubscriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CategoryController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function subscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(SubscriptionType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $request->getSession()->getFlashBag()->add('notice', 'Abonnements modifiés !');
        }
        return $this->render('CGCuddleBundle:Category:subscriptions.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em
            ->getRepository('CGCuddleBundle:Category')
            ->findAll()
        ;

        return $this->render('CGCuddleBundle:Category:index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @ParamConverter("category", options={"mapping": {"category_slug": "slug"}})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewAction(Category $category)
    {
        // $cuddles = $em
        //     ->getRepository('CGCuddleBundle:Cuddle')
        //     ->getAllWithCategory($category)
        // ;

        return $this->render('CGCuddleBundle:Category:view.html.twig', [
            'category' => $category,
            // 'cuddles' => $cuddles
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien créée !');
            return $this->redirectToRoute('cg_cuddle_category_view', ['category_slug' => $category->getSlug()]);
        }

        return $this->render('CGCuddleBundle:Category:add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @ParamConverter("category", options={"mapping": {"category_slug": "slug"}})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien modifiée !');
            return $this->redirectToRoute('cg_cuddle_category_view', ['category_slug' => $category->getSlug()]);
        }

        return $this->render('CGCuddleBundle:Category:edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @ParamConverter("category", options={"mapping": {"category_slug": "slug"}})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Category $category, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien supprimée !');
            return $this->redirectToRoute('cg_cuddle_category_home');
        }

        return $this->render('CGCuddleBundle:Category:delete.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }
}