<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Image_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        if ($this->_defaults['configurable']['width']) {
            if (!isset($this->_defaults['selects']['width'])) {
                $width = Aitsu_Content_Config_Text::set($this->_index, 'width', Aitsu_Translate::_('Width'), $this->_translation['configuration']);
            } else {
                $width_select = Aitsu_Content_Config_Select::set($this->_index, 'width', Aitsu_Translate::_('Width'), array_flip($this->_defaults['selects']['width']['names']), $this->_translation['configuration']);
                $width = $this->_defaults['selects']['width']['values'][$width_select];
            }
        }

        $this->_view->width = !empty($width) ? (int) $width : $this->_defaults['width'];

        if ($this->_defaults['configurable']['height']) {
            if (!isset($this->_defaults['selects']['height'])) {
                $height = Aitsu_Content_Config_Text::set($this->_index, 'height', Aitsu_Translate::_('Height'), $this->_translation['configuration']);
            } else {
                $height_select = Aitsu_Content_Config_Select::set($this->_index, 'height', Aitsu_Translate::_('Height'), array_flip($this->_defaults['selects']['height']['names']), $this->_translation['configuration']);
                $height = $this->_defaults['selects']['height']['values'][$height_select];
            }
        }

        $this->_view->height = !empty($height) ? (int) $height : $this->_defaults['height'];

        if ($this->_defaults['configurable']['render']) {
            $renderSelect = array(
                'skalieren' => 0,
                'zuschneiden' => 1,
                'fokussieren' => 2
                );

            $render = Aitsu_Content_Config_Select::set($this->_index, 'render', Aitsu_Translate::_('Render'), $renderSelect, $this->_translation['configuration']);
        }

        $this->_view->render = isset($render) && strlen($render) > 0 ? (int) $render : $this->_defaults['render'];

        if ($this->_defaults['configurable']['all']) {
            $showAllSelect = array(
                'show all Images' => true,
                'select single Images' => false
                );

            $all = Aitsu_Content_Config_Radio::set($this->_index, 'all', Aitsu_Translate::_('show all Images'), $showAllSelect, $this->_translation['configuration']);
        }

        $all = isset($all) && strlen($all) > 0 ? filter_var($all, FILTER_VALIDATE_BOOLEAN) : $this->_defaults['all'];

        if (!$all) {
            $images = Moraso_Content_Config_Media::set($this->_index, 'Image.Media', 'Media', $this->_defaults['idart']);
            $this->_view->selectedImages = Moraso_Persistence_View_Media::byFileName($this->_defaults['idart'], $images);
        } else {
            $this->_view->selectedImages = Moraso_Persistence_View_Media::ofSpecifiedArticle($this->_defaults['idart']);
        }

        if (!empty($this->_defaults['attr'])) {
            $attr = new Zend_Config(Moraso_Util::object_to_array($this->_defaults['attr']), array('allowModifications' => true));
        } else {
            $attr = new Zend_Config(array(), array('allowModifications' => true));
        }

        if ($this->_defaults['configurable']['attr']) {
            $attr_config = Aitsu_Content_Config_Textarea::set($this->_index, 'attr', Aitsu_Translate::_('Attributes'), $this->_translation['configuration']);
        }

        if (!empty($attr_config)) {
            $config = new Zend_Config(Moraso_Util::parseSimpleIni($attr_config)->toArray(), array('allowModifications' => true));
            $attr = $attr->merge($config);
        }

        if ($this->_defaults['configurable']['rel']) {
            $rel = Aitsu_Content_Config_Text::set($this->_index, 'rel', Aitsu_Translate::_('rel'), $this->_translation['configuration']);
        }

        $attr->rel = !empty($rel) ? $rel : (isset($this->_defaults['attr']->rel) && !empty($this->_defaults['attr']->rel)) ? $this->_defaults['attr']->rel : $this->_defaults['rel'];

        if (!isset($attr->style)) {
            $attr->style = new stdClass();
        }

        if ($this->_defaults['configurable']['style']) {
            $attr->style->self = Aitsu_Content_Config_Text::set($this->_index, 'style', Aitsu_Translate::_('Style'), $this->_translation['configuration']);
        }

        if ($this->_defaults['configurable']['float']) {
            $floatSelect = array(
                Aitsu_Translate::_('not specified') => '',
                Aitsu_Translate::_('left') => 'left',
                Aitsu_Translate::_('right') => 'right',
                Aitsu_Translate::_('none') => 'none'
                );

            $float = Aitsu_Content_Config_Select::set($this->_index, 'float', Aitsu_Translate::_('Float'), $floatSelect, $this->_translation['configuration']);
        }

        if (!empty($float)) {
            $attr->style->float = $float;
        }

        $this->_view->attributes = $attr;

        $this->_view->customFields = array();
        for ($i = 1; $i <= $this->_defaults['customFields']; $i++) {
            $this->_view->customFields[$i] = Aitsu_Content_Config_Text::set($this->_index, 'customField_' . $i, Aitsu_Translate::_('custom field') . ' #' . $i, $this->_translation['configuration']);
        }

        if (empty($this->_view->selectedImages)) {
            $this->_withoutView = true;
            return '';
        }
    }
}