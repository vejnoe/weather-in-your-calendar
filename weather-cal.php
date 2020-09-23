<?php
// Setting the $appkey from a seperat file
require_once '../api_key.php'; // Get a API Key at https://openweathermap.org/appid

// Loading variables from URL
if (isset($_GET['city'])) {
  $city = $_GET['city'];
} else if (isset($_GET['zip'])) {
  $zip = $_GET['zip'];
}
if (isset($_GET['country_code'])) {
  $country_code = $_GET['country_code'];
}

if (isset($_GET['units'])) {
  $units = $_GET['units'];
} else {
  $units = "metric";
}

if (isset($_GET['location'])) {
  $location = $_GET['location'];
} else {
  $location = "show";
}

if (isset($_GET['temperature'])) {
  $temp = $_GET['temperature'];
} else {
  $temp = "day";
}

// Loading json
if (isset($zip)) {
  $string = file_get_contents("http://api.openweathermap.org/data/2.5/forecast/daily?zip=" . $zip . "," . $country_code . "&units=" . $units . "&cnt=16&appid=" . $appkey);
} else {
  $string = file_get_contents("http://api.openweathermap.org/data/2.5/forecast/daily?q=" . $city . "&units=" . $units . "&cnt=16&appid=" . $appkey);
}
$json = json_decode($string, true);

// Setting ical header
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=weather-cal.ics');

// Define helper functions
function dateToCal($timestamp) {
  return date('Ymd\THis\Z', $timestamp);
}
function dayToCal($timestamp) {
  return date('Ymd', $timestamp);
}
function nextDayToCal($timestamp) {
  return date('Ymd', strtotime('+1 day', $timestamp));
}
function iconToEmoji($icon) {
  switch ($icon) {
    case '01d': $emoji = '☀️'; break;
    case '01n': $emoji = '✨'; break;
    case '02d': case '02n': $emoji = '🌤'; break;
    case '03d': case '03n': $emoji = '☁️'; break;
    case '04d': case '04n': $emoji = '☁️'; break;
    case '09d': case '09n': $emoji = '🌧'; break;
    case '10d': case '10n': $emoji = '🌦'; break;
    case '11d': case '11n': $emoji = '⛈'; break;
    case '13d': case '13n': $emoji = '🌨'; break;
    case '50d': case '50n': $emoji = '🌫'; break;

    default: $emoji = '🤔'; break;
  }
  return $emoji;
}
function windDirectionPro($deg) {
	$directions = array('N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N');
	return $directions[round($deg / 22.5)];
}
function windDirectionArrow($deg) {
	// $directions = array('⇑', '⇗', '⇒', '⇘', '⇓', '⇙', '⇐', '⇖', '⇑');
	$directions = array('↑', '↗', '→', '↘', '↓', '↙', '←', '↖', '↑');
	return $directions[round($deg / 45)];
}
function makeDescriptions($data) {
  $desc = iconToEmoji($data['weather'][0]['icon']) . ' ' . ucfirst($data['weather'][0]['description']) . '\n\n';
  $desc .= '🌅 Sunrise ' . date("G:i", (int)$data['sunrise']) . ' and sets ' . date("G:i", (int)$data['sunset']) . '\n\n';
  $desc .= '⚡️ Pressure ' . $data['pressure'] . ' hPa\n\n';
  $desc .= '💧 Humidity ' . $data['humidity'] . '%\n\n';
  $desc .= '💨 Wind speed up to ' . (int)$data['speed'] . ' m/s\n';
  $desc .= '🚩 from ' . windDirectionPro($data['deg']) . ' ' . windDirectionArrow($data['deg']) . '\n\n\n\n';
  $desc .= 'weather.vejnoe.dk';

  return $desc;
}
function displayTemp($temp, $display) {
  if ($display == 'day') {
    return round($temp['day']) . '°';
  } else {
    return round($temp['min']) . '°/' . round($temp['max']) . '°';
  }
}

// 3. Echo out the ics file's contents
?>BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//vejnoe.dk//v0.2//EN
X-WR-CALNAME:Weather fo
 r <?= $json['city']['name'] . '
'; ?>
X-APPLE-CALENDAR-COLOR:#ffffff
CALSCALE:GREGORIAN
<?php
  // print_r($json['list']);
  // Loop throue all the days
  foreach ($json['list'] as $key => $val) {
?>
BEGIN:VEVENT
SUMMARY;LANGUAGE=en:<?= iconToEmoji($val['weather'][0]['icon']) ?> <?= displayTemp($val['temp'], $temp) . '
'; ?> 
X-FUNAMBOL-ALLDAY:1 
CONTACT:Andreas Vejnø Andersen\, andreas@vejnoe.dk 
UID:<?= dayToCal($val['dt']) ?>@vejnoe.dk 
DTSTAMP;VALUE=DATE:<?= date('Ymd\THis', time()) . '
' ?>
DTSTART;VALUE=DATE:<?= dayToCal($val['dt']) . '
' ?>
<?php if ($location == 'show') { ?>
<?= 'LOCATION:' . $json['city']['name'] . '
' ?> 
<?php } ?>
X-MICROSOFT-CDO-ALLDAYEVENT:TRUE 
URL;VALUE=URI:http://www.vejnoe.dk 
DTEND;VALUE=DATE:<?= nextDayToCal($val['dt']) . '
' ?>
X-APPLE-TRAVEL-ADVISORY-BEHAVIOR:AUTOMATIC 
DESCRIPTION;LANGUAGE=en:<?= makeDescriptions($val) . '
' ?>
END:VEVENT
<?php
  }
?>
END:VCALENDAR