<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class EventsPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $this->_helper->json((object) array(
                    'data' => Fashionweb_Events::getEvents()
        ));
    }

    public function editAction() {

        $idevent = $this->getRequest()->getParam('idevent');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('event', APPLICATION_PATH . '/plugins/generic/management/events/forms/edit.ini');
        $form->title = Aitsu_Translate::translate('Edit event');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        $organizer = Fashionweb_Events::getAllOrganizer();

        $organizerCollection = array();
        foreach ($organizer as $row) {
            $organizerCollection[] = (object) array(
                        'name' => $row['name'],
                        'value' => $row['idorganizer']
            );
        }
        $form->setOptions('idorganizer', $organizerCollection);

        $categories = Fashionweb_Events::getCategories();

        $categoryCollection = array();
        foreach ($categories as $category) {
            $categoryCollection[] = (object) array(
                        'name' => $category['name'],
                        'value' => $category['idcategory']
            );
        }
        $form->setOptions('idcategory', $categoryCollection);

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

        $medias = Fashionweb_Events::getMedia();

        $mediaCollection = array();
        $mediaCollection[] = (object) array(
                    'name' => '--- no media selected ---',
                    'value' => 0
        );
        foreach ($medias as $media) {
            $mediaCollection[] = (object) array(
                        'name' => $media['name'],
                        'value' => $media['mediaid']
            );
        }
        $form->setOptions('media_1', $mediaCollection);
        $form->setOptions('media_2', $mediaCollection);
        $form->setOptions('media_3', $mediaCollection);

        if (!empty($idevent)) {
            $data = Fashionweb_Events::getEvent($idevent);

            $form->setValues($data);

            $organizerInfo = Fashionweb_Events::getOrganizerInfo($data['idorganizer']);

            $form->setValue('organizer_name', $organizerInfo['name']);
            $form->setValue('organizer_phone', $organizerInfo['phone']);
            $form->setValue('organizer_email', $organizerInfo['email']);
            $form->setValue('organizer_homepage', $organizerInfo['homepage']);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();

                $now = date('Y-m-d H:i:s');

                if (empty($data['idevent'])) {
                    unset($data['idevent']);

                    $data['created'] = $now;
                    $data['lastmodified'] = $now;
                    $data['idclient'] = Aitsu_Registry::get()->session->currentClient;
                }

                $data['idorganizer'] = Fashionweb_Events::getOrganizer($data);

                if (empty($data['idorganizer'])) {
                    $data['idorganizer'] = Fashionweb_Events::setOrganizer($data);
                }

                if (empty($data['endtime'])) {
                    $data['endtime'] = $data['starttime'];
                }

                Fashionweb_Events::setEvent($data);

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

        $idevent = $this->getRequest()->getParam('idevent');

        try {
            Fashionweb_Events::deleteEvent($idevent);

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

    public function activateAction() {

        $idevent = $this->getRequest()->getParam('idevent');

        try {
            Fashionweb_Events::setStatus($idevent, 1);

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

    public function deactivateAction() {

        $idevent = $this->getRequest()->getParam('idevent');

        try {
            Fashionweb_Events::setStatus($idevent, 0);

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