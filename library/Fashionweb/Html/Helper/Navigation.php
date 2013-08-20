<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Html_Helper_Navigation
{
    public static function getHtml(array $nav, array $ulIds = array(), array $ulClasses = array(), array $options = array())
    {
        $self = new self();

        $optionDefaults = array(
            1 => array(
                'noLink' => false,
                'liClassIfIsActive' => 'active',
                'liClassIfIsCurrent' => 'isCurrent',
                'liClassIfIsParent' => 'isParent',
                'liClassIfHasChildren' => 'hasChildren',
                'liClassIfIsLast' => 'last',
                'liClassIfIsFirst' => 'first',
                'aClassIfIsActive' => '',
                'aClassIfIsCurrent' => '',
                'aClassIfIsParent' => '',
                'aClassIfHasChildren' => '',
                'aClassIfIsLast' => '',
                'aClassIfIsFirst' => '',
                'divider' => false
            ),
            2 => array(
                'noLink' => false,
                'liClassIfIsActive' => 'active',
                'liClassIfIsCurrent' => 'isCurrent',
                'liClassIfIsParent' => 'isParent',
                'liClassIfHasChildren' => 'hasChildren',
                'liClassIfIsLast' => 'last',
                'liClassIfIsFirst' => 'first',
                'aClassIfIsActive' => '',
                'aClassIfIsCurrent' => '',
                'aClassIfIsParent' => '',
                'aClassIfHasChildren' => '',
                'aClassIfIsLast' => '',
                'aClassIfIsFirst' => '',
                'divider' => false
            ),
            3 => array(
                'noLink' => false,
                'liClassIfIsActive' => 'active',
                'liClassIfIsCurrent' => 'isCurrent',
                'liClassIfIsParent' => 'isParent',
                'liClassIfHasChildren' => 'hasChildren',
                'liClassIfIsLast' => 'last',
                'liClassIfIsFirst' => 'first',
                'aClassIfIsActive' => '',
                'aClassIfIsCurrent' => '',
                'aClassIfIsParent' => '',
                'aClassIfHasChildren' => '',
                'aClassIfIsLast' => '',
                'aClassIfIsFirst' => '',
                'divider' => false
            )
        );

        $options = $self->_array_merge_custom($optionDefaults, $options);

        return $self->_createUl($nav, $ulIds, $ulClasses, $options);
    }

    private function _array_merge_custom()
    {
        $array = array();
        $arguments = func_get_args();

        foreach ($arguments as $args) {
            foreach ($args as $key => $value) {
                foreach ($value as $key_s => $value_s) {
                    $array[$key][$key_s] = $value_s;
                }
            }
        }

        return $array;
    }

    private function _createUl($nav, $ulIds = null, $ulClasses = null, $options = null, $level = 1)
    {
        $ul = '<ul';

        if (isset($ulIds[$level]) && !empty($ulIds[$level])) {
            $ul.= ' id="' . implode(' ', $ulIds[$level]) . '"';
        }

        if (isset($ulClasses[$level]) && !empty($ulClasses[$level])) {
            $ul.= ' class="' . implode(' ', $ulClasses[$level]) . '"';
        }

        $ul.= '>';

        $cntLIs = count($nav);
        $i = 0;
        foreach ($nav as $row) {
            $i++;

            $ul.= $this->_createLi($row, $ulIds, $ulClasses, $options, $level, $i == $cntLIs ? true : false, $i == 1 ? true : false);
        }

        $ul.= '</ul>';

        return $ul;
    }

    private function _createLi($row, $ulIds, $ulClasses, $options, $level, $isLast, $isFirst)
    {
        $liClasses = array();
        $aClasses = array();

        if ($row['isCurrent'] || $row['isParent']) {
            $liClasses[] = $options[$level]['liClassIfIsActive'];
            $aClasses[] = $options[$level]['aClassIfIsActive'];

            if ($row['isCurrent']) {
                $liClasses[] = $options[$level]['liClassIfIsCurrent'];
                $aClasses[] = $options[$level]['aClassIfIsCurrent'];
            }

            if ($row['isParent']) {
                $liClasses[] = $options[$level]['liClassIfIsParent'];
                $aClasses[] = $options[$level]['aClassIfIsParent'];
            }
        }

        if ($isLast) {
            $liClasses[] = $options[$level]['liClassIfIsLast'];
            $aClasses[] = $options[$level]['aClassIfIsLast'];
        }
        
        if ($isFirst) {
            $liClasses[] = $options[$level]['liClassIfIsFirst'];
            $aClasses[] = $options[$level]['aClassIfIsFirst'];
        }

        if (isset($row['hasChildren']) && $row['hasChildren']) {
            $liClasses[] = $options[$level]['liClassIfHasChildren'];
            $aClasses[] = $options[$level]['aClassIfHasChildren'];
        }

        $li = '<li';

        if (!empty($liClasses)) {
            $li.= ' class="' . implode(' ', $liClasses) . '"';
        }

        $li.= '>';

        if (isset($options[$level]['noLink']) && !empty($options[$level]['noLink'])) {
            $li.= $row['name'];
        } else {
            if (empty($aClasses)) {
                $li.= '<a href="{ref:idcat-' . $row['idcat'] . '}">' . $row['name'] . '</a>';
            } else {
                $li.= '<a class="' . implode(' ', $aClasses) . '" href="{ref:idcat-' . $row['idcat'] . '}">' . $row['name'] . '</a>';
            } 
        }

        if (isset($row['hasChildren']) && $row['hasChildren']) {
            $li.= $this->_createUl($row['children'], $ulIds, $ulClasses, $options, $level + 1);
        }

        $li.= '</li>';

        if ($options[$level]['divider'] && !empty($options[$level]['divider']) && !$isLast) {
            $li.= $options[$level]['divider'];
        }

        return $li;
    }
}