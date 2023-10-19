<?php
include 'event.php';
include 'curl.php';
include 'facebook.php';

// https://positionstack.com/documentation

// Event à copier, ne pas changer !
$wixEventId = "1e50a614-dfa4-4d66-ab9d-66ab140d3a17"; 

if(!isset($_POST['wixSiteId']) && !isset($_POST['wixToken']) && !isset($_POST['events'])) {
    var_dump("Un champ n'est pas rempli, c'est KO frérot, recommence !");
    die;
}

$wixSiteId = $_POST['wixSiteId'];
$wixToken = $_POST['wixToken'];
$events = $_POST['events'];

$allEvents = explode("\r\n", $events);
$eventIds = [];
foreach ($allEvents as $event) {
    $eventId = str_replace("https://www.facebook.com/events/", "", $event);
    if(substr($eventId, -1) == '/') {
        $eventId = rtrim($eventId, '/');
    }
    array_push($eventIds, $eventId);
}


getEvents($wixSiteId, $wixToken);

foreach ($eventIds as $eventId) {
    $eventInformation = retrieveEventFromFacebook($eventId);
    $image = buildImportImage($eventInformation->imagePath, $eventInformation->title);
    $wixImage = importImage($wixSiteId, $wixToken, $image);
    $eventUpdated = buildEventUpdate($eventId, $eventInformation, $wixImage->file->id);
    $copiedEvent = copyEvent($wixSiteId, $wixToken, $wixEventId);
    $result = updateEvent($wixSiteId, $wixToken, $copiedEvent->event->id, $eventUpdated);
    var_dump("Evenement " . $eventInformation->title . " importé !<br />");
    sleep(1);
}

var_dump("Terminé !");
