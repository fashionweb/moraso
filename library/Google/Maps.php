<?php

/* Hier kommt das ganze Google API */

class Google_Maps
{
    public static function getGeoData($name, $street, $house_number, $postal_code, $city, $country = 'de', $language = 'de', $sensor = 'false')
    {
        $client = new Zend_Http_Client('http://maps.googleapis.com/maps/api/geocode/json');
        $client->setParameterGet('language', $language);
        $client->setParameterGet('region', strtolower($country));
        $client->setParameterGet('sensor', $sensor);
        $client->setParameterGet('address', $name . ', ' . $street . ' ' . $house_number . ', ' . $postal_code . ' ' . $city);
        
        $response = $client->request('GET');
        
        $data = json_decode($response->getBody());
        
        $googleData = array();

        if ($data->status === 'OK') {
            $location = $data->results[0]->geometry->location;

            $googleData['lng'] = $location->lng;
            $googleData['lat'] = $location->lat;

            foreach ($data->results[0]->address_components as $comp) {
                $googleData[$comp->types[0]] = $comp->long_name;
            }
        }

        return $googleData;
    }

}