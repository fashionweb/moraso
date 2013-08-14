<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
abstract class Moraso_Module_Abstract extends Aitsu_Module_Abstract
{
    protected $_renderOnMobile = true;
    protected $_renderOnTablet = true;
    protected $_moduleConfigDefaults = array();
    protected $_withoutView = false;

    protected static function _getInstance($className)
    {
        $instance = new $className ();

        $className = str_replace('_', '.', $className);
        $className = preg_replace('/^(?:Skin\\.Module|Moraso\\.Module|Module)\\./', "", $className);
        $className = preg_replace('/\\.Class$/', "", $className);

        if (isset($_GET['renderOnly']) && $className == substr($_GET['renderOnly'], 0, strlen($className)) && !$instance->_renderOnlyAllowed) {
            throw new Aitsu_Security_Module_RenderOnly_Exception($className);
        }

        $instance->_moduleName = $className;

        return $instance;
    }

    public static function init($context, $instance = null)
    {
        $instance = is_null($instance) ? self::_getInstance($context['className']) : $instance;

        if ($instance->_notForHumans()) {
            return;
        }

        $isMobile = Aitsu_Registry::get()->env->mobile->detect->isMobile;
        $isTablet = Aitsu_Registry::get()->env->mobile->detect->isTablet;

        if (($isMobile == 'is' && !$instance->_renderOnMobile) || ($isTablet == 'is' && !$instance->_renderOnTablet)) {
            return false;
        }

        if (($isMobile == 'is' && $isTablet == 'isNot') && (!$instance->_renderOnMobile && $instance->_renderOnTablet)) {
            return false;
        }

        if (!$instance->_isBlock) {
            Aitsu_Content_Edit::isBlock(false);
        }

        $instance->_context = $context;

        $instance->_context['rawIndex'] = $instance->_context['index'];
        $instance->_context['index'] = preg_replace('/[^a-zA-Z_0-9]/', '_', $instance->_context['index']);
        $instance->_context['index'] = str_replace('.', '_', $instance->_context['index']);

        $instance->_index = empty($instance->_context['index']) ? 'noindex' : $instance->_context['index'];

        if (!empty($instance->_context['params'])) {
            $instance->_params = Aitsu_Util::parseSimpleIni($instance->_context['params']);
        }

        $instance->_defaults = $instance->_getModulConfigDefaults(str_replace('_', '.', strtolower($instance->_moduleName)));
        $instance->_view = $instance->_getView();
        $instance->_view->_index = $instance->_index;
        
        $instance->_translation = array(
            'configuration' => Aitsu_Translate::_('Configuration'),
            'source' => Aitsu_Translate::_('Source')
            );

        if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') && (isset($instance->defaults['ajax_sleep_before_rendering']) && !empty($instance->defaults['ajax_sleep_before_rendering']))) {
            sleep($instance->defaults['ajax_sleep_before_rendering']);
        }

        $output_raw = $instance->_init();

        if ($instance->_cachingPeriod() > 0) {
            if ($instance->_get($context['className'], $output_raw)) {

                if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') && (isset($instance->defaults['ajax_sleep_after_rendering']) && !empty($instance->defaults['ajax_sleep_after_rendering']))) {
                    sleep($instance->defaults['ajax_sleep_after_rendering']);
                }

                return $output_raw;
            }
        }

        $availableTemplates = $instance->_getTemplates();

        if (isset($instance->_defaults['newRenderingMethode']) && $instance->_defaults['newRenderingMethode']) {
            if (!$instance->_withoutView) {
                if ($instance->_defaults['configurable']['template']) {
                    $template = Aitsu_Content_Config_Select::set($instance->_index, 'template', Aitsu_Translate::_('Template'), $availableTemplates, $instance->_translation['configuration']);

                    if (!empty($template)) {
                        $instance->_view->template = $template . '.phtml';
                    }
                }
            }

            if ($instance->_defaults['configurable']['idart']) {
                $idart = Aitsu_Content_Config_Link::set($instance->_index, 'idart', 'idart', $instance->_translation['source']);

                if (!empty($idart)) {
                    $instance->_defaults['idart'] = preg_replace('/[^0-9]/', '', $idart);
                    $instance->_defaults['idartlang'] = Moraso_Util::getIdArtLang($instance->_defaults['idart'], $instance->_defaults['idlang']);
                }
            }

            if ($instance->_defaults['configurable']['idcat']) {
                $idcat = Aitsu_Content_Config_Link::set($instance->_index, 'idcat', 'idcat', $instance->_translation['source']);

                if (!empty($idcat)) {
                    $instance->_defaults['idcat'] = preg_replace('/[^0-9]/', '', $idcat);
                }
            }
        }

        $output_raw .= $instance->_main();

        if (count(array_filter($instance->_defaults['configurable'])) === 0) {
            Aitsu_Content_Edit::noEdit($instance->_moduleName, true);
        }

        if ((isset($instance->_defaults['newRenderingMethode']) && $instance->_defaults['newRenderingMethode']) && !$instance->_withoutView) {
            if (!isset($instance->_view->template) || empty($instance->_view->template)) {
                $instance->_view->template = $instance->_defaults['template'] . '.phtml';
            }

            $output_raw .= $instance->_view->render($instance->_view->template);
        }

        $output = $instance->_transformOutput($output_raw);

        if ($instance->_cachingPeriod() > 0) {
            $instance->_save($output, $instance->_cachingPeriod());
        }

        if (Aitsu_Application_Status::isEdit()) {
            $maxLength = 60;
            $index = strlen($context['index']) > $maxLength ? substr($context['index'], 0, $maxLength) . '...' : $context['index'];

            $match = array();
            if (trim($output) == '' && $instance->_allowEdit) {
                if (preg_match('/^Module_(.*?)_Class$/', $context['className'], $match)) {
                    $moduleName = str_replace('_', '.', $match[1]);
                } elseif (preg_match('/^(Skin|Moraso)_Module_(.*?)_Class$/', $context['className'], $match)) {
                    $moduleName = str_replace('_', '.', $match[2]);
                } else {
                    $moduleName = 'UNKNOWN';
                }

                if ($instance->_isBlock) {
                    return '' .
                    '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
                    '<div style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' .
                    '	<div style="height:15px; background-color: #CCC; color: white; font-size: 11px; padding:2px 5px 0 5px;">' .
                    '		<span style="font-weight:bold; float:left;">' . $index . '</span><span style="float:right;">Module <span style="font-weight:bold;">' . $moduleName . '</span></span>' .
                    '	</div>' .
                    '</div>';
                } else {
                    return '' .
                    '<span style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' .
                    '	' . $moduleName . '::' . $index .
                    '</span>';
                }
            }

            if (!$instance->_isBlock) {
                return '' .
                '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
                '<span style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' . $output . '</span>';
            }

            if (isset($instance->_params->suppressWrapping) && $instance->_params->suppressWrapping) {
                return $output;
            }

            return '' .
            '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
            '<div>' . $output . '</div>';
        }

        if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') && (isset($instance->defaults['ajax_sleep_after_rendering']) && !empty($instance->defaults['ajax_sleep_after_rendering']))) {
            sleep($instance->defaults['ajax_sleep_after_rendering']);
        }

        return $output;
    }

    protected function _getView($view = null)
    {
        if ($this->_view != null) {
            return $this->_view;
        }

        $view = empty($view) ? new Zend_View() : $view;

        $module_parts = explode('_', get_class($this));

        $module_sliced = array_slice($module_parts, $module_parts[0] != 'Module' ? 2 : 1, -1);

        $modulePath = implode('/', $module_sliced);

        $view->addScriptPath(APPLICATION_PATH . '/modules/' . $modulePath . '/');
        $view->addScriptPath(realpath(APPLICATION_PATH . '/../library/') . '/Moraso/Module/' . $modulePath . '/');

        $heredity = Moraso_Skin_Heredity::build();

        foreach (array_reverse($heredity) as $skin) {
            $view->addScriptPath(APPLICATION_PATH . "/skins/" . $skin . "/module/" . $modulePath . '/');
        }

        return $view;
    }

    protected function _getDefaults()
    {
        $module_parts = explode('_', get_class($this));

        $module_sliced = array_slice($module_parts, $module_parts[0] != 'Module' ? 2 : 1, -1);

        $modulePath = implode('/', $module_sliced);

        $modulePaths = array();

        $modulePaths[] = realpath(APPLICATION_PATH . '/../library/') . '/Moraso/Module/';
        $modulePaths[] = APPLICATION_PATH . '/modules/' . $modulePath . '/';
        $modulePaths[] = realpath(APPLICATION_PATH . '/../library/') . '/Moraso/Module/' . $modulePath . '/';

        $heredity = Moraso_Skin_Heredity::build();

        foreach (array_reverse($heredity) as $skin) {
            $modulePaths[] = APPLICATION_PATH . "/skins/" . $skin . "/module/" . $modulePath . '/';
        }

        $defaults = array();
        $defaults['newRenderingMethode'] = false;

        foreach ($modulePaths as $key => $modulePath) {
            if (file_exists($modulePath . 'module.json')) {
                if ($key >= 1) {
                    $defaults['newRenderingMethode'] = true;
                }

                $module_config = json_decode(file_get_contents($modulePath . 'module.json'));

                if (isset($module_config->defaults) && !empty($module_config->defaults)) {
                    foreach ($module_config->defaults as $key => $value) {
                        if ($key === 'configurable' && is_object($value)) {
                            foreach ($value as $param => $bool) {
                                $defaults['configurable'][$param] = $bool;
                            }
                        } else {
                            switch($value) {
                                case '##this.article.idart##':
                                $value = Aitsu_Registry::get()->env->idart;
                                break;
                                case '##this.article.idlang##':
                                $value = Aitsu_Registry::get()->env->idlang;
                                break;
                                case '##this.article.idartlang##':
                                $value = Aitsu_Registry::get()->env->idartlang;
                                break;
                                case '##this.article.idcat##':
                                $value = Aitsu_Registry::get()->env->idcat;
                                break;
                                case '##secondsUntilEndOf.day##':
                                $value = Aitsu_Util_Date::secondsUntilEndOf('day');
                                break;
                                case '##secondsUntilEndOf.year##':
                                $value = Aitsu_Util_Date::secondsUntilEndOf('year');
                                break;
                            }

                            $defaults[$key] = $value;
                        }
                    }
                }
            }
        }

        return $defaults;
    }

    protected function _getModulConfigDefaults($module)
    {
        $moduleConfig = Moraso_Config::get('module.' . $module);

        if (isset($this->_params->idart) && !empty($this->_params->idart)) {
            if (isset($this->_params->idlang) && !empty($this->_params->idlang)) {
                $this->_params->idartlang = Moraso_Util::getIdArtLang($this->_params->idart, $this->_params->idlang);
            } else {
                $this->_params->idartlang = Moraso_Util::getIdArtLang($this->_params->idart);
            }
        } elseif (isset($this->_params->idlang) && !empty($this->_params->idlang)) {
            $this->_params->idartlang = Moraso_Util::getIdArtLang($this->_params->idart, $this->_params->idlang);
        } elseif (isset($this->_params->idartlang) && !empty($this->_params->idartlang)) {
            $this->_params->idart = Moraso_Util::getIdArt($this->_params->idartlang);
            $this->_params->idlang = Moraso_Util::getIdLangByIdArtLang($this->_params->idartlang);
        }

        $defaults = $this->_getDefaults();

        foreach ($defaults as $key => $value) {
            $type = gettype($value);

            if (isset($moduleConfig->$key->default)) {
                $default = $moduleConfig->$key->default;
                $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
            }

            if (isset($moduleConfig->$key->configurable)) {
                $defaults['configurable'][$key] = filter_var($moduleConfig->$key->configurable, FILTER_VALIDATE_BOOLEAN);

                if (isset($moduleConfig->$key->selects) && $defaults['configurable'][$key]) {
                    $selects = $moduleConfig->$key->selects;

                    foreach ($selects as $i => $select) {
                        if (!is_object($select)) {
                            $defaults['selects'][$key]['values'][$i] = $select;
                            $defaults['selects'][$key]['names'][$i] = $select;
                        } else {
                            $defaults['selects'][$key]['values'][$i] = $select->value;
                            $defaults['selects'][$key]['names'][$i] = $select->name;
                        }
                    }
                }
            }

            if (!isset($defaults['configurable'][$key])) {
                $defaults['configurable'][$key] = false;
            }

            if (isset($this->_params->$key)) {
                $default = $this->_params->$key;

                if ($default === 'config') {
                    if (isset($this->_params->default->$key)) {
                        $default = $this->_params->default->$key;
                        $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
                    }

                    if (isset($this->_params->selects->$key)) {
                        $selects = $this->_params->selects->$key;

                        foreach ($selects as $i => $select) {
                            if (!is_object($select)) {
                                $defaults['selects'][$key]['values'][$i] = $select;
                                $defaults['selects'][$key]['names'][$i] = $select;
                            } else {
                                $defaults['selects'][$key]['values'][$i] = $select->value;
                                $defaults['selects'][$key]['names'][$i] = $select->name;
                            }
                        }
                    }

                    $defaults['configurable'][$key] = true;
                } else {
                    $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
                }
            }
        }

        $this->_moduleConfigDefaults = $defaults;

        return $defaults;
    }

    protected function _cachingPeriod()
    {
        if (isset($this->_defaults['caching'])) {
            return $this->_defaults['caching'];
        }

        return 0;
    }
}