<?php

/**
 * @author Christian Kehres <c.kehres@wellbo.de>
 * @copyright (c) 2013, wellbo <http://www.wellbo.de>
 */
class Moraso_Module_Cart_Modal_Overview_Class extends Moraso_Module_Abstract
{
    protected $_allowEdit = false;
    protected $_renderOnlyAllowed = true;

    protected function _init()
    {
        Aitsu_Registry::setExpireTime(0);
    }

    protected function _main()
    {
        $nf = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

        /* get Data */
        $cart = Moraso_Cart::getInstance();

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

            $price = $articlePropertyCart->price->value;
            $price_total = bcmul($articlePropertyCart->price->value, $qty, 2);
            $sku = $articlePropertyCart->sku->value;
            $tax_class = (int) $articlePropertyCart->tax_class->value;

            $articles[$idart] = (object) array(
                        'qty' => $qty,
                        'pagetitle' => $articleInfo->pagetitle,
                        'summary' => $articleInfo->summary,
                        'price' => $nf->formatCurrency($price, 'EUR'),
                        'sku' => $sku,
                        'tax_class' => $tax_class,
                        'mainimage' => Moraso_Html_Helper_Image::getPath($idart, $articleInfo->mainimage, 100, 100, 2),
                        'price_total' => $nf->formatCurrency($price_total, 'EUR')
            );

            $amount_total = bcadd($amount_total, $price_total, 2);

            if (isset($amount_total_tax[$tax_class])) {
                $amount_total_tax[$tax_class] = $amount_total_tax[$tax_class] + ($price_total - ($price_total / ((100 + $tax_class) / 100)));
            } else {
                $amount_total_tax[$tax_class] = $price_total - ($price_total / ((100 + $tax_class) / 100));
            }
        }

        foreach ($amount_total_tax as $tax_class => $tax_value) {
            $amount_total_tax[$tax_class] = $nf->formatCurrency($tax_value, 'EUR');

            $tax_total = $tax_total + $tax_value;
        }

        /* create View */
        $view = $this->_getView();
        $view->articles = (object) $articles;
        $view->amount_total = $nf->formatCurrency($amount_total, 'EUR');
        $view->amount_total_tax = $amount_total_tax;
        $view->amount_total_without_tax = $nf->formatCurrency($amount_total - $tax_total, 'EUR');
        echo $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return 0;
    }

}