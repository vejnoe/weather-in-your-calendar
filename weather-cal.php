<?php
// Variables used in this script:
$appkey = ''; // Get a API Key at https://openweathermap.org/appid
$city = $_GET['city'];
$summary = 'Weather for your calendar — Vejnø';

// Loading json
$string = file_get_contents("http://api.openweathermap.org/data/2.5/forecast/daily?q=" . $city . "&units=metric&cnt=16&appid=" . $appkey);
$json = json_decode($string, true);
//
// Notes:
//  - the UID should be unique to the event, so in this case I'm just using
//    uniqid to create a uid, but you could do whatever you'd like.
//
//  - iCal requires a date format of "yyyymmddThhiissZ". The "T" and "Z"
//    characters are not placeholders, just plain ol' characters. The "T"
//    character acts as a delimeter between the date (yyyymmdd) and the time
//    (hhiiss), and the "Z" states that the date is in UTC time. Note that if
//    you don't want to use UTC time, you must prepend your date-time values
//    with a TZID property. See RFC 5545 section 3.3.5
//
//  - The Content-Disposition: attachment; header tells the browser to save/open
//    the file. The filename param sets the name of the file, so you could set
//    it as "my-event-name.ics" or something similar.
//
//  - Read up on RFC 5545, the iCalendar specification. There is a lot of helpful
//    info in there, such as formatting rules. There are also many more options
//    to set, including alarms, invitees, busy status, etc.
//
//      https://www.ietf.org/rfc/rfc5545.txt
// 1. Set the correct headers for this file
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=weather-cal.ics');
// 2. Define helper functions
// Converts a unix timestamp to an ics-friendly format
// NOTE: "Z" means that this timestamp is a UTC timestamp. If you need
// to set a locale, remove the "\Z" and modify DTEND, DTSTAMP and DTSTART
// with TZID properties (see RFC 5545 section 3.3.5 for info)
//
// Also note that we are using "H" instead of "g" because iCalendar's Time format
// requires 24-hour time (see RFC 5545 section 3.3.12 for info).
function dateToCal($timestamp) {
  return date('Ymd\THis\Z', $timestamp);
}
function dayToCal($timestamp) {
  return date('Ymd', $timestamp);
}
function nextDayToCal($timestamp) {
  return date('Ymd', strtotime('+1 day', $timestamp));
}
// Escapes a string of characters
function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}
// 3. Echo out the ics file's contents
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//vejnoe.dk//v0.1//EN
X-WR-CALNAME:Weather for <?= $city . '
' ?>
X-APPLE-CALENDAR-COLOR:#ffffff
CALSCALE:GREGORIAN

<?php
//print_r($json['list']);
foreach ($json['list'] as $key => $val) {
  //print_r($val);
  switch ($val['weather'][0]['icon']) {
  	case '01d':
  		$icon = '☀️';
  		break;
  	case '02d':
  		$icon = '🌤';
  		break;
  	case '03d':
  		$icon = '☁️';
  		break;
  	case '04d':
  		$icon = '☁️☁️';
  		break;
  	case '09d':
  		$icon = '🌧';
  		break;
  	case '10d':
  		$icon = '🌦';
  		break;
  	case '11d':
  		$icon = '⛈';
  		break;
  	case '13d':
  		$icon = '🌨';
  		break;
  	case '50d':
  		$icon = '🌫';
  		break;
  	default:
  		$icon = '🤔';
  		break;
  }
	?>

BEGIN:VEVENT
SUMMARY;LANGUAGE=en:<?= $icon ?> <?= round($val['temp']['day']); ?>°
X-FUNAMBOL-ALLDAY:1
CONTACT:Andreas Vejnø Andersen\, andreas@vejnoe.dk
UID:<?= dayToCal($val['dt']) ?>@vejnoe.dk
DTSTART;VALUE=DATE:<?= dayToCal($val['dt']) . '
' ?>
LOCATION:<?= $city . '
' ?>
X-MICROSOFT-CDO-ALLDAYEVENT:TRUE
URL;VALUE=URI:http://www.vejnoe.dk
DTEND;VALUE=DATE:<?= nextDayToCal($val['dt']) . '
' ?>
X-APPLE-TRAVEL-ADVISORY-BEHAVIOR:AUTOMATIC
DESCRIPTION;LANGUAGE=en:<?= $val['weather'][0]['main'] . ': ' . $val['weather'][0]['description'] . '
' ?>
END:VEVENT
<?php
	}
?>


END:VCALENDAR
