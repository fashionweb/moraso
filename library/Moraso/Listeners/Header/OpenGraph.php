<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_OpenGraph implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$prefix = '';
		$suffix = '';
		$type = '';
		$locale = '';
		$country_name = '';
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->pagetitle->prefix) && !empty($json_file_content->pagetitle->prefix)) {
					$prefix = $json_file_content->pagetitle->prefix;
				}

				if (isset($json_file_content->pagetitle->suffix) && !empty($json_file_content->pagetitle->suffix)) {
					$suffix = $json_file_content->pagetitle->suffix;
				}

				if (isset($json_file_content->open_graph->type) && !empty($json_file_content->open_graph->type)) {
					$type = $json_file_content->open_graph->type;
				}

				if (isset($json_file_content->open_graph->locale) && !empty($json_file_content->open_graph->locale)) {
					$locale = $json_file_content->open_graph->locale;
				}

				if (isset($json_file_content->open_graph->country_name) && !empty($json_file_content->open_graph->country_name)) {
					$country_name = $json_file_content->open_graph->country_name;
				}
			}
		}

		$article = Aitsu_Persistence_Article::factory(Aitsu_Registry::get()->env->idart);
		$articleProperty = Aitsu_Persistence_ArticleProperty::factory(Aitsu_Registry::get()->env->idartlang)->load();

		$open_graph = (object) $articleProperty->open_graph;

		$rewrite = $article->isIndex() ? '{ref:idcat-' . Aitsu_Registry::get()->env->idcat . '}' : '{ref:idart-' . Aitsu_Registry::get()->env->idart . '}';

		$url = Moraso_Rewrite_Standard::getInstance()->rewriteOutput($rewrite);
		
		$open_graphs = array(
			'og:title' => $prefix . ' ' . $article->pagetitle . ' ' . $suffix,
			'og:url' => $url
			);

		if (!empty($type)) {
			$open_graphs['og:type'] = $type;
		}

		if (!empty($locale)) {
			$open_graphs['og:locale'] = $locale;
		}

		if (!empty($country_name)) {
			$open_graphs['og:country-name'] = $country_name;
		}

		if (!empty($article->mainimage)) {
			$open_graphs['og:image'] = Moraso_Html_Helper_Image::getPath(Aitsu_Registry::get()->env->idart, $article->mainimage, 500, 500, 2);
			$open_graphs['og:image:width'] = 500;
			$open_graphs['og:image:height'] = 500;
		}

		$openGraphs = array();
		foreach ($open_graphs as $name => $content) {
			if (!empty($content)) {
				$openGraphs[] = '<meta name="' . $name . '" content="' . $content . '" />';
			}
		}

		Aitsu_Registry::get()->header->open_graph = (object) array(
			"name" => "Open Graph",
			"tags" => $openGraphs
			);
	}
}