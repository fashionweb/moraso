<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_Geo_Class extends Moraso_Module_Abstract
{
    protected $_allowEdit = false;

    protected function _main()
    {
        $json = Moraso_Db::fetchOne('' .
            'SELECT ' .
            '   ggl.jsonresponse ' .
            'FROM' .
            '   _art_geolocation AS agl ' .
            'LEFT JOIN ' .
            '   _google_geolocation AS ggl ON ggl.id = agl.idlocation ' .
            'WHERE ' .
            '   agl.idartlang =:idartlang ' .
            '', array(
                ':idartlang' => $this->_defaults['idartlang']
                ));

        if (empty($json)) {
            $this->_withoutView = true;
            return '';
        }

        $location = json_decode($json);

        foreach ($location->results[0]->address_components as $address_component) {
            $type = $address_component->types[0];

            $this->_view->$type = new stdClass();
            $this->_view->$type->long_name = $address_component->long_name;
            $this->_view->$type->short_name = $address_component->short_name;
        }

        $this->_view->lat = $location->results[0]->geometry->location->lat;
        $this->_view->lng = $location->results[0]->geometry->location->lng;
    }
}