<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class GuestbookPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Fashionweb_Guestbook::getEntries();

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('guestbook', APPLICATION_PATH . '/plugins/generic/management/guestbook/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit entry');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        $activeCollection = array();
        $activeCollection[] = (object) array(
                    'name' => 'inactive',
                    'value' => 0
        );
        $activeCollection[] = (object) array(
                    'name' => 'active',
                    'value' => 1
        );
        $form->setOptions('active', $activeCollection);

        $data = Fashionweb_Guestbook::getEntry($id);

        $form->setValues($data);

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();

                Fashionweb_Guestbook::setEntry($data);

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

    public function activateAction() {

        $id = $this->getRequest()->getParam('id');
        $active = $this->getRequest()->getParam('active');

        try {            
            Fashionweb_Guestbook::setActive($id, $active);

            $this->_helper->json((object) array(
                        'success' => true
            ));
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

        try {
            Fashionweb_Guestbook::deleteEntry($id);

            $this->_helper->json((object) array(
                        'success' => true
            ));
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false,
                        'exception' => true,
                        'message' => $e->getMessage()
            ));
        }
    }

}