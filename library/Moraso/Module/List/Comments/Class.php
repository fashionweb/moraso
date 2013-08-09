<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Comments_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $parent_node_id = $this->_getParentNodeId($this->_defaults['idartlang']);

        $comments = Moraso_Comments::getComments($parent_node_id, Aitsu_Adm_User::getInstance() !== NULL ? false : true, $this->_defaults['startLevel'], $this->_defaults['maxLevel']);

        $this->_view->parent_node_id = $parent_node_id;
        $this->_view->comments = $comments;
        $this->_view->user = $user;
    }

    protected function _getParentNodeId($idartlang)
    {
        $properties = Aitsu_Persistence_ArticleProperty::factory($idartlang);

        $commentsInfo = (object) $properties->comments;

        if (!isset($commentsInfo->node_id->value) || empty($commentsInfo->node_id->value)) {
            return $this->_createArticleMainNode($idartlang);
        } else {
            return $commentsInfo->node_id->value;
        }
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