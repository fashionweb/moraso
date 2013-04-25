<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class QuotesPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $this->_helper->json((object) array(
                    'data' => Fashionweb_Quote::getQuotes()
        ));
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('quote', APPLICATION_PATH . '/plugins/generic/management/quotes/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit quote');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        $classesFromConfig = Moraso_Config::get('quotes.config.classes');

        $classes = array();
        foreach ($classesFromConfig as $key => $value) {
            $classes[] = array(
                'name' => $value,
                'value' => $key
            );
        }
        $form->setOptions('class', $classes);

        if (!empty($id)) {
            $data = Fashionweb_Quote::getQuote($id);

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

                $id = Fashionweb_Quote::setQuote($data);

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
            Fashionweb_Quote::deleteQuote($id);

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