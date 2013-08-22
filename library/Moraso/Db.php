<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Db extends Aitsu_Db
{
    public static function filter($baseQuery, $limit = null, $offset = null, $filters = null, $orders = null, $groups = null)
    {
        $limit = is_null($limit) || !is_numeric($limit) ? 100 : $limit;
        $offset = is_null($offset) || !is_numeric($offset) ? 0 : $offset;
        $filters = is_array($filters) ? $filters : array();
        $orders = is_array($orders) ? $orders : array();
        $groups = is_array($groups) ? $groups : array();

        $filterClause = array();
        $filterValues = array();
        for ($i = 0; $i < count($filters); $i++) {
            $filterClause[] = $filters[$i]->clause . ' :value' . $i;
            $filterValues[':value' . $i] = $filters[$i]->value;
        }
        $where = count($filterClause) == 0 ? '' : 'where ' . implode(' and ', $filterClause);

        $orderBy = count($orders) == 0 ? '' : 'order by ' . implode(', ', $orders);
        $groupBy = count($groups) == 0 ? '' : 'group by ' . implode(', ', $groups);

        $results = Moraso_Db::fetchAll('' .
            $baseQuery .
            ' ' . $where .
            ' ' . $groupBy .
            ' ' . $orderBy .
            'limit ' . $offset . ', ' . $limit, $filterValues);

        $return = array();

        if ($results) {
            foreach ($results as $result) {
                $return[] = (object) $result;
            }
        }

        return $return;
    }

    public static function delete($from, array $where)
    {
        $whereClause = array();
        $whereValues = array();

        foreach ($where as $key => $value) {
            $clause = '=';

            if (is_array($value)) {
                $clause = $value['clause'];
                $value = $value['value'];
            }

            $whereClause[] = $key . ' ' . $clause . ':value_' . $key;
            $whereValues[':value_' . $key] = $value;
        }

        Moraso_Db::query('DELETE FROM ' . $from . ' WHERE ' . implode(' AND ', $whereClause) . '', $whereValues);
    }

    public static function simpleFetch($select, $from, array $where, $limit = 1, $caching = 0, array $orderBy)
    {
        $whereClause = array();
        $whereValues = array();
        foreach ($where as $key => $value) {
            $clause = '=';

            if (is_array($value)) {
                $clause = $value['clause'];
                $value = $value['value'];
            }

            $whereClause[] = $key . ' ' . $clause . ':value_' . $key;
            $whereValues[':value_' . $key] = $value;
        }

        $orderClause = array();
        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $sort) {
                $orderClause[] = $field . ' ' . $sort;
            }
        }

        $where = empty($whereClause) ? '', ' WHERE ' . implode(' AND ', $whereClause);
        $orderBy = empty($orderClause) ? '', ' ORDER BY ' . implode(', ', $orderClause);

        if ($caching === 0) {
            if ($limit === 1) {
                if (is_array($select)) {
                    return Moraso_Db::fetchRow('SELECT `' . implode('`, `', $select) . '` FROM ' . $from . $where . $orderBy, $whereValues);
                } elseif ($select === 'all') {
                    return Moraso_Db::fetchRow('SELECT * FROM ' . $from . $where . $orderBy, $whereValues);
                }

                return Moraso_Db::fetchOne('SELECT `' . $select . '` FROM ' . $from . $where . $orderBy, $whereValues);
            } else {
                if (!is_array($select)) {
                    $select = str_split($select);
                }

                if ($select === 'all') {
                    return Moraso_Db::fetchAll('SELECT * FROM ' . $from . $where . $orderBy, $whereValues);
                } else {
                    return Moraso_Db::fetchAll('SELECT `' . implode('`, `', $select) . '` FROM ' . $from . $where . $orderBy, $whereValues);
                }
            }
        } else {
            if ($limit === 1) {
                if (is_array($select)) {
                    return Moraso_Db::fetchRowC($caching, 'SELECT `' . implode('`, `', $select) . '` FROM ' . $from . $where . $orderBy, $whereValues);
                }

                if ($select === 'all') {
                    return Moraso_Db::fetchOneC($caching, 'SELECT * FROM ' . $from . $where . $orderBy, $whereValues);
                } else {
                    return Moraso_Db::fetchOneC($caching, 'SELECT `' . $select . '` FROM ' . $from . $where . $orderBy, $whereValues);
                }
            } else {
                if (!is_array($select)) {
                    $select = str_split($select);
                }

                if ($select === 'all') {
                    return Moraso_Db::fetchAllC($caching, 'SELECT * FROM ' . $from . $where . $orderBy, $whereValues);
                } else {
                    return Moraso_Db::fetchAllC($caching, 'SELECT `' . implode('`, `', $select) . '` FROM ' . $from . $where . $orderBy, $whereValues);
                }
            }
        }
    }
}