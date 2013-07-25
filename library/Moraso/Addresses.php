<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Addresses
{
    public static function set($name, $street, $house_number, $postal_code, $city, $country = 'de', $address_id = 0, array $groups = array())
    {
        $address = array(
            'name' => $name,
            'street' => $street,
            'house_number' => $house_number,
            'postal_code' => $postal_code,
            'city' => $city,
            'country' => $country
        );

        $primary = null;

        if (!empty($address_id)) {
            $address['address_id'] = $address_id;

            $primary = 'address_id';
        }

        $address_id = Moraso_Db::put('_addresses', 'address_id', $address);

        $googleData = Google_Maps::getGeoData($name, $street, $house_number, $postal_code, $city, $country);
        $googleData['address_id'] = $address_id;

        Moraso_Db::put('_addresses_google_data', $primary, $googleData);

        Moraso_Db::query('DELETE FROM _addresses_address_has_group WHERE address_id =:address_id', array(':address_id' => $address_id));

        foreach ($groups as $addresses_group_id) {
            Moraso_Db::put('_addresses_address_has_group', null, array(
                'address_id' => $address_id,
                'addresses_group_id' => $addresses_group_id
            ));
        }

        return $address_id;
    }

    public static function getGroups()
    {
        return (object) Moraso_Db::fetchAll('' .
                        'SELECT ' .
                        '   * ' .
                        'FROM ' .
                        '   _addresses_groups');
    }

    public static function get($address_id = null, $addresses_group_id = null)
    {
        if (is_null($address_id)) {
            if (is_null($addresses_group_id)) {
                $return = array(
                    'userInput' => (object) Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   address.*, ' .
                            '   addresses_group.name AS addresses_group ' .
                            'FROM ' .
                            '   _addresses AS address ' .
                            'LEFT JOIN ' .
                            '   _addresses_address_has_group AS hasGroup ON hasGroup.address_id = address.address_id ' .
                            'LEFT JOIN ' .
                            '   _addresses_groups AS addresses_group ON addresses_group.addresses_group_id = hasGroup.addresses_group_id'),
                    'googleOutput' => (object) Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   * ' .
                            'FROM ' .
                            '   _addresses_google_data')
                );
            } else {
                $return = array(
                    'userInput' => (object) Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   address.* ' .
                            'FROM ' .
                            '   _addresses AS address ' .
                            'LEFT JOIN ' .
                            '   _addresses_address_has_group AS hasGroup ON hasGroup.address_id = address.address_id ' .
                            'WHERE ' .
                            '   hasGroup.addresses_group_id =:addresses_group_id', array(
                        ':addresses_group_id' => $addresses_group_id
                    )),
                    'googleOutput' => (object) Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   googleData.*, ' .
                            '   address.name ' .
                            'FROM ' .
                            '   _addresses_google_data AS googleData ' .
                            'LEFT JOIN ' .
                            '   _addresses AS address ON address.address_id = googleData.address_id ' .
                            'LEFT JOIN ' .
                            '   _addresses_address_has_group AS hasGroup ON hasGroup.address_id = address.address_id ' .
                            'WHERE ' .
                            '   hasGroup.addresses_group_id =:addresses_group_id', array(
                        ':addresses_group_id' => $addresses_group_id
                    ))
                );
            }
        } else {
            $return = array(
                'userInput' => (object) Moraso_Db::fetchRow('SELECT * FROM _addresses WHERE address_id =:address_id', array(':address_id' => $address_id)),
                'googleOutput' => (object) Moraso_Db::fetchRow('SELECT * FROM _addresses_google_data WHERE address_id =:address_id', array(':address_id' => $address_id)),
                'groups' => (object) Moraso_Db::fetchCol('SELECT addresses_group_id FROM _addresses_address_has_group WHERE address_id =:address_id', array(':address_id' => $address_id)),
            );
        }

        return (object) $return;
    }

    public static function delete($address_id)
    {
        Moraso_Db::query('DELETE FROM _addresses WHERE address_id =:address_id', array(':address_id' => $address_id));
    }

}