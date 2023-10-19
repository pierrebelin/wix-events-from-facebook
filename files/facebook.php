<?php

function convertDateParseToString($date_array) 
{
    return date('Y-m-d\TH:i:s\Z', mktime($date_array['hour'], $date_array['minute'], $date_array['second'], $date_array['month'], $date_array['day'], $date_array['year'])); 
}

function retrieveEventDate($eventDate)
{
    // Récupère les deux parties de la date Facebook
    $dates = explode(' – ', $eventDate);
    $startDate = "";
    $endDate = "";

    if(count($dates) == 1) {
        // Ex: "Aug 24 at 4:00 PM – Aug 27 at 8:00 PM UTC+02"
        $date_array = date_parse($dates[0]);
        $startDate = convertDateParseToString($date_array);
        $date_array['second'] = $date_array['second'] + 1;
        $endDate = convertDateParseToString($date_array); 
        return array( $startDate, $endDate );
    } else if (startsWithNumber($dates[1])) {
        // Saturday, September 16, 2023 at 8:00 PM UTC+02
        // "Saturday, September 16, 2023 at 1:00 PM – 11:00 PM UTC+02"
        // "Friday, July 28, 2023 at 11:30 PM – 6:30 AM UTC+02"
        $date_array_start = date_parse($dates[0]);

        $startDate = convertDateParseToString($date_array_start); 
        $date_array_end = date_parse($dates[1]);
        if($date_array_start['hour'] > $date_array_end['hour']) {
            $tmp = new DateTime($date_array_start['year'] .'-'. $date_array_start['month'] .'-' . $date_array_start['day']);
            $tmp->modify('+1 day');
            $date_array_end['year'] = $tmp->format('Y');
            $date_array_end['month'] = $tmp->format('m');
            $date_array_end['day'] = $tmp->format('d');
        } else {
            $date_array_end['year'] = $date_array_start['year'];
            $date_array_end['month'] = $date_array_start['month'];
            $date_array_end['day'] = $date_array_start['day'];
        }
        $endDate = convertDateParseToString($date_array_end); 
    } else {
        // Ex: "Aug 24 at 4:00 PM – Aug 27 at 8:00 PM UTC+02"
        $date_array_start = date_parse($dates[0]);
        if($date_array_start['year'] == false){
            $date_array_start['year'] = date('Y');
        }
        $startDate = convertDateParseToString($date_array_start); 

        $date_array_end = date_parse($dates[1]);
        if($date_array_end['year'] == false){
            $date_array_end['year'] = date('Y');
        }
        $endDate = convertDateParseToString($date_array_end); 
    }
    
    return array( $startDate, $endDate );
}

function startsWithNumber($str) {
    return preg_match('/^\d/', $str) === 1;
}

function retrieveEventInfoFromFacebook($eventId) 
{
    $options = array(
        'http'=>array(
          'method'=>"GET",
          'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5
            \r\n" // i.e. An iPad 
        )
    );
    
    $context = stream_context_create($options);
    $result = file_get_contents('https://www.facebook.com/events/' . $eventId . '?active_tab=about', false, $context);
    return $result;
}

function retrieveEventFromFacebook($eventId) 
{
    $eventInfo = retrieveEventInfoFromFacebook($eventId);

    $patterntitle = '#<h1 class="([\w\W]*?)">([\w\W]*?)</h1>#';
    preg_match_all($patterntitle, $eventInfo, $titleout);
    $title = trim(strip_tags(htmlspecialchars_decode($titleout[0][0])));
    
    $patternDate = '#fbEventInfoText">([\w\W]*?)</div>#';

    preg_match_all($patternDate, $eventInfo, $fbEventInfoOut);
    $dateString = strip_tags($fbEventInfoOut[1][0]);

    $localization = null;
    if(isset($fbEventInfoOut[1][1])){
        $localization = strip_tags(htmlspecialchars_decode($fbEventInfoOut[1][1]));
    }

    $patternDescriptionDisplay = '#<span data-sigil="expose">([\w\W]*?)<span class#';
    preg_match_all($patternDescriptionDisplay, $eventInfo, $descriptionDisplayOut);
    $descriptionDisplay = $descriptionDisplayOut[1][0];
    
    $patternDescriptionShow = '#<span class="text_exposed_show">([\w\W]*?)</span></span>#';
    preg_match_all($patternDescriptionShow, $eventInfo, $descriptionShowOut);
    $descriptionShow = $descriptionShowOut[1][0];

    $fullDescription = str_replace("<br /> <br />","<br />", $descriptionDisplay . $descriptionShow);
    $fullDescription = str_replace("<br />","<p></p>", $fullDescription); // Adapt for Wix CR

    $patternEventHeader = '#id="event_header">([\w\W]*?)</div>#';
    preg_match_all($patternEventHeader, $eventInfo, $eventHeaderOut);
    $patternEventImage = '#img src="([\w\W]*?)"#';
    preg_match_all($patternEventImage, $eventHeaderOut[0][0], $eventHeaderImage);

    $eventDates = retrieveEventDate($dateString);

    $eventInfo = new EventInformation();
    $eventInfo->title = $title;
    $eventInfo->description = $fullDescription;
    $eventInfo->startDate = $eventDates[0];
    $eventInfo->endDate = $eventDates[1];
    $eventInfo->localization = $localization;
    $eventInfo->imagePath = htmlspecialchars_decode($eventHeaderImage[1][0]);

    return $eventInfo;
}

