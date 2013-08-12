<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_BreadCrumb_Class extends Moraso_Module_Abstract
{   
    protected $type = 'navigation';

    protected function _main()
    {
        $breadcrumbs = Aitsu_Persistence_View_Category::breadCrumb($this->_defaults['idcat']);
        unset($breadcrumbs[0]);

        foreach ($breadcrumbs as &$breadcrumb) {
            $breadcrumb = (object) $breadcrumb;
        }

        $article = Aitsu_Core_Article::factory($this->_defaults['idartlang']);

        $lastCategory = end($breadcrumbs);
        if (isset($lastCategory) && !empty($lastCategory)) {
            if ($lastCategory->startidartlang !== $article->idartlang) {
                $breadcrumbs[] = (object) array(
                    'idart' => $article->idart,
                    'name' => $article->pagetitle,
                    'url' => $lastCategory->url . '/' . $article->urlname . '.html'
                    );
            }
        }
        
        $this->_view->breadcrumbs = (object) $breadcrumbs;
    }
}