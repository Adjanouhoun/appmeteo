<?php

namespace App\Controller;

use App\Entity\Position;
use App\Service\WeatherService;
use App\Entity\OpenWeatherMapForm;
use App\Entity\Ville;
use App\Service\GeoLocationService;
use App\Repository\PositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController
{
  private $weatherService;

  public function __construct(WeatherService $weather)
  {
    $this->weatherService = $weather;
  }


  /**
   * @Route("/", name="weather")
   */
  public function index(Request $request, GeoLocationService $geoLocationService, PositionRepository $positionRepository)
  {

    $city_name = new OpenWeatherMapForm();

    $form = $this->createFormBuilder($city_name)
      ->add('city_name', TextType::class)
      ->getForm();
    // form validation
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $city_name = $form->getData();

      return $this->redirectToRoute('weather_city', [
        'city' => $city_name->getCityName(),
      ]);
    }
    $position = $positionRepository->findOneBy([]);
    $latitude = $position->getLatitude();
    $longitude = $position->getLongitude();
    //dump($longitude); die();
    $dataPosition = $this->weatherService->getWeather($latitude, $longitude);

    $hourlyForecastData = $this->weatherService->getWeatherHourly($latitude, $longitude);

    //dump($hourlyForecastData); die();

    $dayliForecastData = $this->weatherService->getWeatherDaily($latitude, $longitude);

    //dump($dayliForecastData); die();
    return $this->render('weather/index.html.twig', [
      'form' => $form->createView(),
      'data' => $dataPosition,
      'hourlyData' => $hourlyForecastData,
      'jourData' => $dayliForecastData
    ]);
  }

  /**
   * @Route("/weather/{city}", name="weather_city")
   */
  public function number($city)
  {
    $coordinates = $this->weatherService->getCoordinatesByCityName($city);
    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];
    
    $dataPosition = $this->weatherService->getWeather($latitude, $longitude);

    $hourlyForecastData = $this->weatherService->getWeatherHourly($latitude, $longitude);

    //dump($hourlyForecastData); die();

    $dayliForecastData = $this->weatherService->getWeatherDaily($latitude, $longitude);

    //dump($dayliForecastData); die();


    if (is_array($dataPosition)) {
      return $this->render('weather/result.html.twig', [
        'data' => $dataPosition,
        'hourlyData' => $hourlyForecastData,
        'jourData' => $dayliForecastData
      ]);
    } else {
      $statusCode = 0;
      $errorMessage = '';
      $e = $data;
      if (method_exists($e, 'getResponse')) {
        $statusCode = $e->getResponse()->getStatusCode();
      }
      if ($statusCode == 0) {
        $errorMessage = 'Error occurs';
      }
      if (401 == $statusCode) {
        $errorMessage = "Erreur appel API";
      }
      if (404 == $statusCode) {
        $errorMessage = "API calls return an error 404.
          You can get this error in the following cases:";
      }
      if (429 == $statusCode) {
        $errorMessage = "API calls return an error 429";
      }
      return $this->render('errors.html.twig', ['error' => $errorMessage]);
    }
  }

  /**
   * @Route("/get-city-name", name="get_city_name", methods={"POST"})
   */
  public function getCityName(Request $request, EntityManagerInterface $entityManager): Response
  {
    $data = json_decode($request->getContent(), true);

    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    // Récupérer l'enregistrement existant
    $position = $entityManager->getRepository(Position::class)->findOneBy([]);

    if (!$position) {
      // Gérer le cas où il n'y a pas d'enregistrement existant, peut-être créer un nouvel enregistrement
      $position = new Position();
    }

    // Mettre à jour les valeurs de longitude et de latitude (exemple : nouvelles valeurs aléatoires)
    $position->setLatitude($latitude);
    $position->setLongitude($longitude);

    // Enregistrer les modifications dans la base de données
    $entityManager->persist($position);
    $entityManager->flush();


    return new JsonResponse($position);
  }
}
