<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Article;

class ArticleController extends Controller {





/**
* @Route("/create-article")
*/
public function createAction(Request $request) {

  $article = new Article();
  $form = $this->createFormBuilder($article)
    ->add('title', TextType::class)
    ->add('author', TextType::class)
    ->add('body', TextareaType::class)
    ->add('url', TextType::class,
    array('required' => false, 'attr' => array('placeholder' => 'www.example.com')))
    ->add('save', SubmitType::class, array('label' => 'New Article'))
    ->getForm();

  $form->handleRequest($request);

  if ($form->isSubmitted()) {

    $article = $form->getData();

    $em = $this->getDoctrine()->getManager();
    $em->persist($article);
    $em->flush();

    return $this->redirect('/view-article/' . $article->getId());

  }

  return $this->render(
    'article/edit.html.twig',
    array('form' => $form->createView())
    );

}
/**
* @Route("/show-articles")
*/  
public function showAction() {

  $articles = $this->getDoctrine()
    ->getRepository('AppBundle:Article')
    ->findAll();

  return $this->render(
    'article/show.html.twig',
    array('articles' => $articles)
    );

}

/**
* @Route("/view-article/{id}")
*/   
public function viewAction($id) {

  $article = $this->getDoctrine()
    ->getRepository('AppBundle:Article')
    ->find($id);

  if (!$article) {
    throw $this->createNotFoundException(
    'There are no articles with the following id: ' . $id
    );
  }

  return $this->render(
    'article/view.html.twig',
    array('article' => $article)
    );

}

/**
* @Route("/delete-article/{id}")
*/ 
public function deleteAction($id) {

  $em = $this->getDoctrine()->getManager();
  $article = $em->getRepository('AppBundle:Article')->find($id);

  if (!$article) {
    throw $this->createNotFoundException(
    'There are no articles with the following id: ' . $id
    );
  }

  $em->remove($article);
  $em->flush();

  return $this->redirect('/show-articles');

}

/**
* @Route("/update-article/{id}")
*/  
public function updateAction(Request $request, $id) {

  $em = $this->getDoctrine()->getManager();
  $article = $em->getRepository('AppBundle:Article')->find($id);

  if (!$article) {
    throw $this->createNotFoundException(
    'There are no articles with the following id: ' . $id
    );
  }

  $form = $this->createFormBuilder($article)
    ->add('title', TextType::class)
    ->add('author', TextType::class)
    ->add('body', TextareaType::class)
    ->add('url', TextType::class,
    array('required' => false, 'attr' => array('placeholder' => 'www.example.com')))
    ->add('save', SubmitType::class, array('label' => 'Update'))
    ->getForm();

  $form->handleRequest($request);

  if ($form->isSubmitted()) {

    $article = $form->getData();
    $em->flush();

    return $this->redirect('/view-article/' . $id);

  }

  return $this->render(
    'article/edit.html.twig',
    array('form' => $form->createView())
    );

}


}


?>
