<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Guestbook_Class extends Aitsu_Module_Abstract {

    protected $_renderOnlyAllowed = true;

    protected function _main() {

        $view = $this->_getView();

        if (isset($_POST['create_guestbook_entry'])) {
            $success = self::createEntry($_POST, $this->_params->autoActive);

            if ($success) {
                $redirect = Aitsu_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idart-' . $this->_params->redirect_idart . '}');

                header("Location: " . $redirect . "");
            } else {
                $view->formData = (object) $_POST;
            }
        }

        $view->entries = self::getEntries();

        return $view->render('index.phtml');
    }

    private static function createEntry($data, $autoActive = false) {

        Moraso_Db::startTransaction();

        try {
            $data['idclient'] = Aitsu_Registry::get()->env->idclient;
            $data['created'] = date('Y-m-d H:i:s');

            if ($autoActive) {
                $data['active'] = 1;
            }

            Fashionweb_Guestbook::setEntry($data);
        } catch (Exception $e) {
            trigger_error('Gästebuch Eintrag konnte nicht gespeichert werden! ' . $e->getMessage());
            Moraso_Db::rollback();
            return false;
        }

        Moraso_Db::commit();

        return true;
    }

    private static function getEntries($limit = 1000) {

        return Fashionweb_Guestbook::getActiveEntries($limit);
    }

}