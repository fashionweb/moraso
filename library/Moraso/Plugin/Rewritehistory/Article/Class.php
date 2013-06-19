<?php

/**
 * @author Christian Kehres
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Moraso_Plugin_Rewritehistory_Article_Controller extends Moraso_Adm_Plugin_Controller {

    const ID = '4cbd68e4-6b4c-487c-9fd7-13237f000201';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
                    'name' => 'rewritehistory',
                    'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Rewrite History'),
                    'enabled' => self :: getPosition($idart, 'rewritehistory'),
                    'position' => self :: getPosition($idart, 'rewritehistory'),
                    'id' => self :: ID
        );
    }

    public function indexAction() {

        $this->view->idart = $this->getRequest()->getParam('idart');
    }

    public function storeAction() {

        $idart = $this->getRequest()->getParam('idart');

        $data = Aitsu_Db::fetchAll("
            SELECT
                `history`.`id`,
                `history`.`url`
            FROM
                `_aitsu_rewrite_history` AS `history`
            LEFT JOIN
                `_art_lang` AS `artlang` ON `artlang`.`idartlang` = `history`.`idartlang`
            WHERE
                `artlang`.`idart` = :idart
            ORDER BY
                `history`.`id` DESC
            ", array(
                    ':idart' => $idart
        ));

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function addAction() {

        $idart = $this->getRequest()->getParam('idart');
        $value = $this->getRequest()->getParam('value');

        if (!empty($value)) {
            $idlang = Aitsu_Registry::get()->session->currentLanguage;

            $data['idartlang'] = Aitsu_Util::getIdArtLang($idart, $idlang);

            $data['url'] = $value;

            $data['manualentry'] = 1;

            Aitsu_Db :: put('_aitsu_rewrite_history', 'id', $data);
        }

        $this->_helper->json((object) array(
                    'success' => true
        ));

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('entry', APPLICATION_PATH . '/plugins/article/rewritehistory/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit rewrite Rule');
        $form->url = $this->view->url(array('plugin' => 'rewritehistory', 'paction' => 'edit'), 'aplugin');

        $data = Aitsu_Db::fetchRow("
            SELECT
                `history`.`id`,
                `history`.`url`
            FROM
                `_aitsu_rewrite_history` AS `history`
            WHERE
                `history`.`id` = :id
        ", array(
                    ':id' => $id
        ));

        $data['idart'] = $idart;
        $form->setValues($data);

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();
                $data['manualentry'] = 1;

                $idlang = Aitsu_Registry::get()->session->currentLanguage;

                $data['idartlang'] = Aitsu_Util::getIdArtLang($idart, $idlang);

                $primarykey = null;

                if (empty($data['id'])) {
                    unset($data['id']);
                }

                Aitsu_Db :: put('_aitsu_rewrite_history', 'id', $data);


                $this->_helper->json((object) array(
                            'success' => true,
                            'data' => (object) $data
                ));
            } else {
                $this->_helper->json((object) array(
                            'success' => false,
                            'errors' => $form->getErrors()
                ));
            }
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false,
                        'exception' => true,
                        'message' => $e->getMessage()
            ));
        }
    }

    public function deleteAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        Aitsu_Db::query("DELETE FROM `_aitsu_rewrite_history` WHERE `id` =:id", array(':id' => $id));

        $this->_helper->json((object) array(
                    'success' => true,
        ));
    }

}