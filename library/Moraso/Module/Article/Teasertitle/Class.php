<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Teasertitle_Class extends Moraso_Module_Abstract
{
    protected $_newRenderingMethode = true;

    protected function _main()
    {
        $teasertitle = Aitsu_Content_Text::get('Teasertitle', 0);

        if (empty($teasertitle)) {
            $teasertitle = stripslashes(Aitsu_Core_Article::factory()->teasertitle);
        }

        $this->_view->tag = $this->_defaults['tag'];
        $this->_view->teasertitle = htmlentities($teasertitle, ENT_COMPAT, 'UTF-8');
    }
}