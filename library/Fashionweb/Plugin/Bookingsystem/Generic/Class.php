<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Plugin_Bookingsystem_Generic_Controller extends Moraso_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Fashionweb_Bookingsystem::getBookings();

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function editAction() {

        $id_request = $this->getRequest()->getParam('id_request');

        $this->_helper->layout->disableLayout();

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit request');
        $form->url = $this->view->url(array('paction' => 'edit'));

        /* status */
        $statusCollection = array();
        $statusCollection[] = (object) array(
                    'name' => 'angefragt',
                    'value' => 1
        );
        $statusCollection[] = (object) array(
                    'name' => 'gebucht',
                    'value' => 2
        );
        $statusCollection[] = (object) array(
                    'name' => 'abgelehnt',
                    'value' => 3
        );
        $form->setOptions('status', $statusCollection);

        /* get Values */
        if (!empty($id_request)) {
            $data = Fashionweb_Bookingsystem::getBooking($id_request);

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

                if (empty($data['id_request'])) {
                    unset($data['id_request']);
                }

                $id_request = Fashionweb_Bookingsystem::setRequest($data);

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

        try {
            Fashionweb_Bookingsystem::deleteRequest($this->getRequest()->getParam('id_request'));

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

    public function statusAction() {

        try {
            Fashionweb_Bookingsystem::setStatus($this->getRequest()->getParam('id_request'), $this->getRequest()->getParam('status'));

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