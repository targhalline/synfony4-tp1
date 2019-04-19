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
use App\Entity\Category;
use App\Entity\Skill;
use App\Entity\AdvertSkill;
use App\Repository;

/**
 * @Route("/advert")
 */
class AdvertController extends Controller{

	/**
	* @Route("/add-app/{id}", name="oc_advert_add_app", requirements={"id" = "\d+"})
	*/
	public function add_application(Request $request, $id){

		// Création d'une première candidature
		$application = new Application();
		$application->setAuthor('assil');
		$application->setContent("Je suis à l'écoute du marché");

		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;
		$em = $this->getDoctrine()->getManager();
		// On récupère l'annonce

		$advert = $repository->find($id);
		$application->setAdvert($advert);

		$em->persist($application);
		$em->flush();
		return $this->redirectToRoute('oc_advert_view', array('id' => $id));
	}

	/**
	* @Route("/repository", name="oc_advert_repository")
	*/
	public function find3(Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;

		$adverts = $repository->getAdvertWithCategories(array('Reseau', 'Integration'));
		
		return $this->render('Advert/view-titres1.html.twig', array(
			'adverts' => $adverts
		));
	}

	/**
	* @Route("/repository1/{limit}", name="oc_advert_repository1", requirements={"limit" = "\d+"} )
	*/
	public function find4($limit, Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Application')
		;

		$adverts = $repository->getApplicationsWithAdvert($limit);
		return $this->render('Advert/view-titres2.html.twig', array('adverts' => $adverts));
	}
 	
 	/**
	* @Route("/chercher", name="oc_advert_find")
	*/
	public function find1(Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;
		// On récupère le repository
		$listAdverts = $repository->findBy(
			array('author' => 'Alexandre'), // Critere
			array('date' => 'desc'),        // Tri
			5,                              // Limite
			0                               // Offset
		);

		if (null === $listAdverts) {
		throw new NotFoundHttpException("L'auteur n'existe pas n'existe pas.");
		}

		// Le render ne change pas, on passait avant un tableau, maintenant un objet
		return $this->render('Advert/find1.html.twig', array(
		'listAdverts' => $listAdverts
		));
	}

	/**
	* @Route("/cherche-un", name="oc_advert_find2")
	*/
	public function find2(Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Image')
		;
		// On récupère le repository
		$image = $repository->findOneBy(
			array('url' => 'http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg') // Critere
		);

		if (null === $image) {
		throw new NotFoundHttpException("L'image n'existe pas.");
		}

		// Le render ne change pas, on passait avant un tableau, maintenant un objet
		return $this->render('find2.html.twig', array(
		'image' => $image
		));
	}

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

		// $advert est donc une instance de App\Entity\Advert
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
	* @Route("/view-cat", name="oc_advert_view_cat")
	*/
	public function viewCat(Request $request){
		// On récupère le repository
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Category')
		;

		// On récupère l'entité correspondante à l'id $id
		$listCategories = $repository->findAll();
		// $advert est donc une instance de App\Entity\Advert
		// ou null si l'id $id  n'existe pas, d'où ce if :
		if (null === $listCategories) {
			throw new NotFoundHttpException("il n y a aucune catégorie.");
		}

		return $this->render('Advert/view-cat.html.twig', array(
		'listCategories' => $listCategories
		));
	}

	/**
	* @Route("/view-titres", name="oc_advert_view_titles")
	*/
	public function viewTitle(Request $request){
		// On récupère le repository
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App\Entity\Advert')
		;

		$listTitles = $repository->findByTitle('Recherche developpeur java.');

		if (null === $listTitles) {
			throw new NotFoundHttpException("il n y a aucun annonce correspond à ce titre.");
		}

		return $this->render('Advert/view-titres.html.twig', array(
		'listTitles' => $listTitles
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

	    // Création de l'entité
	    $advert = new Advert();
	    $advert->setTitle('Recherche devefefeloppeur java.');
	    $advert->setAuthor('Youeefssef');
	    $advert->setContent("Nous recherchons unfefef développeur java débutant sur Lyon. Blabla…");
	    // On peut ne pas définir ni la date ni la publication,
	    // car ces attributs sont définis automatiquement dans le constructeur

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
=======
  	/**
	* @Route("/add-cat/{id}", name="oc_advert_add_cat", requirements={"id" = "\d+"})
>>>>>>> ch3-p3
	*/
	 public function addCat($id, Request $request){
		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$advert = $em->getRepository('App\Entity\Advert')->find($id);

		if (null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		// La méthode findAll retourne toutes les catégories de la base de données
		$listCategories = $em->getRepository('App\Entity\Category')->findAll();

		// On boucle sur les catégories pour les lier à l'annonce
		foreach ($listCategories as $category) {
			$advert->addCategory($category);
		}

		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
		// Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

		// Étape 2 : On déclenche l'enregistrement
		$em->flush();
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

	/**
	* @Route("/del-cat/{id}", name="oc_advert_del_cat", requirements={"id" = "\d+"})
	*/
	public function delCat($id){
		
		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$advert = $em->getRepository('App\Entity\Advert')->find($id);

		if (null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		// On boucle sur les catégories de l'annonce pour les supprimer
		foreach ($advert->getCategories() as $category) {
			$advert->removeCategory($category);
		}
		$em->flush();
		return $this->render('Advert/delete.html.twig');
	}

	/**
	* @Route("/add-skills", name="oc_advert_del_cat")
	*/
	public function addSkills(Request $request) {
		// On récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();

		// Création de l'entité Advert
		$advert = new Advert();
		$advert->setTitle('Recherche développeur Symfony4.');
		$advert->setAuthor('Alexandre');
		$advert->setContent("Nous recherchons un développeur Symfony4 débutant sur cairo. Blabla…");

		// On récupère toutes les compétences possibles
		$listSkills = $em->getRepository('App\Entity\Skill')->findAll();

		// Pour chaque compétence
		foreach ($listSkills as $skill) {
			// On crée une nouvelle « relation entre 1 annonce et 1 compétence »
			$advertSkill = new AdvertSkill();

			// On la lie à l'annonce, qui est ici toujours la même
			$advertSkill->setAdvert($advert);
			// On la lie à la compétence, qui change ici dans la boucle foreach
			$advertSkill->setSkill($skill);

			// Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
			$advertSkill->setLevel('Expert');

			// Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
			$em->persist($advertSkill);
		}

		// Doctrine ne connait pas encore l'entité $advert. Si vous n'avez pas défini la relation AdvertSkill
		// avec un cascade persist (ce qui est le cas si vous avez utilisé mon code), alors on doit persister $advert
		$em->persist($advert);

		// On déclenche l'enregistrement
		$em->flush();

		// … reste de la méthode
		return $this->render('Advert/view.html.twig', array(
		'advert' => $advert
		));
	}

}
