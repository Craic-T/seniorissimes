<?php

namespace App\Controller;

use App\Form\VilleType;
use App\Repository\ActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(
        ActiviteRepository $activiteRepo,
        Request $request
        ): Response
    {
        $form = $this->createForm(VilleType::class);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid())
        {
            $ville = $form['ville']->getData();
          

            // API
            $apikey = "a0742c85528ef8c3c99498b98e9e20e7";
            $apicall = "https://api.openweathermap.org/data/2.5/weather?q=$ville,fr&APPID=$apikey&units=metric&lang=fr";
            
           $resultapi = json_decode(file_get_contents("$apicall"),true);
             /*   $keys = array_keys($resultapi); 
           print_r($keys); */
            // requête API avec $ville
            $temperature = intval($resultapi["main"]["temp"]); // temp récup depuis API
            $condiMeteo = $resultapi["weather"][0]["main"]; // conditions météo de l'api
            $maxtemp = intval($resultapi["main"]["temp_max"]);
            $mintemp = intval($resultapi["main"]["temp_min"]);
            $picmeteo =  $resultapi["weather"][0]["icon"];
            
        if($resultapi != NULL){ 
            if($temperature >= 15 && $condiMeteo == 'Clear' or $condiMeteo == 'Clouds' ) 
            {
                $condiMeteo = $resultapi["weather"][0]["description"];
                $resultapi["weather"][0]["main"];
                $icon = "http://openweathermap.org/img/wn/$picmeteo.png";
                $location = "ext";
                $activites = $activiteRepo->findAll();
            }
            else
            {
                $condiMeteo = $resultapi["weather"][0]["description"];
                $resultapi["weather"][0]["main"];
                $icon = "http://openweathermap.org/img/wn/$picmeteo@2x.png";
                $location = 'int';
                $activites = $activiteRepo->findBy(['location' => "$location"],);
            }
      
                  
            return $this->render('accueil/activites.html.twig', [
                'controller_name' => 'AccueilController',
                'activites'            => $activites,
                'ville'           => $ville,
                'meteo'           => $condiMeteo,
                'location'        => $location,
                'apicall'          => $apicall,
                'resultapi'        => $resultapi,
                'icon'             => $icon,
                'maxtemp'          => $maxtemp,
                'mintemp'          => $mintemp,
                'temp'             => $temperature

            ]);
        }
        else
        {
            echo "<br><br><br><h1 class=\"text-center\">Cette Ville n'existe pas !</h1>";
        }

        }
       
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'form'            => $form->createView(),

        ]);
    }

    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('accueil/conditions.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }

}