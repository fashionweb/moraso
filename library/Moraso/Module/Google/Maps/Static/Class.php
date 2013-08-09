<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Maps_Static_Class extends Moraso_Module_Abstract
{
    protected $_newRenderingMethode = true;

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
        /* MapTypeId */
        if ($this->_defaults['configurable']['MapTypeId']) {
            $mapTypeIdSelect = array(
                'default' => '',
                'This map type displays a transparent layer of major streets on satellite images.' => 'HYBRID',
                'This map type displays a normal street map.' => 'ROADMAP',
                'This map type displays satellite images.' => 'SATELLITE',
                'This map type displays maps with physical features such as terrain and vegetation.' => 'TERRAIN'
                );

            $mapTypeId = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeId', 'mapTypeId', $mapTypeIdSelect, $this->_translations['configuration'];
        }

        $mapTypeId = !empty($mapTypeId) ? $mapTypeId : $this->_defaults['mapTypeId'];

        /* zoom */
        if ($this->_defaults['configurable']['zoom']) {
            $zoom = Aitsu_Content_Config_Text::set($this->_index, 'zoom', 'zoom', 'zoom');
        }

        $zoom = !empty($zoom) ? $zoom : $this->_defaults['zoom'];
        
        /* latitude */
        if ($this->_defaults['configurable']['latitude']) {
            $latitude = Aitsu_Content_Config_Text::set($this->_index, 'latitude', 'latitude', 'latitude');
        }

        $latitude = !empty($latitude) ? $latitude : $this->_defaults['latitude'];
        
        /* longitude */
        if ($this->_defaults['configurable']['longitude']) {
            $longitude = Aitsu_Content_Config_Text::set($this->_index, 'longitude', 'longitude', 'longitude');
        }

        $longitude = !empty($longitude) ? $longitude : $this->_defaults['longitude'];
        
        /* width */
        if ($this->_defaults['configurable']['width']) {
            $width = Aitsu_Content_Config_Text::set($this->_index, 'width', 'width', 'width');
        }

        $width = !empty($width) ? $width : $this->_defaults['width'];
        
        /* height */
        if ($this->_defaults['configurable']['height']) {
            $height = Aitsu_Content_Config_Text::set($this->_index, 'height', 'height', 'height');
        }

        $height = !empty($height) ? $height : $this->_defaults['height'];

        $this->_view->index = $this->_index;
        $this->_view->mapTypeId = $mapTypeId;
        $this->_view->zoom = $zoom;
        $this->_view->latitude = $latitude;
        $this->_view->longitude = $longitude;
        $this->_view->width = $width;
        $this->_view->height = $height;
    }
}