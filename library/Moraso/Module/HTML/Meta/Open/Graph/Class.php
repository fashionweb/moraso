<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_Open_Graph_Class extends Moraso_Module_Abstract
{
    protected $_allowEdit = false;

    protected function _main()
    {
        $idart = Aitsu_Registry::get()->env->idart;
        $idcat = Aitsu_Registry::get()->env->idcat;
        $idartlang = Aitsu_Registry::get()->env->idartlang;

        // get Data
        $article = Aitsu_Persistence_Article::factory($idart);
        $articleProperty = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

        $open_graph = (object) $articleProperty->open_graph;

        if ($article->isIndex()) {
            $url = Moraso_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idcat-' . $idcat . '}');
        } else {
            $url = Moraso_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idart-' . $idart . '}');
        }

        // set Graph Data
        $data = array(
            'og:title' => $article->pagetitle . $this->_params->title_suffix,
            'og:type' => isset($open_graph->type->value) && !empty($open_graph->type->value) ? $open_graph->type->value : 'website',
            'og:image' => Moraso_Html_Helper_Image::getPath($idart, $article->mainimage, 500, 500, 2),
            'og:url' => $url,
            'og:site_name' => $this->_params->site_name,
            'og:locale' => 'de_DE'
        );

        // create View
        $view = $this->_getView();
        $view->data = $data;
        return $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return Aitsu_Util_Date::secondsUntilEndOf('year');
    }

}