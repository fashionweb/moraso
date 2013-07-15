<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Comments_Listeners_Dispatch_Comments implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if ((isset($_POST['action']) && $_POST['action'] === 'addComment') && (isset($_POST['parent_node_id']) && !empty($_POST['parent_node_id']))) {
            Moraso_Comments::create($_POST['parent_node_id'], array(
                'author' => $_POST['author'],
                'comment' => $_POST['comment']
            ));
        }
    }

}