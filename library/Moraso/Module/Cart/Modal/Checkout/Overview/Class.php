<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Cart_Modal_Checkout_Overview_Class extends Moraso_Module_Abstract
{
    protected $_allowEdit = false;
    protected $_renderOnlyAllowed = true;

    protected function _init()
    {
        Aitsu_Registry::setExpireTime(0);

        $nf = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

        /* get Data */
        $cart = Moraso_Cart::getInstance();
        $properties = $cart->getProperties();
        $cartArticles = $cart->getArticles();

        $amount_total = 0;
        $amount_total_tax = array();
        $tax_total = 0;

        $articles = array();
        foreach ($cartArticles as $idart => $qty) {
            $articleInfo = Aitsu_Persistence_Article::factory($idart)->load();

            $idartlang = Moraso_Util::getIdArtLang($idart);

            $articleProperties = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

            $articlePropertyCart = (object) $articleProperties->cart;

            $price = $nf->formatCurrency($articlePropertyCart->price->value, 'EUR');
            $price_total = bcmul($articlePropertyCart->price->value, $qty, 2);
            $sku = $articlePropertyCart->sku->value;

            $articles[$idart] = (object) array(
                        'qty' => $qty,
                        'pagetitle' => $articleInfo->pagetitle,
                        'price' => $price,
                        'sku' => $sku,
                        'price_total' => $nf->formatCurrency($price_total, 'EUR')
            );

            $amount_total = bcadd($amount_total, $price_total, 2);

            $tax_class = (int) $articlePropertyCart->tax_class->value;

            if (isset($amount_total_tax[$tax_class])) {
                $amount_total_tax[$tax_class] = $amount_total_tax[$tax_class] + ($price_total - ($price_total / ((100 + $tax_class) / 100)));
            } else {
                $amount_total_tax[$tax_class] = $price_total - ($price_total / ((100 + $tax_class) / 100));
            }
        }

        $cart->createOrder();

        $paymentStrategy = Moraso_Cart::getPaymentStrategy(null, $properties['payment']['method']);

        $payment = new Moraso_Cart_Payment($paymentStrategy);

        $checkoutUrl = $payment->getCheckoutUrl();
        $hiddenFields = $payment->getHiddenFormFields();

        foreach ($amount_total_tax as $tax_class => $tax_value) {
            $amount_total_tax[$tax_class] = $nf->formatCurrency($tax_value, 'EUR');
            
            $tax_total = $tax_total + $tax_value;
        }

        /* create View */
        $view = $this->_getView();
        $view->checkoutUrl = $checkoutUrl;
        $view->hiddenFields = $hiddenFields;
        $view->properties = $properties;
        $view->articles = $articles;
        $view->amount_total = $nf->formatCurrency($amount_total, 'EUR');
        $view->amount_total_tax = $amount_total_tax;
        $view->amount_total_without_tax = $nf->formatCurrency($amount_total - $tax_total, 'EUR');
        echo $view->render('index.phtml');
    }

}