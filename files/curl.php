<?php

function setRequestHeaders($ch, $siteId, $token) 
{
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'wix-site-id: ' . $siteId, 'Authorization: ' . $token));
}


function getEvents($siteId, $token) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.wixapis.com/events/v1/events?limit=10");
    setRequestHeaders($ch, $siteId, $token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    else {
        // process your $result
    }
    
    curl_close($ch);
}

function updateEvent($siteId, $token, $eventId, $eventUpdated) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.wixapis.com/events/v1/events/" . $eventId);
    setRequestHeaders($ch, $siteId, $token);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

    $eventUpdatedJsonData = json_encode($eventUpdated);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $eventUpdatedJsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $decode = json_decode($result);
    }
    
    curl_close($ch);
    return $decode;
}

function copyEvent($siteId, $token, $eventId) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.wixapis.com/events/v1/events/" . $eventId . "/copy");
    setRequestHeaders($ch, $siteId, $token);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $decode = json_decode($result);
    }

    curl_close($ch);
    return $decode;
}

function importImage($siteId, $token, $image) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.wixapis.com/site-media/v1/files/import");
    setRequestHeaders($ch, $siteId, $token);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $imageJsonData = json_encode($image);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $imageJsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $decode = json_decode($result);
    }
    curl_close($ch);
    return $decode;
}