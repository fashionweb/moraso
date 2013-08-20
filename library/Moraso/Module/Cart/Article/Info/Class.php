<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Cart_Article_Info_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $nf = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

        $article = Aitsu_Persistence_ArticleProperty::factory($this->_defaults['idartlang'])->load();

        $cart = (object) $article->cart;

        $price = $cart->price->value;

        $this->_view->cart = (object) array(
            'sku' => $cart->sku->value,
            'price' => $nf->formatCurrency($price, 'EUR'),
            'tax_class' => $cart->tax_class->value,
            'tax' => $nf->formatCurrency($cart->tax_class->value * $price / 100, 'EUR'),
            'price_net' => $nf->formatCurrency($price - $tax, 'EUR')
            );
    }
}