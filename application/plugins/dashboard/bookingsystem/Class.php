<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class bookingsystemDashboardController extends Aitsu_Adm_Plugin_Controller {

    const ID = '5174fb11-d8fc-46ab-bbcb-18b6c0a8b230';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        return (object) array(
                    'name' => 'bookingsystem',
                    'tabname' => Aitsu_Translate :: _('Bookingsystem'),
                    'enabled' => true,
                    'id' => self :: ID
        );
    }

    public function indexAction() {

        $email_config_auth = Moraso_Config::get('email.config.auth');
        $email_config_username = Moraso_Config::get('email.config.username');
        $email_config_password = Moraso_Config::get('email.config.password');
        $email_config_sender_mail = Moraso_Config::get('email.config.sender.mail');
        $email_config_sender_name = Moraso_Config::get('email.config.sender.name');
        $email_config_receiver_mail = Moraso_Config::get('email.config.receiver.mail');
        $email_config_receiver_name = Moraso_Config::get('email.config.receiver.name');

        $config = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient)->load()->config;
        
        if (empty($email_config_auth)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.auth',
                'value' => 'login'
            ));
        }

        if (empty($email_config_username)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.username',
                'value' => ''
            ));
        }

        if (empty($email_config_password)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.password',
                'value' => ''
            ));
        }

        if (empty($email_config_sender_mail)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.sender.mail',
                'value' => ''
            ));
        }

        if (empty($email_config_sender_name)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.sender.name',
                'value' => ''
            ));
        }

        if (empty($email_config_receiver_mail)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.receiver.mail',
                'value' => ''
            ));
        }

        if (empty($email_config_receiver_name)) {
            Moraso_Db::put('_moraso_config', 'id', array(
                'config' => $config,
                'env' => 'default',
                'identifier' => 'email.config.receiver.name',
                'value' => ''
            ));
        }
    }

    public function storeAction() {

        $data = Fashionweb_Bookingsystem::getBookings();

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

}