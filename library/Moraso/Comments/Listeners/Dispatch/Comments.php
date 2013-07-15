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
            $node_id = Moraso_Comments::create($_POST['parent_node_id'], array(
                        'author' => $_POST['author'],
                        'comment' => $_POST['comment']
                            ), true, true);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                if (!empty($node_id)) {
                    echo json_encode(array('success' => true, 'node_id' => $node_id));
                } else {
                    echo json_encode(array('success' => false));
                }
                exit();
            }
        }

        if ((isset($_POST['action']) && $_POST['action'] === 'deleteComment') && (isset($_POST['node_id']) && !empty($_POST['node_id']))) {
            $deleted = Moraso_Comments::delete($_POST['node_id'], '_nodes_comments');

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(array('success' => $deleted));
                exit();
            }
        }
        
        if ((isset($_POST['action']) && $_POST['action'] === 'activateComment') && (isset($_POST['node_id']) && !empty($_POST['node_id']))) {
            // alles was einen Eintrag aktiviert
        }
    }

}