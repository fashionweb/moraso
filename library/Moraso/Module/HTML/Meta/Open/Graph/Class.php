<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_Open_Graph_Class extends Moraso_Module_Abstract
{
    protected $_allowEdit = false;

    protected function _main()
    {
        $idart = Aitsu_Registry::get()->env->idart;
        $idcat = Aitsu_Registry::get()->env->idcat;
        $idartlang = Aitsu_Registry::get()->env->idartlang;

        // get Data
        $article = Aitsu_Persistence_Article::factory($idart);
        $articleProperty = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

        $open_graph = (object) $articleProperty->open_graph;

        if ($article->isIndex()) {
            $url = Moraso_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idcat-' . $idcat . '}');
        } else {
            $url = Moraso_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idart-' . $idart . '}');
        }

        // set Graph Data
        $data = array(
            'og:title' => $article->pagetitle . $this->_params->title_suffix,
            'og:type' => 'website',
            'og:image' => Moraso_Html_Helper_Image::getPath($idart, $article->mainimage, 500, 500, 2),
            'og:image:width' => 500,
            'og:image:height' => 500,
            'og:url' => $url,
            'og:locale' => 'de_DE',
            'og:country-name' => 'GER'
        );

        /*
         * Standard-Werte wenn gegeben überschreiben
         */
        // og:locale
        if (isset($this->_params->locale) && !empty($this->_params->locale)) {
            $data['og:locale'] = $this->_params->locale;
        }

        // og:country-name
        if (isset($this->_params->country_name) && !empty($this->_params->country_name)) {
            $data['og:country-name'] = $this->_params->country_name;
        }

        // og:site_name
        if (isset($this->_params->site_name) && !empty($this->_params->site_name)) {
            $data['og:site_name'] = $this->_params->site_name;
        }

        // og:admins
        if (isset($this->_params->admins) && !empty($this->_params->admins)) {
            $data['fb:admins'] = $this->_params->admins;
        }

        // og:page_id
        if (isset($this->_params->page_id) && !empty($this->_params->page_id)) {
            $data['fb:page_id'] = $this->_params->page_id;
        }

        /*
         * Werte die per Artikel-Plugin überschrieben werden können
         */
        // og:type
        if (isset($this->_params->type) && !empty($this->_params->type)) {
            $data['og:type'] = $this->_params->type;
        }

        if (isset($open_graph->type->value) && !empty($open_graph->type->value)) {
            $data['og:type'] = $open_graph->type->value;
        }

        // og:description
        if (isset($article->summary) && !empty($article->summary)) {
            $data['og:description'] = $article->summary;
        }

        if (isset($open_graph->type->description) && !empty($open_graph->type->description)) {
            $data['og:description'] = $open_graph->type->description;
        }

        // og:locality
        if (isset($this->_params->locality) && !empty($this->_params->locality)) {
            $data['og:locality'] = $this->_params->locality;
        }

        if (isset($open_graph->type->locality) && !empty($open_graph->type->locality)) {
            $data['og:locality'] = $open_graph->type->locality;
        }
        
        // og:city
        if (isset($this->_params->city) && !empty($this->_params->city)) {
            $data['og:city'] = $this->_params->city;
        }

        if (isset($open_graph->type->city) && !empty($open_graph->type->city)) {
            $data['og:city'] = $open_graph->type->city;
        }

        // og:latitude
        if (isset($this->_params->latitude) && !empty($this->_params->latitude)) {
            $data['og:latitude'] = $this->_params->latitude;
        }

        if (isset($open_graph->type->latitude) && !empty($open_graph->type->latitude)) {
            $data['og:latitude'] = $open_graph->type->latitude;
        }

        // og:longitude
        if (isset($this->_params->longitude) && !empty($this->_params->longitude)) {
            $data['og:longitude'] = $this->_params->longitude;
        }

        if (isset($open_graph->type->longitude) && !empty($open_graph->type->longitude)) {
            $data['og:longitude'] = $open_graph->type->longitude;
        }

        // og:street-address
        if (isset($this->_params->street_address) && !empty($this->_params->street_address)) {
            $data['og:street-address'] = $this->_params->street_address;
        }

        if (isset($open_graph->type->street_address) && !empty($open_graph->type->street_address)) {
            $data['og:street-address'] = $open_graph->type->street_address;
        }

        // og:region
        if (isset($this->_params->region) && !empty($this->_params->region)) {
            $data['og:region'] = $this->_params->region;
        }

        if (isset($open_graph->type->region) && !empty($open_graph->type->region)) {
            $data['og:region'] = $open_graph->type->region;
        }

        // og:postal-code
        if (isset($this->_params->postal_code) && !empty($this->_params->postal_code)) {
            $data['og:postal-code'] = $this->_params->postal_code;
        }

        if (isset($open_graph->type->postal_code) && !empty($open_graph->type->postal_code)) {
            $data['og:postal-code'] = $open_graph->type->postal_code;
        }

        // og:email
        if (isset($this->_params->email) && !empty($this->_params->email)) {
            $data['og:email'] = $this->_params->email;
        }

        if (isset($open_graph->type->email) && !empty($open_graph->type->email)) {
            $data['og:email'] = $open_graph->type->email;
        }

        // og:phone_number
        if (isset($this->_params->phone_number) && !empty($this->_params->phone_number)) {
            $data['og:phone_number'] = $this->_params->phone_number;
        }

        if (isset($open_graph->type->phone_number) && !empty($open_graph->type->phone_number)) {
            $data['og:phone_number'] = $open_graph->type->phone_number;
        }

        // og:fax
        if (isset($this->_params->fax) && !empty($this->_params->fax)) {
            $data['og:fax'] = $this->_params->fax;
        }

        if (isset($open_graph->type->fax) && !empty($open_graph->type->fax)) {
            $data['og:fax'] = $open_graph->type->fax;
        }

        // create View
        $view = $this->_getView();
        $view->data = $data;
        return $view->render('index.phtml');
    }

    protected function _cachingPeriod()
    {
        return Aitsu_Util_Date::secondsUntilEndOf('year');
    }

}