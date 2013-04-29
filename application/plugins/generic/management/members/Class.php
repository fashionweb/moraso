<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class MembersPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Moraso_Eav::getAllData('plugin_generic_management_members');

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function overviewAction() {
        
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('members', APPLICATION_PATH . '/plugins/generic/management/members/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit Members');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        if (!empty($id)) {
            $data = Moraso_Eav::getEntityData('plugin_generic_management_members', $id);

            $form->setValue('id', $id);
            $form->setValues($data);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();

                if (empty($data['id'])) {
                    $data['id'] = Moraso_Eav::createEntity();
                }

                Moraso_Eav::setValues('plugin_generic_management_members', $data);

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

        try {
            Moraso_Eav::deleteEntity($id);

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