<?php
/*
Plugin Name: Peregum Weather Plugin
Plugin URI: http://xyzscripts.com/wordpress-plugins/insert-php-code-snippet/
Description: This is a Weather plugin to give weather informations about Peregum's weather stations in Perus/Poá in Sao Paulo.       
Version: 1.0.0
Author: marilia.dev.br
Author URI: http://marilia.dev.br
Text Domain: peregum-weather
License: GPLv2 or later
*/

if (!defined('ABSPATH')) exit;

function getInfo(string $token, int $id) {
    $currentDate = date('d/m/Y H:i:s', time());
    $options = ['headers' => ['access_token' => $token]];
    $request = wp_remote_get('https://plugfield.com.br/api/currentWeather?deviceId='.$id, $options);
    $result = json_decode($request['body']);
    $results = $result->currentWeatherList;
    return $results;
}

function uniqueDate(array $array, int $initial = 0,int $limit = 3) : array {
  $currentDate = '';
  $days = [];
  foreach($array as $result) : 
    $day = date('d', $result->dt);
    if($currentDate === $day) {
      continue;
    }
    $currentDate = $day;
    array_push($days, $result);
  endforeach;
  return array_slice($days, $initial, $limit);
}


function poaStation() {
    $results1 = getInfo('', '1474');
    $currentDayPoa = uniqueDate($results1, 0, 1)[0];
    $nextDaysPoa = uniqueDate($results1, 1, 3);
    
    ?>
<div>
  <div class="weather-widget_heading">
    <h2>
      O clima na estação Poá
    </h2>
    <h3>Estação Metereológica
      Peregum Uneafro/11 de
      Agosto</h3>
  </div>
  <div class="weather-widget_item">
    <div class="weather-widget_today">
      <p>
        Agora <br>
        <?= date('H:i') ?>
      </p>
      <div class="weather-now">
        <?php echo "<img src=\"http://openweathermap.org/img/wn/{$currentDayPoa->weatherList[0]->icon}@2x.png\" alt=\"\">"; ?>
        <p><?= $currentDayPoa->main->temperature; ?>°C</p>
      </div>
    </div>
    <div class="weather-widget_upcoming-days">
      <?php foreach($nextDaysPoa as $result)  : $weather = $result->weatherList[0]->icon; ?>
      <div class="weather-widget_upcoming-days_box">
        <h3><?= date('d/m', $result->dt); ?></h3>
        <div class="icon">
          <?php echo "<img src=\"http://openweathermap.org/img/wn/{$weather}@2x.png\" alt=\"\">"; ?>
        </div>
        <p>
          Min: <?= $result->main->tempMin; ?>°C <br>Max: <?= $result->main->tempMax; ?>°C
        </p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php
}

add_shortcode('poa-weather', 'poaStation');

function perusStation() {
    $results2 = getInfo('', '1511');
    $currentDayPerus = uniqueDate($results2, 0, 1)[0];
    $nextDaysPerus = uniqueDate($results2, 1, 3);
?>
<div>
  <div class="weather-widget_heading">
    <h2>
      O clima na estação Perus
    </h2>
    <h3>Estação Metereológica
      Peregum Uneafro/Quilombaque</h3>
  </div>
  <div class="weather-widget_item">
    <div class="weather-widget_today">
      <p>
        Agora <br>
        <?= date('H:i') ?>
      </p>
      <div class="weather-now">
        <?php echo "<img src=\"http://openweathermap.org/img/wn/{$currentDayPerus->weatherList[0]->icon}@2x.png\" alt=\"\">"; ?>
        <p><?= $currentDayPerus->main->temperature; ?>°C</p>
      </div>
    </div>
    <div class="weather-widget_upcoming-days">
      <?php foreach($nextDaysPerus as $result)  : $weather = $result->weatherList[0]->icon; ?>
      <div class="weather-widget_upcoming-days_box">
        <h3><?= date('d/m', $result->dt); ?></h3>
        <div class="icon">
          <?php echo "<img src=\"http://openweathermap.org/img/wn/{$weather}@2x.png\" alt=\"\">"; ?>
        </div>
        <p>
          Min: <?= $result->main->tempMin; ?>°C <br>Max: <?= $result->main->tempMax; ?>°C
        </p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php
}
add_shortcode('perus-weather', 'perusStation');


// functions

function bot(){
    date_default_timezone_set('America/Sao_Paulo');
}

function styles(){
    wp_enqueue_style('weather-style', plugins_url('/css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'styles');

add_action('init', 'bot');