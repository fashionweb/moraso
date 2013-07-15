<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Comments_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'template' => 'index',
            'startLevel' => 1,
            'maxLevel' => 2
        );

        return $defaults;
    }

    protected function _main()
    {
        $idartlang = Aitsu_Registry::get()->env->idartlang;

        $parent_node_id = $this->_getParentNodeId($idartlang);
        
        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        /* Configuration */
        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];

        /* get Data */
        $comments = Moraso_Comments::getComments($parent_node_id, true, $defaults['startLevel'], $defaults['maxLevel']);

        /* create View */
        $view = $this->_getView();
        $view->parent_node_id = $parent_node_id;
        $view->comments = $comments;
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 0;
    }

    protected function _getParentNodeId($idartlang)
    {
        $properties = Aitsu_Persistence_ArticleProperty::factory($idartlang);

        $commentsInfo = (object) $properties->comments;

        if (!isset($commentsInfo->node_id->value) || empty($commentsInfo->node_id->value)) {
            $parent_node_id = $this->_createArticleMainNode($idartlang);
        } else {
            $parent_node_id = $commentsInfo->node_id->value;
        }

        return $parent_node_id;
    }

    protected function _createArticleMainNode($idartlang)
    {
        $properties = Aitsu_Persistence_ArticleProperty::factory($idartlang);

        $nodes_comments_main_node_id = Moraso_Config::get('nodes_comments_main_node_id');

        if (empty($nodes_comments_main_node_id)) {
            $nodes_comments_main_node_id = $this->_createMainNode();
        }

        $article_main_node_id = Moraso_Nodes::insert($nodes_comments_main_node_id, true, true);

        $properties->setValue('comments', 'node_id', $article_main_node_id);
        $properties->save();

        return $article_main_node_id;
    }

    protected function _createMainNode()
    {
        $nodes_comments_main_node_id = Moraso_Nodes::insert(null, true, true);

        $data = array(
            'config' => 'default',
            'env' => 'default',
            'identifier' => 'nodes_comments_main_node_id',
            'value' => $nodes_comments_main_node_id
        );

        Moraso_Db::put('_moraso_config', 'id', $data);

        return $nodes_comments_main_node_id;
    }

}