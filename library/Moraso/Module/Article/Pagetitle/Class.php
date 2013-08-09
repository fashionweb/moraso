<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Pagetitle_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $pagetitle = Aitsu_Content_Text::get('Pagetitle', 0);

        if (empty($pagetitle)) {
            $pagetitle = stripslashes(Aitsu_Core_Article::factory()->pagetitle);
        }

        $this->_view->tag = $this->_defaults['tag'];
        $this->_view->pagetitle = htmlentities($pagetitle, ENT_COMPAT, 'UTF-8');
    }
}