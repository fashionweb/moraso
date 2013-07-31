<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Plugin_Categorize_Article_Controller extends Moraso_Adm_Plugin_Controller
{
    const ID = '51f8e1bf-2f8c-4ebe-a032-0b75c0a8b230';

    public function init()
    {
        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($id_art)
    {
        return (object) array(
                    'name' => 'Categorize',
                    'tabname' => Aitsu_Registry::get()->Zend_Translate->translate('Categorize'),
                    'enabled' => self::getPosition($id_art, 'categorize'),
                    'position' => self::getPosition($id_art, 'categorize'),
                    'id' => self::ID
        );
    }

    public function indexAction()
    {
        $this->view->id_art = $this->getRequest()->getParam('idart');
    }

    public function addAction()
    {
        $id_art = $this->getRequest()->getParam('id_art');
        $id_category = $this->getRequest()->getParam('id_category');

        if (!empty($id_category)) {
            
            if (!is_numeric($id_category)) {
                $id_category = Moraso_Categorize::create($id_category);
            }
            
            $id_categorization = Moraso_Categorize::set($id_art, $id_category);
        }

        $this->_helper->json((object) array(
                    'success' => true,
                    'id_categorization' => $id_categorization
        ));
    }

    public function storeAction()
    {
        $id_art = $this->getRequest()->getParam('id_art');
        $categories_data = Moraso_Categorize::get($id_art);

        $categories = array();
        foreach ($categories_data as $category) {
            $categories[] = (object) $category;
        }

        $this->_helper->json((object) array(
                    'categories' => $categories
        ));
    }

    public function deleteAction()
    {
        $id_categorization = $this->getRequest()->getParam('id_categorization');

        Moraso_Categorize::delete($id_categorization);

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function categorystoreAction()
    {
        $this->_helper->json((object) array(
                    'categories' => Moraso_Categorize::get()
        ));
    }

}