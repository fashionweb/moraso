<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Teasertitle_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $teasertitle = Aitsu_Content_Text::get('Teasertitle', 0);

        if (empty($teasertitle)) {
            $teasertitle = stripslashes(Aitsu_Core_Article::factory()->teasertitle);
        }

        $view = $this->_getView();

        $view->tag = $this->_params->tag;
        $view->teasertitle = htmlentities($teasertitle, ENT_COMPAT, 'UTF-8');

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}