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
            $pagetitle = stripslashes(Aitsu_Core_Article::factory()->teaertitle);
        }

        $view = $this->_getView();

        $view->tag = $this->_params->tag;
        $view->pagetitle = htmlentities($pagetitle, ENT_COMPAT, 'UTF-8');

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}