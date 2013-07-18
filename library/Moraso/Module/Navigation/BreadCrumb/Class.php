<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_BreadCrumb_Class extends Moraso_Module_Abstract
{
    protected $type = 'navigation';
    protected $_allowEdit = false;

    protected function _main()
    {
        $breadcrumbs = Aitsu_Persistence_View_Category::breadCrumb();
        unset($breadcrumbs[0]);

        foreach ($breadcrumbs as &$breadcrumb) {
            $breadcrumb = (object) $breadcrumb;
        }

        $article = Aitsu_Core_Article::factory();

        $lastCategory = end($breadcrumbs);
        if ($lastCategory->startidartlang !== $article->idartlang) {
            $breadcrumbs[] = (object) array(
                        'idart' => $article->idart,
                        'name' => $article->pagetitle,
                        'url' => $lastCategory->url . '/' . $article->urlname . '.html'
            );
        }
        
        $view = $this->_getView();
        $view->breadcrumbs = (object) $breadcrumbs;
        return $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}