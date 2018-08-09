<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Image;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    private function json_response(Array $data)
    {
      $data_json = json_encode($data);
      $res = new Response();
      $res->headers->set('Content-Type', 'application/json');
      $res->headers->set('Access-Control-Allow-Origin', '*');
      //$res->headers->set('Access-Control-Allow-Methods', '*');
      $res->headers->set('Access-Control-Allow-Headers', 'Content-Type');
      $res->setContent($data_json);
      return $res;
    }

    /**
     * @Route("/api/category", name="api_category")
     */
    public function category()
    {
          $categoryRepo =
            $this->getDoctrine()->getRepository(Category::class);

          $categories = $categoryRepo->findBy([], ['label' => 'ASC']);


          $categories_assoc = [];
          foreach($categories as $category) {
            $category_assoc = array(
              'id' => $category->getId(),
              'label' => $category->getLabel()
            );
            array_push($categories_assoc, $category_assoc);
          }

          return $this->json_response($categories_assoc);
    }

    /**
     * @Route("/api/image", name="api_image")
     */
    public function image(Request $request)
    {
      // récupération des paramètres d'URL
      // ->query donne accès à la superglobale $_GET
      $category_id      = intval($request->query->get('cat'));

      $filters = []; // par défaut pas de filtre
      if ($category_id != 0) $filters['category'] = $category_id;

      $imageRepo = $this->getDoctrine()->getRepository(Image::class);
      $images = $imageRepo->findBy($filters, []);

      if ($images) {
        //return $this->json_response(['question0' => $questions[0]->getLabel()]);
        $img = [];
        foreach($images as $image) {
          $image = [
            'id' => $image->getId(),
            'title' => $image->getTitle(),
            'description' => $image->getDescription(),
            'photo'=> $image->getPhoto()

          ];
          array_push($img, $image);
        }
        shuffle($img); // mélange les éléments du tableau de manière aléatoire
        return $this->json_response($img);

      } else {
        return $this->json_response(['img' => 'aucune proverb']);
      }

    }

}
