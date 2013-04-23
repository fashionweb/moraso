<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class AssetsPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _assets ' .
                        'where ' .
                        '   idclient =:idclient ' .
                        'order by ' .
                        '   created desc', array(
                    ':idclient' => Aitsu_Registry :: get()->session->currentClient
        ));

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function overviewAction() {
        
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('event', APPLICATION_PATH . '/plugins/generic/management/assets/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit asset');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        /* media */
        $medias = Aitsu_Db :: fetchAll('' .
                        'select ' .
                        '   media.mediaid as mediaid, ' .
                        '   description.name as name ' .
                        'from _media media ' .
                        'left join ' .
                        '   _media_description description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '	(media.idart = :idart or media.idart is null)' .
                        '	and media.deleted is null ' .
                        '	and media.mediaid in (' .
                        '		select ' .
                        '			max(media.mediaid) ' .
                        '		from _media media ' .
                        '		where ' .
                        '			(idart = :idart or idart is null)' .
                        '		group by' .
                        '			filename ' .
                        '	) ' .
                        'order by ' .
                        '	description.name desc', array(
                    ':idart' => Aitsu_Config::get('assets.media.source'),
                    ':idlang' => Aitsu_Registry :: get()->session->currentLanguage
        ));

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

        /* active / inactive */
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

        /* get Values */
        if (!empty($id)) {
            $data = Aitsu_Db::fetchRow('' .
                            'select ' .
                            '   * ' .
                            'from ' .
                            '   _assets ' .
                            'where ' .
                            '   id =:id', array(
                        ':id' => $id
            ));

            $data['media_1'] = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   mediaid ' .
                            'from ' .
                            '   _assets_have_media ' .
                            'where ' .
                            '   idasset =:id ' .
                            'order by ' .
                            '   mediaid asc ' .
                            'limit ' .
                            '   0, 1', array(
                        ':id' => $id
            ));

            $data['media_2'] = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   mediaid ' .
                            'from ' .
                            '   _assets_have_media ' .
                            'where ' .
                            '   idasset =:id ' .
                            'order by ' .
                            '   mediaid asc ' .
                            'limit ' .
                            '   1, 1', array(
                        ':id' => $id
            ));

            $data['media_3'] = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   mediaid ' .
                            'from ' .
                            '   _assets_have_media ' .
                            'where ' .
                            '   idasset =:id ' .
                            'order by ' .
                            '   mediaid asc ' .
                            'limit ' .
                            '   2, 1', array(
                        ':id' => $id
            ));

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

                trigger_error(print_r($data, true));
                
                $now = date('Y-m-d H:i:s');

                if (empty($data['id'])) {
                    unset($data['id']);

                    $data['created'] = $now;
                    $data['lastmodified'] = $now;
                    $data['idclient'] = Aitsu_Registry :: get()->session->currentClient;
                }

                $id = Aitsu_Db::put('_assets', 'id', $data);

                /* set media */
                Aitsu_Db::query('delete from _assets_have_media where idasset =:idasset', array(
                    ':idasset' => $id
                ));

                if (!empty($data['media_1'])) {
                    $mediaData = array(
                        'idasset' => $id,
                        'mediaid' => $data['media_1']
                    );
                    trigger_error('Media 1 wird gesetzt');
                    Aitsu_Db::put('_assets_have_media', 'id', $mediaData);
                }

                if (!empty($data['media_2'])) {
                    $mediaData = array(
                        'idasset' => $id,
                        'mediaid' => $data['media_2']
                    );
                    trigger_error('Media 2 wird gesetzt');
                    Aitsu_Db::put('_assets_have_media', 'id', $mediaData);
                }

                if (!empty($data['media_3'])) {
                    $mediaData = array(
                        'idasset' => $id,
                        'mediaid' => $data['media_3']
                    );
                    trigger_error('Media 3 wird gesetzt');
                    Aitsu_Db::put('_assets_have_media', 'id', $mediaData);
                }

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
            Aitsu_Db::query('' .
                    'delete from ' .
                    '   _assets ' .
                    'where ' .
                    '   id =:id', array(
                ':id' => $id
            ));

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

        $id = $this->getRequest()->getParam('id');

        try {
            Aitsu_Db::put('_assets', 'id', array(
                'id' => $id,
                'active' => 1
            ));

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

        $id = $this->getRequest()->getParam('id');

        try {
            Aitsu_Db::put('_assets', 'id', array(
                'id' => $id,
                'active' => 0
            ));

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