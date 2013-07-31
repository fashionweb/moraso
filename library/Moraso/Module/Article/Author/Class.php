<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Author_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'template' => 'index',
            'configurable' => array(
                'template' => true
            )
        );

        return $defaults;
    }
    protected function _main()
    {
        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];
        
        $view = $this->_getView();
        $view->author = Moraso_db::fetchOneC('eternal', '' .
		'SELECT ' .
		'   author ' .
		'FROM ' .
                '   _art_meta ' .
		'WHERE ' .
		'   idartlang = :idartlang', array (
			':idartlang' => Aitsu_Registry::get()->env->idartlang
		));
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}