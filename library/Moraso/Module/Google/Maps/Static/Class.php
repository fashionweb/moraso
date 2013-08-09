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
        $this->_view->index = $this->_index;

        /* MapTypeId */
        if ($this->_defaults['configurable']['MapTypeId']) {
            $mapTypeIdSelect = array(
                'default' => '',
                'This map type displays a transparent layer of major streets on satellite images.' => 'HYBRID',
                'This map type displays a normal street map.' => 'ROADMAP',
                'This map type displays satellite images.' => 'SATELLITE',
                'This map type displays maps with physical features such as terrain and vegetation.' => 'TERRAIN'
                );

            $mapTypeId = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeId', 'mapTypeId', $mapTypeIdSelect, $this->_translations['configuration']);
        }

        $this->_view->mapTypeId = !empty($mapTypeId) ? $mapTypeId : $this->_defaults['mapTypeId'];

        /* zoom */
        if ($this->_defaults['configurable']['zoom']) {
            $zoom = Aitsu_Content_Config_Text::set($this->_index, 'zoom', 'zoom', 'zoom');
        }

        $this->_view->zoom = !empty($zoom) ? $zoom : $this->_defaults['zoom'];
        
        /* latitude */
        if ($this->_defaults['configurable']['latitude']) {
            $latitude = Aitsu_Content_Config_Text::set($this->_index, 'latitude', 'latitude', 'latitude');
        }

        $this->_view->latitude = !empty($latitude) ? $latitude : $this->_defaults['latitude'];
        
        /* longitude */
        if ($this->_defaults['configurable']['longitude']) {
            $longitude = Aitsu_Content_Config_Text::set($this->_index, 'longitude', 'longitude', 'longitude');
        }

        $this->_view->longitude = !empty($longitude) ? $longitude : $this->_defaults['longitude'];
        
        /* width */
        if ($this->_defaults['configurable']['width']) {
            $width = Aitsu_Content_Config_Text::set($this->_index, 'width', 'width', 'width');
        }

        $this->_view->width = !empty($width) ? $width : $this->_defaults['width'];
        
        /* height */
        if ($this->_defaults['configurable']['height']) {
            $height = Aitsu_Content_Config_Text::set($this->_index, 'height', 'height', 'height');
        }

        $this->_view->height = !empty($height) ? $height : $this->_defaults['height'];
    }
}