<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Comments_Listeners_Dispatch_Comments implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        $spamProtectionSeconds = 10;

        if ((isset($_POST['action']) && $_POST['action'] === 'addComment') && (isset($_POST['parent_node_id']) && !empty($_POST['parent_node_id']))) {
            if (!is_numeric($_POST['protect']) && base64_decode($_POST['protect'], true) && (time() - $spamProtectionSeconds >= base64_decode($_POST['protect']))) {
                $_POST['node_id'] = Moraso_Comments::create($_POST['parent_node_id'], array(
                            'author' => $_POST['author'],
                            'comment' => $_POST['comment']
                                ), true, true);
                
                $_POST['success'] = true;
                
            } else {
                $_POST['success'] = false;
                $_POST['spam'] = true;
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($_POST);
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