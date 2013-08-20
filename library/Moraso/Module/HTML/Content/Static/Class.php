<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Content_Static_Class extends Moraso_Module_Abstract
{
	protected function _init()
	{		
		$this->_withoutView = true;

		$template = Aitsu_Content_Config_Radio::set($this->_index, 'HtmlStaticTemplate', '', $this->_getTemplates(), 'Template');
		
		if (empty($template)) {
			$template = 'index';
		}

		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry::isEdit()) {
			$startTag = '<div>';
			$endTag = '</div>';
		}
		
		return $startTag . preg_replace('/<\\!-{2}.*?\\-{2}>\\s*/s', '', $this->_view->render($template . '.phtml')) . $endTag;
	}
}