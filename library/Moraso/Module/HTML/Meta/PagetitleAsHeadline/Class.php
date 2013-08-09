<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_PagetitleAsHeadline_Class extends Moraso_Module_Abstract {

    protected function _init() {
        if (Aitsu_Application_Status::isEdit()) {
            echo '>>> MODUL DEPRECTED <<< Bitte anstelle des Moduls "HTML.Meta.PagetitleAsHeadline", das Modul "Article.Pagetitle" nutzen! DANKE';
        }
    }

    protected function _main() {

        $headline = Aitsu_Content_Text::get('Headline', 0);

        if (empty($headline)) {
            $headline = stripslashes(Aitsu_Core_Article::factory()->pagetitle);
        }

        $view = $this->_getView();

        $view->tag = $this->_params->tag;
        $view->headline = htmlentities($headline, ENT_COMPAT, 'UTF-8');

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}