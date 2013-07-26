<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Plugin_Addresses_Generic_Controller extends Moraso_Adm_Plugin_Controller
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        header("Content-type: text/javascript");
    }

    public function storeAction()
    {
        $addresses = Moraso_Addresses::get();

        $total = count((array) $addresses->userInput);

        $this->_helper->json((object) array(
                    'message' => $total . ' Adressen wurden geladen',
                    'addresses' => (array) $addresses->userInput,
                    'success' => true,
                    'total' => $total
        ));
    }

    public function editAction()
    {
        $address_id = $this->getRequest()->getParam('address_id');

        $this->_helper->layout->disableLayout();

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/address.ini');
        $form->title = Aitsu_Translate::translate('Edit Address');
        $form->url = $this->view->url(array('paction' => 'edit'));

        $addresses_groups = Moraso_Addresses::getGroups();

        $groups = array();
        foreach ($addresses_groups as $addresses_group) {
            $groups[] = (object) array(
                        'value' => $addresses_group['addresses_group_id'],
                        'name' => $addresses_group['name']
            );
        }

        $form->setOptions('groups', $groups);

        $country_list = Moraso_Db::fetchAll('SELECT * FROM _geo_country');

        $countries = array();
        foreach ($country_list as $country) {
            $countries[] = (object) array(
                        'value' => $country['iso2'],
                        'name' => str_replace("'", "\'", $country['en'])
            );
        }

        $form->setOptions('country', $countries);

        $address = Moraso_Addresses::get($address_id);

        if ($address) {
            $values = array();

            $googleOutput = (array) $address->googleOutput;
            foreach ($googleOutput as $key => $value) {
                $values['google_' . $key] = $value;
            }

            $userInput = (array) $address->userInput;
            foreach ($userInput as $key => $value) {
                $values[$key] = $value;
            }

            $values['groups'] = (array) $address->groups;

            $form->setValues($values);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {
                $address = $form->getValues();

                Moraso_Addresses::set($address['name'], $address['street'], $address['house_number'], $address['postal_code'], $address['city'], $address['country'], $address['address_id'], $address['groups']);

                $this->_helper->json((object) array(
                            'success' => true,
                            'address' => (object) $address
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

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();

        Moraso_Addresses::delete($this->getRequest()->getParam('address_id'));

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

}