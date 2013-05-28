<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Ticketoo_Opendata {

    public static function getBankleitzahlen($format = 'json') {

        $cache = new Aitsu_Cache_File();
        $cache->setId('ticketoo_opendata_bankleitzahlen_de');
        $cache->setCacheIfLoggedIn(true);
        $cache->setLifetime(Aitsu_Util_Date::secondsUntilEndOf('week'));

        if ($cache->isValid()) {
            return $cache->load();
        }

        $data = file_get_contents('http://opendata.ticktoo.com/api/bankleitzahlen_de/' . $format);

        $cache->save($data, array('opendata', 'ticktoo', 'bankleitzahlen'));

        return $data;
    }

    public static function getVorwahlen($format = 'json') {

        $cache = new Aitsu_Cache_File();
        $cache->setId('ticketoo_opendata_vorwahlen_de');
        $cache->setCacheIfLoggedIn(true);
        $cache->setLifetime(Aitsu_Util_Date::secondsUntilEndOf('week'));

        if ($cache->isValid()) {
            return $cache->load();
        }

        $data = file_get_contents('http://opendata.ticktoo.com/api/vorwahlen_de/' . $format);

        $cache->save($data, array('opendata', 'ticktoo', 'vorwahlen'));

        return $data;
    }

    public static function getCountries($format = 'json') {

        $cache = new Aitsu_Cache_File();
        $cache->setId('ticketoo_opendata_countries');
        $cache->setCacheIfLoggedIn(true);
        $cache->setLifetime(Aitsu_Util_Date::secondsUntilEndOf('week'));

        if ($cache->isValid()) {
            return $cache->load();
        }

        $data = file_get_contents('http://opendata.ticktoo.com/api/countries/' . $format);

        $cache->save($data, array('opendata', 'ticktoo', 'countries'));

        return $data;
    }

}