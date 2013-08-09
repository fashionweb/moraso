<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Maps_Class extends Moraso_Module_Abstract
{
    protected function _init()
    {
        Aitsu_Util_Javascript::addReference('https://maps.googleapis.com/maps/api/js?v=3.9&sensor=false&language=de');
    }

    protected function _positionArray()
    {
        return array(
            'default' => '',
            'Elements are positioned in the center of the bottom row' => 'BOTTOM_CENTER',
            'Elements are positioned in the bottom left and flow towards the middle. Elements are positioned to the right of the Google logo.' => 'BOTTOM_LEFT',
            'Elements are positioned in the bottom right and flow towards the middle. Elements are positioned to the left of the copyrights.' => 'BOTTOM_RIGHT',
            'Elements are positioned on the left, above bottom-left elements, and flow upwards.' => 'LEFT_BOTTOM',
            'Elements are positioned in the center of the left side.' => 'LEFT_CENTER',
            'Elements are positioned on the left, below top-left elements, and flow downwards.' => 'LEFT_TOP',
            'Elements are positioned on the right, above bottom-right elements, and flow upwards.' => 'RIGHT_BOTTOM',
            'Elements are positioned in the center of the right side.' => 'RIGHT_CENTER',
            'Elements are positioned on the right, below top-right elements, and flow downwards.' => 'RIGHT_TOP',
            'Elements are positioned in the center of the top row.' => 'TOP_CENTER',
            'Elements are positioned in the top left and flow towards the middle.' => 'TOP_LEFT',
            'Elements are positioned in the top right and flow towards the middle.' => 'TOP_RIGHT'
            );
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
        /* Name */
        if ($this->_defaults['configurable']['name']) {
            $name = Aitsu_Content_Config_Text::set($this->_index, 'name', Aitsu_Translate::translate('Name'), $this->_translation['configuration']);
        }

        $name = !empty($name) ? $name : $this->_defaults['name'];

        /* Address */
        if ($this->_defaults['configurable']['address']) {
            $address = Aitsu_Content_Config_Text::set($this->_index, 'address', Aitsu_Translate::translate('Address'), $this->_translation['configuration']);
        }

        $address = !empty($address) ? $address : $this->_defaults['address'];

        /* MapTypeId */
        if ($this->_defaults['configurable']['mapTypeId']) {
            $mapTypeIdSelect = array(
                'default' => '',
                'This map type displays a transparent layer of major streets on satellite images.' => 'HYBRID',
                'This map type displays a normal street map.' => 'ROADMAP',
                'This map type displays satellite images.' => 'SATELLITE',
                'This map type displays maps with physical features such as terrain and vegetation.' => 'TERRAIN'
                );

            $mapTypeId = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeId', 'mapTypeId', $mapTypeIdSelect, $this->_translation['configuration']);
        }

        $mapTypeId = !empty($mapTypeId) ? $mapTypeId : $this->_defaults['mapTypeId'];

        /* mapTypeControl */
        if ($this->_defaults['configurable']['mapTypeControl']) {
            $mapTypeControl = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeControl', 'active', $this->_trueFalseArray(), 'mapTypeControl');
        }

        $mapTypeControl = !empty($mapTypeControl) ? filter_var($mapTypeControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['mapTypeControl'];

        /* mapTypeControlOptions */
        $mapTypeControlOptions = new stdClass();

        /* mapTypeControlOptions_position */
        if ($this->_defaults['configurable']['mapTypeControlOptions_position']) {
            $mapTypeControlOptions_position = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeControlOptions_position', 'position', $this->_positionArray(), 'mapTypeControl');
        }

        $mapTypeControlOptions->position = !empty($mapTypeControlOptions_position) ? $mapTypeControlOptions_position : $this->_defaults['mapTypeControlOptions_position'];

        /* mapTypeControlOptions_style */
        if ($this->_defaults['configurable']['mapTypeControlOptions_style']) {
            $mapTypeControlOptions_style_select = array(
                'default' => '',
                'Uses the default map type control. The control which DEFAULT maps to will vary according to window size and other factors. It may change in future versions of the API.' => 'DEFAULT',
                'A dropdown menu for the screen realestate conscious.' => 'DROPDOWN_MENU',
                'The standard horizontal radio buttons bar.' => 'HORIZONTAL_BAR',
                );

            $mapTypeControlOptions_style = Aitsu_Content_Config_Select::set($this->_index, 'mapTypeControlOptions_style', 'style', $mapTypeControlOptions_style_select, 'mapTypeControl');
        }

        $mapTypeControlOptions->style = !empty($mapTypeControlOptions_style) ? $mapTypeControlOptions_style : $this->_defaults['mapTypeControlOptions_style'];

        /* maxZoom */
        if ($this->_defaults['configurable']['maxZoom']) {
            $maxZoom = Aitsu_Content_Config_Text::set($this->_index, 'maxZoom', 'maxZoom', 'zoom');
        }

        $maxZoom = !empty($maxZoom) ? $maxZoom : $this->_defaults['maxZoom'];

        /* minZoom */
        if ($this->_defaults['configurable']['minZoom']) {
            $minZoom = Aitsu_Content_Config_Text::set($this->_index, 'minZoom', 'minZoom', 'zoom');
        }

        $minZoom = !empty($minZoom) ? $minZoom : $this->_defaults['minZoom'];

        /* overviewMapControl */
        if ($this->_defaults['configurable']['overviewMapControl']) {
            $overviewMapControl = Aitsu_Content_Config_Select::set($this->_index, 'overviewMapControl', 'active', $this->_trueFalseArray(), 'overviewMapControl');
        }

        $overviewMapControl = !empty($overviewMapControl) ? filter_var($overviewMapControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['overviewMapControl'];

        /* overviewMapControlOptions */
        $overviewMapControlOptions = new stdClass();

        /* overviewMapControlOptions_opened */
        if ($this->_defaults['configurable']['mapTypeControlOptions_position']) {
            $overviewMapControlOptions_opened = Aitsu_Content_Config_Select::set($this->_index, 'overviewMapControlOptions_opened', 'opened', $this->_trueFalseArray(), 'overviewMapControl');
        }

        $overviewMapControlOptions->opened = (!empty($overviewMapControlOptions_opened) ? filter_var($overviewMapControlOptions_opened, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['overviewMapControlOptions_opened']) ? 'true' : 'false';

        /* panControl */
        if ($this->_defaults['configurable']['panControl']) {
            $panControl = Aitsu_Content_Config_Select::set($this->_index, 'panControl', 'active', $this->_trueFalseArray(), 'panControl');
        }

        $panControl = !empty($panControl) ? filter_var($panControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['panControl'];

        /* panControlOptions */
        $panControlOptions = new stdClass();

        /* panControlOptions_position */
        if ($this->_defaults['configurable']['panControlOptions_position']) {
            $panControlOptions_position = Aitsu_Content_Config_Select::set($this->_index, 'panControlOptions_position', 'position', $this->_positionArray(), 'panControl');
        }

        $panControlOptions->position = !empty($panControlOptions_position) ? $panControlOptions_position : $this->_defaults['panControlOptions_position'];

        /* rotateControl */
        if ($this->_defaults['configurable']['rotateControl']) {
            $rotateControl = Aitsu_Content_Config_Select::set($this->_index, 'rotateControl', 'active', $this->_trueFalseArray(), 'rotateControl');
        }

        $rotateControl = !empty($rotateControl) ? filter_var($rotateControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['rotateControl'];

        /* rotateControlOptions */
        $rotateControlOptions = new stdClass();

        /* rotateControlOptions_position */
        if ($this->_defaults['configurable']['rotateControlOptions_position']) {
            $rotateControlOptions_position = Aitsu_Content_Config_Select::set($this->_index, 'rotateControlOptions_position', 'position', $this->_positionArray(), 'rotateControl');
        }

        $rotateControlOptions->position = !empty($rotateControlOptions_position) ? $rotateControlOptions_position : $this->_defaults['rotateControlOptions_position'];

        /* scaleControl */
        if ($this->_defaults['configurable']['scaleControl']) {
            $scaleControl = Aitsu_Content_Config_Select::set($this->_index, 'scaleControl', 'active', $this->_trueFalseArray(), 'scaleControl');
        }

        $scaleControl = !empty($scaleControl) ? filter_var($scaleControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['scaleControl'];

        /* scaleControlOptions */
        $scaleControlOptions = new stdClass();

        /* scaleControlOptions_position */
        if ($this->_defaults['configurable']['scaleControlOptions_position']) {
            $scaleControlOptions_position = Aitsu_Content_Config_Select::set($this->_index, 'scaleControlOptions_position', 'position', $this->_positionArray(), 'scaleControl');
        }

        $scaleControlOptions->position = !empty($scaleControlOptions_position) ? $scaleControlOptions_position : $this->_defaults['scaleControlOptions_position'];

        /* scaleControlOptions_style */
        if ($this->_defaults['configurable']['scaleControlOptions_style']) {
            $scaleControlOptions_position_style = array(
                'default' => '',
                'The standard scale control.' => 'DEFAULT'
                );

            $scaleControlOptions_style = Aitsu_Content_Config_Select::set($this->_index, 'scaleControlOptions_style', 'style', $scaleControlOptions_position_style, 'scaleControl');
        }

        $scaleControlOptions->style = !empty($scaleControlOptions_style) ? $scaleControlOptions_style : $this->_defaults['scaleControlOptions_style'];

        /* scrollwheel */
        if ($this->_defaults['configurable']['scrollwheel']) {
            $scrollwheel = Aitsu_Content_Config_Select::set($this->_index, 'scrollwheel', 'active', $this->_trueFalseArray(), 'scrollwheel');
        }

        $scrollwheel = !empty($scrollwheel) ? filter_var($scrollwheel, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['scrollwheel'];

        /* zoom */
        if ($this->_defaults['configurable']['zoom']) {
            $zoom = Aitsu_Content_Config_Text::set($this->_index, 'zoom', 'zoom', 'zoom');
        }

        $zoom = !empty($zoom) ? $zoom : $this->_defaults['zoom'];

        /* zoomControl */
        if ($this->_defaults['configurable']['zoomControl']) {
            $zoomControl = Aitsu_Content_Config_Select::set($this->_index, 'zoomControl', 'active', $this->_trueFalseArray(), 'zoomControl');
        }

        $zoomControl = !empty($zoomControl) ? filter_var($zoomControl, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['zoomControl'];

        /* zoomControlOptions */
        $zoomControlOptions = new stdClass();

        /* zoomControlOptions_position */
        if ($this->_defaults['configurable']['zoomControlOptions_position']) {
            $zoomControlOptions_position = Aitsu_Content_Config_Select::set($this->_index, 'zoomControlOptions_position', 'position', $this->_positionArray(), 'zoomControl');
        }

        $zoomControlOptions->position = !empty($zoomControlOptions_position) ? $zoomControlOptions_position : $this->_defaults['zoomControlOptions_position'];

        $this->_view->index = $this->_index;
        $this->_view->mapTypeControl = $mapTypeControl ? 'true' : 'false';
        $this->_view->mapTypeControlOptions = $mapTypeControlOptions;
        $this->_view->mapTypeId = $mapTypeId;
        $this->_view->maxZoom = $maxZoom;
        $this->_view->minZoom = $minZoom;
        $this->_view->overviewMapControl = $overviewMapControl ? 'true' : 'false';
        $this->_view->overviewMapControlOptions = $overviewMapControlOptions;
        $this->_view->panControl = $panControl ? 'true' : 'false';
        $this->_view->panControlOptions = $panControlOptions;
        $this->_view->rotateControl = $rotateControl ? 'true' : 'false';
        $this->_view->rotateControlOptions = $rotateControlOptions;
        $this->_view->scaleControl = $scaleControl ? 'true' : 'false';
        $this->_view->scaleControlOptions = $scaleControlOptions;
        $this->_view->scrollwheel = $scrollwheel ? 'true' : 'false';
        $this->_view->zoom = $zoom;
        $this->_view->zoomControl = $zoomControl ? 'true' : 'false';
        $this->_view->zoomControlOptions = $zoomControlOptions;
        $this->_view->name = $name;
        $this->_view->address = $address;

        Aitsu_Util_Javascript::add($this->_view->render('js.phtml'));
    }
}