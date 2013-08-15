<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Cart_Mail implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if ($event->receiver['title'] === 'Herr') {
            $anrede = 'Sehr geehrter';
        } elseif ($event->receiver['title'] === 'Frau') {
            $anrede = 'Sehr geehrte';
        }

        $emailmessage = $anrede . ' ' . $event->receiver['title'] . ' ' . $event->receiver['name']['last'] . ',<br />';
        $emailmessage.= '<br />';
        $emailmessage.= 'vielen Dank für Ihre Bestellung.<br />';
        $emailmessage.= '<br />';
        $emailmessage.= 'Ihre Bestellnummer lautet ' . $event->cart->order_id . '.<br /><br />';
        $emailmessage.= 'Ihre Bestellung nochmals in der Übersicht:<br />';

        $nf = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

        $amount_total = 0;
        $amount_total_tax = array();
        $tax_total = 0;

        foreach ($event->articles as $idart => $qty) {
            $articleInfo = Aitsu_Persistence_Article::factory($idart)->load();

            $idartlang = Moraso_Util::getIdArtLang($idart);

            $articleProperties = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

            $articlePropertyCart = (object) $articleProperties->cart;

            $price_total = bcmul($articlePropertyCart->price->value, $qty, 2);

            $emailmessage.= $qty . 'x ' . $articleInfo->pagetitle . ' (' . $articlePropertyCart->sku->value . ') für ' . $nf->formatCurrency($price_total, 'EUR') . '<br />';

            $amount_total = bcadd($amount_total, $price_total, 2);

            $tax_class = (int) $articlePropertyCart->tax_class->value;

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

        $emailmessage.= '<br />';
        $emailmessage.= 'Gesamtsumme: ' . $nf->formatCurrency($amount_total, 'EUR') . '<br />';
        $emailmessage.= 'enthaltende MwSt.: ' . $nf->formatCurrency($tax_total, 'EUR') . '<br />';

        $emailmessage.= '<br />';

        $emailmessage.= '<strong>Versandadresse</strong><br />';
        $emailmessage.= $event->receiver['title'] . ' ' . $event->receiver['name']['first'] . ' ' . $event->receiver['name']['last'] . '<br />';
        $emailmessage.= $event->receiver['street'] . ' ' . $event->receiver['housenumber'] . '<br />';
        $emailmessage.= $event->receiver['postal_code'] . ' ' . $event->receiver['city'] . '<br />';

        $emailmessage.= '<br />';

        $emailmessage.= '<strong>Rechnungsadresse</strong><br />';
        if (isset($event->billing['same_than_delivery']) && $event->billing['same_than_delivery']) {
            $emailmessage.= 'Entspricht der Versandadresse<br />';
        } else {
            $emailmessage.= $event->billing['title'] . ' ' . $event->billing['name']['first'] . ' ' . $event->billing['name']['last'] . '<br />';
            $emailmessage.= $event->billing['street'] . ' ' . $event->billing['housenumber'] . '<br />';
            $emailmessage.= $event->billing['postal_code'] . ' ' . $event->billing['city'] . '<br />';
        }
        
        $transport = new Zend_Mail_Transport_Smtp(Aitsu_Config::get('email.config.host'), array(
            'auth' => Aitsu_Config::get('email.config.auth'),
            'username' => Aitsu_Config::get('email.config.username'),
            'password' => Aitsu_Config::get('email.config.password')
            ));

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom(Aitsu_Config::get('email.config.sender.mail'), Aitsu_Config::get('email.config.sender.name'));
        $mail->addTo($event->receiver['email'], $event->receiver['name']['first'] . ' ' . $event->receiver['name']['last']);
        $mail->addBcc(Aitsu_Config::get('email.config.sender.mail'));
        $mail->setSubject('Ihre Bestellung');
        $mail->setBodyHtml($emailmessage);
        $mail->send($transport);
    }
}