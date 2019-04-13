<?php
// src/Controller/AdvertController.php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environement;
use App\Entity\Advert;

/**
 * @Route("/advert")
 */
class AdvertController extends Controller{
	/**
	* @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
	*/
	public function index($page){

		// On ne sait pas combien de pages il y a
		// Mais on sait qu'une page doit être supérieure ou égale à 1
		if ($page < 1) {
			// On déclenche une exception NotFoundHttpException, cela va afficher
			// une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
			throw $this->createNotFoundException('Page "'.$page.'" inexistante.');
		}

		// Ici, on récupérera la liste des annonces, puis on la passera au template

		// Mais pour l'instant, on ne fait qu'appeler le template
		return $this->render('Advert/index.html.twig', ['page' => $page]);
	}

	/**
	* @Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
	*/
	public function view($id){
		// On récupère le repository
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;

		// On récupère l'entité correspondante à l'id $id
		$advert = $repository->find($id);

		// $advert est donc une instance de OC\PlatformBundle\Entity\Advert
		// ou null si l'id $id  n'existe pas, d'où ce if :
		if (null === $advert) {
		throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		// Le render ne change pas, on passait avant un tableau, maintenant un objet
		return $this->render('Advert/view.html.twig', array(
		'advert' => $advert
		));
	}

	/**
	* @Route("/add", name="oc_advert_add")
	*/
	 public function add(Request $request){
	    // Création de l'entité
	    $advert = new Advert();
	    $advert->setTitle('Recherche developpeur java.');
	    $advert->setAuthor('Youssef');
	    $advert->setContent("Nous recherchons un développeur java débutant sur Lyon. Blabla…");
	    // On peut ne pas définir ni la date ni la publication,
	    // car ces attributs sont définis automatiquement dans le constructeur

	    // On récupère l'EntityManager
	    $em = $this->getDoctrine()->getManager();

	    // Étape 1 : On « persiste » l'entité
	    $em->persist($advert);

	    // Étape 2 : On « flush » tout ce qui a été persisté avant
	    $em->flush();

	    // Reste de la méthode qu'on avait déjà écrit
	    if ($request->isMethod('POST')) {
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

			// Puis on redirige vers la page de visualisation de cettte annonce
			return $this->redirectToRoute('oc_advert_view', array('id' => $advert->getId()));
	    }

	    // Si on n'est pas en POST, alors on affiche le formulaire
	    return $this->render('Advert/add.html.twig', array('advert' => $advert));
  	}


	/**
	* @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" = "\d+"})
	*/
	public function delete($id){
		// Ici, on récupérera l'annonce correspondant à $id

		// Ici, on gérera la suppression de l'annonce en question

		return $this->render('Advert/delete.html.twig');
	}
}
