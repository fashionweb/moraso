<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Cart_Menu_Info_Class extends Aitsu_Module_Abstract
{
    protected $_allowEdit = false;
    protected $_renderOnlyAllowed = true;

    protected function _init()
    {
        Aitsu_Registry::setExpireTime(0);

        /* prüfen ob es sich um einen Ajax Request handelt */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');

            $return = array();
            $return['success'] = true; // false, wenn der Warenkorb geleert wird gibts ein false und dann wird er nicht aktualisiert, der letzte bleibt drin
            $return['qty'] = 0;
            $return['amount'] = 0;

            $nf = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

            /* get Data */
            $cart = Moraso_Cart::getInstance();

            $cartArticles = $cart->getArticles();
            foreach ($cartArticles as $idart => $qty) {
                $idartlang = Moraso_Util::getIdArtLang($idart);

                $articleProperties = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

                $articlePropertyCart = (object) $articleProperties->cart;

                $return['qty'] = $return['qty'] + $qty;
                $return['amount'] = $return['amount'] + bcmul($articlePropertyCart->price->value, $qty, 2);

                $return['success'] = true;
            }

            $return['amount'] = $nf->formatCurrency($return['amount'], 'EUR');

            echo json_encode($return);
        }
    }

}