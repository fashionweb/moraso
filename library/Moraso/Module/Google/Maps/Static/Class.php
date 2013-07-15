<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Maps_Static_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'mapTypeId' => 'HYBRID',
            'zoom' => 18,
            'latitude' => 52.80896,
            'longitude' => 9.142725,
            'width' => 800,
            'height' => 450
        );

        $defaults['configurable'] = array(
            'mapTypeId' => true,
            'zoom' => true,
            'latitude' => true,
            'longitude' => true,
            'width' => true,
            'height' => true
        );

        return $defaults;
    }

    protected function _trueFalseArray()
    {
        return array(
            'default' => '',
            'true' => 'true',
            'false' => 'false'
        );
    }

    protected function _main()
    {

        $defaults = $this->_moduleConfigDefaults;

        /* MapTypeId */
        if ($defaults['configurable']['MapTypeId']) {
            $mapTypeIdSelect = array(
                'default' => '',
                'This map type displays a transparent layer of major streets on satellite images.' => 'HYBRID',
                'This map type displays a normal street map.' => 'ROADMAP',
                'This map type displays satellite images.' => 'SATELLITE',
                'This map type displays maps with physical features such as terrain and vegetation.' => 'TERRAIN'
            );

            $mapTypeId = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeId', 'mapTypeId', $mapTypeIdSelect, Aitsu_Translate::_('Configuration'));
        }

        $mapTypeId = !empty($mapTypeId) ? $mapTypeId : $defaults['mapTypeId'];

        /* zoom */
        if ($defaults['configurable']['zoom']) {
            $zoom = Aitsu_Content_Config_Text::set($this->_index, 'zoom', 'zoom', 'zoom');
        }

        $zoom = !empty($zoom) ? $zoom : $defaults['zoom'];
        
        /* latitude */
        if ($defaults['configurable']['latitude']) {
            $latitude = Aitsu_Content_Config_Text::set($this->_index, 'latitude', 'latitude', 'latitude');
        }

        $latitude = !empty($latitude) ? $latitude : $defaults['latitude'];
        
        /* longitude */
        if ($defaults['configurable']['longitude']) {
            $longitude = Aitsu_Content_Config_Text::set($this->_index, 'longitude', 'longitude', 'longitude');
        }

        $longitude = !empty($longitude) ? $longitude : $defaults['longitude'];
        
        /* width */
        if ($defaults['configurable']['width']) {
            $width = Aitsu_Content_Config_Text::set($this->_index, 'width', 'width', 'width');
        }

        $width = !empty($width) ? $width : $defaults['width'];
        
        /* height */
        if ($defaults['configurable']['height']) {
            $height = Aitsu_Content_Config_Text::set($this->_index, 'height', 'height', 'height');
        }

        $height = !empty($height) ? $height : $defaults['height'];

        /* create View */
        $view = $this->_getView();
        $view->index = $this->_index;

        $view->mapTypeId = $mapTypeId;
        $view->zoom = $zoom;
        $view->latitude = $latitude;
        $view->longitude = $longitude;
        $view->width = $width;
        $view->height = $height;

        return $view->render('index.phtml');
    }

}