<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Include_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        if ($this->_defaults['idart'] == Aitsu_Registry::get()->env->idart || empty($this->_defaults['idart'])) {
            $this->_withoutView = true;
        } else {
            $article = Aitsu_Persistence_Article::factory($this->_defaults['idart'], $this->_defaults['idlang'])->load();

            $this->_view->content = Moraso_Db::simpleFetch(array('index', 'value'), '_article_content', array('idartlang' => $this->_defaults['idartlang']), 999);
            $this->_view->images = Aitsu_Core_File::getFiles($this->_defaults['idartlang']);
            $this->_view->tags = $article->getTags();
            $this->_view->data = $article->getData();
        }        
    }
}