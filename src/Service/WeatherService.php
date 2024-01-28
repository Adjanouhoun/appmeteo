<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class WeatherService
{
  private $client;
  private $apiKey;

  public function __construct($apiKey)
  {
    $this->client = HttpClient::create();
    $this->apiKey = $apiKey;
  }

  /**
   * @return array
   */
  public function getWeather($lat, $lon)
  {
    try {
      $response = $this->client->request(
        'GET',
        'https://api.openweathermap.org/data/2.5/weather?'
          . 'lat=' . $lat
          . '&lon=' . $lon
          . '&units=metric'
          . '&lang=fr'
          . '&appid=' . $this->apiKey
      );
      $content = $response->getContent();
      $dataRaw = json_decode($content, true);
      $data = [];
      // if no error
      if (is_array($dataRaw)) {
        $data = [
          //cordinates
          'lon'            => $dataRaw['coord']['lon'],         //lontitude
          'lat'            => $dataRaw['coord']['lat'],        //latitude
          //weather
          'wid'            => $dataRaw['weather'][0]['id'],
          'condition'        => $dataRaw['weather'][0]['main'],
          'description'        => ucfirst($dataRaw['weather'][0]['description']),
          'icon_css'        => $this->icon_css($dataRaw['weather'][0]['id']),
          'icon_img'        => $this->icon_img($dataRaw['weather'][0]['icon']),
          // 'icon_custom' 		=> $this->icon_custom($dataRaw['weather'][0]['icon']),

          'base'            => $dataRaw['base'],
          //main
          'temperature'    => round($dataRaw['main']['temp']),
          'pressure'        => $dataRaw['main']['pressure'],
          'humidity'         => $dataRaw['main']['humidity'] . "%",
          'min'            => round($dataRaw['main']['temp_min']),
          'max'            => round($dataRaw['main']['temp_max']),

          //wind
          'wind_speed'    => $this->transform(0, $dataRaw['wind']['speed']),
          'wind_deg'        => $dataRaw['wind']['deg'],
          //sys
          'country_code'    => $dataRaw['sys']['country'],
          'sunrise'        => date('H\Hi', $dataRaw['sys']['sunrise']),
          'sunset'        => date('H\Hi', $dataRaw['sys']['sunset']),
          //general
          'country_id'    => $dataRaw['id'],
          'country_name'    => $dataRaw['name'],
          'code'            => $dataRaw['cod'],
          'date'            => gmdate("m-d-Y", $dataRaw['dt']),
          'day'            => $this->transform(1, gmdate("w", $dataRaw['dt']))
        ];
      }
      //dump($data); die();
      return $data;
    } catch (\Exception $e) {
      return $e;
    }
  }


  public function getWeatherHourly($lat, $lon)
  {
    try {
      $response = $this->client->request(
        'GET',
        'https://api.openweathermap.org/data/2.5/forecast?' .
          'lat=' . $lat
          . '&lon=' . $lon
          . '&units=metric' .
          '&lang=fr' .
          '&appid=' . $this->apiKey
      );
      $content = $response->getContent();
      $dataRaw = json_decode($content, true);
      $hourlyData = [];

      // Check if the required keys exist for each hourly forecast
      if (isset($dataRaw['list']) && is_array($dataRaw['list'])) {
        foreach ($dataRaw['list'] as $hourlyForecast) {

          $hourlyData[] = [
            'jour'      => date('G\H', $hourlyForecast['dt']),
            'temperature'    => round($hourlyForecast['main']['temp']),
            'condition'      => $hourlyForecast['weather'][0]['main'],
            'description'    => ucfirst($hourlyForecast['weather'][0]['description']),
            'wind_speed'     => $hourlyForecast['wind']['speed'],
            'wind_deg'       => $hourlyForecast['wind']['deg'],
            'humidity'       => $hourlyForecast['main']['humidity'] . "%",
            'icon_css'        => $this->icon_css($hourlyForecast['weather'][0]['id']),
            'icon_img'        => $this->icon_img($hourlyForecast['weather'][0]['icon']),
          ];
        }
      }

      return $hourlyData;
    } catch (\Exception $e) {
      // Log the exception or handle it appropriately
      return ['error' => 'Exception occurred', 'message' => $e->getMessage()];
    }
  }



  public function getWeatherDaily($lat, $lon)
  {
    try {
      $response = $this->client->request(
        'GET',
        'https://api.openweathermap.org/data/2.5/forecast?' .
          'lat=' . $lat
          . '&lon=' . $lon
          . '&units=metric' .
          '&lang=fr' .
          '&appid=' . $this->apiKey


      );
      $data = $response->toArray();

      // Regrouper les données par jour
      // ...

      $dailyForecast = [];
      foreach ($data['list'] as $entry) {
        $day = date('l', strtotime($entry['dt_txt']));

        // Ajouter un tableau pour chaque jour
        if (!isset($dailyForecast[$day])) {
          $dailyForecast[$day] = [
            'min_temp' => PHP_INT_MAX,  // Initialiser à une valeur maximale
            'max_temp' => PHP_INT_MIN,  // Initialiser à une valeur minimale
            'condition' => null,        // Ajouter une clé pour la condition
          ];
        }

        $weatherInfo = $entry['weather'][0] ?? null;

        if ($weatherInfo !== null) {
          $temperature = round($entry['main']['temp']);

          // Mettre à jour les températures minimale et maximale
          $dailyForecast[$day]['min_temp'] = min($dailyForecast[$day]['min_temp'], $temperature);
          $dailyForecast[$day]['max_temp'] = max($dailyForecast[$day]['max_temp'], $temperature);

          // Ajouter la première description non nulle pour chaque jour
          if ($dailyForecast[$day]['condition'] === null) {
            $dailyForecast[$day]['condition'] = [
              'main'        => $weatherInfo['main'],
              'description' => ucfirst($weatherInfo['description']),
              'icon_img'    => $this->icon_img($weatherInfo['icon']),
              'icon_css'    => $this->icon_css2($weatherInfo['id']),
            ];
          }
        }
      }

      // ...
      return $dailyForecast;



      return $dailyForecast;
    } catch (\Exception $e) {
      // Journaliser l'exception ou la gérer de manière appropriée
      return ['error' => 'Exception occurred', 'message' => $e->getMessage()];
    }
  }





  /**
   * @param string $icon 
   * The icon will retreive from OWM as default
   */
  public function icon_img($icon = null)
  {
    return 'http://openweathermap.org/img/w/' . $icon . '.png';
  }

  /**
   * @param string $code 
   * The code will generate css weather icon base on weather code from OWP
   * Required weathericons.io css
   */
  public function icon_css($code = null)
  {
    return "wi wi-owm-" . $code;
  }

  /**
   * Generates a Font Awesome icon class for weather based on OpenWeatherMap code.
   *
   * @param int $code The OpenWeatherMap weather code.
   * @return string The generated Font Awesome icon class.
   */
  public function icon_css2($code = null)
  {
    // Map OpenWeatherMap numeric codes to Font Awesome icon classes (adjust as needed)
    $iconMappings = [
      800 => 'fa-sun',     // clear sky (day)
      801 => 'fa-cloud-sun',  // few clouds (day)
      802 => 'fa-cloud',   // scattered clouds (day)
      803 => 'fa-cloud',   // broken clouds (day)
      804 => 'fa-cloud',   // overcast clouds
      701 => 'fa-smog',    // mist
      711 => 'fa-smog',    // smoke
      721 => 'fa-smog',    // haze
      731 => 'fa-dust',    // dust
      741 => 'fa-fog',     // fog
      751 => 'fa-dust',    // sand
      761 => 'fa-dust',    // dust whirls
      762 => 'fa-volcano', // volcanic ash
      771 => 'fa-wind',    // squalls
      781 => 'fa-tornado', // tornado
      200 => 'fa-bolt',    // thunderstorm with light rain
      201 => 'fa-bolt',    // thunderstorm with rain
      202 => 'fa-bolt',    // thunderstorm with heavy rain
      210 => 'fa-bolt',    // light thunderstorm
      211 => 'fa-bolt',    // thunderstorm
      212 => 'fa-bolt',    // heavy thunderstorm
      221 => 'fa-bolt',    // ragged thunderstorm
      230 => 'fa-bolt',    // thunderstorm with light drizzle
      231 => 'fa-bolt',    // thunderstorm with drizzle
      232 => 'fa-bolt',    // thunderstorm with heavy drizzle
      300 => 'fa-cloud-rain', // light intensity drizzle
      301 => 'fa-cloud-rain', // drizzle
      302 => 'fa-cloud-rain', // heavy intensity drizzle
      310 => 'fa-cloud-rain', // light intensity drizzle rain
      311 => 'fa-cloud-rain', // drizzle rain
      312 => 'fa-cloud-rain', // heavy intensity drizzle rain
      313 => 'fa-cloud-showers-heavy', // shower rain and drizzle
      314 => 'fa-cloud-showers-heavy', // heavy shower rain and drizzle
      321 => 'fa-cloud-showers-heavy', // shower drizzle
      500 => 'fa-cloud-showers-heavy', // light rain
      501 => 'fa-cloud-showers-heavy', // moderate rain
      502 => 'fa-cloud-showers-heavy', // heavy intensity rain
      503 => 'fa-cloud-showers-heavy', // very heavy rain
      504 => 'fa-cloud-showers-heavy', // extreme rain
      511 => 'fa-snowflake', // freezing rain
      520 => 'fa-cloud-showers-heavy', // light intensity shower rain
      521 => 'fa-cloud-showers-heavy', // shower rain
      522 => 'fa-cloud-showers-heavy', // heavy intensity shower rain
      531 => 'fa-cloud-showers-heavy', // ragged shower rain
      600 => 'fa-snowflake', // light snow
      601 => 'fa-snowflake', // snow
      602 => 'fa-snowflake', // heavy snow
      611 => 'fa-snowflake', // sleet
      612 => 'fa-snowflake', // light shower sleet
      613 => 'fa-snowflake', // shower sleet
      615 => 'fa-snowflake', // light rain and snow
      616 => 'fa-snowflake', // rain and snow
      620 => 'fa-snowflake', // light shower snow
      621 => 'fa-snowflake', // shower snow
      622 => 'fa-snowflake', // heavy shower snow
      701 => 'fa-smog',    // mist
      711 => 'fa-smog',    // smoke
      721 => 'fa-smog',    // haze
      731 => 'fa-dust',    // dust
      741 => 'fa-fog',     // fog
      751 => 'fa-dust',    // sand
      761 => 'fa-dust',    // dust whirls
      762 => 'fa-volcano', // volcanic ash
      771 => 'fa-wind',    // squalls
      781 => 'fa-tornado', // tornado
      800 => 'fa-sun',     // clear sky
      801 => 'fa-cloud-sun',  // few clouds
      802 => 'fa-cloud',   // scattered clouds
      803 => 'fa-cloud',   // broken clouds
      804 => 'fa-cloud',   // overcast clouds
    ];

    // Default to a generic icon class if the code is not mapped
    $defaultIconClass = 'fa-question-circle';

    // Get the corresponding Font Awesome icon class, or use the default
    $iconClass = $iconMappings[$code] ?? $defaultIconClass;

    // Return the generated Font Awesome icon class
    return "fa " . $iconClass;
  }



  /**
   * @param string $data, $type 
   * transform km/h to mp/h and week days
   */
  public function transform($type, $data)
  {
    /**
     * @param	string	$type	The type of the transformation, 0 for units, 1 for days
     * @param	string	$data	The data to be consumed
     * @return	string
     */

    if ($type == 1) {
      $days = array(
        'Dimanche',
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi'
      );
      return $days[$data];
    } else {
      // Transform m/s to km/s
      return round($data * 3600 / 1000, 2) . ' km/h';
    }
  }

  function getCoordinatesByCityName($cityName)
  {
    // Remplacez 'YourAppName' par un nom significatif pour votre application
    $appName = 'YourAppName';

    $cityNameEncoded = urlencode($cityName);
    $url = "https://nominatim.openstreetmap.org/search?format=json&q={$cityNameEncoded}&format=json&limit=1&namedetails=1&extratags=1&addressdetails=1";

    $options = [
      'http' => [
        'header' => "User-Agent: {$appName}\r\n"
      ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response !== false) {
      $data = json_decode($response, true);

      if (!empty($data)) {
        $latitude = $data[0]['lat'];
        $longitude = $data[0]['lon'];

        return ['latitude' => $latitude, 'longitude' => $longitude];
      }
    }

    return null;
  }
}
