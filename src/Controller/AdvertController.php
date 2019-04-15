<?php
// src/Controller/AdvertController.php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reqsponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environement;
use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\Application;


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

		$em = $this->getDoctrine()->getManager();
		$listApplications = $em
		->getRepository('App\Entity\Application')
		->findBy(array('advert' => $advert))
		;

		// Le render ne change pas, on passait avant un tableau, maintenant un objet
		return $this->render('Advert/view.html.twig', array(
		'advert' => $advert,
		'listApplications' => $listApplications
		));
	}

	/**
	* @Route("/add", name="oc_advert_add")
	*/
	 public function add(Request $request){
	    // Création de l'entité Advert
		$advert = new Advert();
		$advert->setTitle('Recherche integrateur Symfony.');
		$advert->setAuthor('Alexandre');
		$advert->setContent("Nous recherchons un integrateur Symfony débutant sur Lyon. Blabla…");

		 // Création de l'entité Image
	    $image = new Image();
	    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
	    $image->setAlt('Job de rêve');

	    // On lie l'image à l'annonce
	    $advert->setImage($image);

		// Création d'une première candidature
		$application1 = new Application();
		$application1->setAuthor('Marine');
		$application1->setContent("J'ai toutes les qualités requises.");

		// Création d'une deuxième candidature par exemple
		$application2 = new Application();
		$application2->setAuthor('Pierre');
		$application2->setContent("Je suis très motivé.");

		// On lie les candidatures à l'annonce
		$application1->setAdvert($advert);
		$application2->setAdvert($advert);

		// On récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();

		// Étape 1 : On « persiste » l'entité
		$em->persist($advert);

		// Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
		// définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
		$em->persist($application1);
		$em->persist($application2);

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
	* @Route("/edit-img/{id}", name="oc_advert_edit_img", requirements={"id" = "\d+"})
	*/
	public function editImageAction($advertId){

		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;
		// On récupère l'annonce
		$advert = $repository->find($advertId);
		// On modifie l'URL de l'image par exemple
		$advert->getImage()->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');

		// On n'a pas besoin de persister l'annonce ni l'image.
		// Rappelez-vous, ces entités sont automatiquement persistées car
		// on les a récupérées depuis Doctrine lui-même

		// On déclenche la modification
		$em->flush();

		return $this->render('Advert/view.html.twig', array(
		'advert' => $advert
		));
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
