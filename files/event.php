<?php


function buildImportImage($url, $name)
{
    $image = new Image();
    $image->displayName = $name;
    $image->url = $url;
    $image->mediaType = "IMAGE";
    $image->private = false;
    return $image;
}

function buildEventUpdate($eventId, $eventInformation, $wixImageId) 
{
    $scheduleConfig = new EventScheduling();
    $scheduleConfig->timeZoneId = "UTC";
    $scheduleConfig->scheduleTbd = false;
    $scheduleConfig->endDateHidden = false;
    $scheduleConfig->showTimeZone = false;
    $scheduleConfig->startDate = $eventInformation->startDate;
    $scheduleConfig->endDate = $eventInformation->endDate;
    
    $coordinates = new EventCoordinates();
    $coordinates->lat = 0;
    $coordinates->long = 0;
    
    $location = new EventLocation();
    $location->name = $eventInformation->localization;
    $location->address = $eventInformation->localization;
    $location->coordinates = $coordinates;
    $location->type = "VENUE";
    
    $mainImage = new EventImage();
    $mainImage->id = $wixImageId;
    $mainImage->width = 500;
    $mainImage->height = 500;
    
    $event = new Event();
    $event->title = $eventInformation->title;
    $event->description = "https://www.facebook.com/events/" . $eventId;
    $event->about = $eventInformation->description;
    $event->scheduleConfig = $scheduleConfig;
    $event->location = $location;
    $event->mainImage = $mainImage;
    
    $params = new stdClass();
    $params->event = $event;

    $paths = array(
        "event.title",
        "event.description",
        "event.about",
        "event.scheduleConfig",
        "event.mainImage"
    );

    if($eventInformation->localization != null){
        array_push($paths, "event.location");
    }

    $params->fields = array(
        "paths" => $paths
    );

    return $params;
}



class Image {
    public $mimeType;
    public $displayName;
    public $url;
    public $mediaType;
    public $private;
    public $urlHeaders;
}

class Event {
    public $scheduleConfig; // ScheduleConfig
    public $location;
    public $title;
    public $description;
    public $about;
    public $mainImage;
}

class EventScheduling {
    public $scheduleTbd;
    public $startDate;
    public $endDate;
    public $timeZoneId; 
    public $showTimeZone; 
    public $endDateHidden; 
}

class EventImage {
    public $id;
    public $width;
    public $height;
}

class EventCoordinates {
    public $lat;
    public $long;
}

class EventLocation {
    public $name;
    public $address;
    public $coordinates;
    public $type; // "VENUE"
}





// LOCAL
class EventInformation {
    public $title;
    public $description;
    public $startDate;
    public $endDate;
    public $localization;
    public $imagePath;
}
