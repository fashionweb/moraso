<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Addresses
{
    public static function set($name, $street, $house_number, $postal_code, $city, $address_id = 0, array $groups = array())
    {
        $googleData = Google_Maps::getGeoData($street, $house_number, $postal_code, $city);

        if (empty($address_id)) {
            $googleData['address_id'] = Moraso_Db::put('_addresses', 'address_id', array(
                        'name' => $name,
                        'street' => $street,
                        'house_number' => $house_number,
                        'postal_code' => $postal_code,
                        'city' => $city
            ));

            Moraso_Db::put('_addresses_google_data', null, $googleData);
        } else {
            $googleData['address_id'] = Moraso_Db::put('_addresses', 'address_id', array(
                        'address_id' => $address_id,
                        'name' => $name,
                        'street' => $street,
                        'house_number' => $house_number,
                        'postal_code' => $postal_code,
                        'city' => $city
            ));

            Moraso_Db::put('_addresses_google_data', 'address_id', $googleData);
        }

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

    public static function get($address_id = null)
    {
        if (is_null($address_id)) {
            $userInput = Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   address.*, ' .
                            '   hasGroup.addresses_group_id ' .
                            'FROM ' .
                            '   _addresses AS address ' .
                            'LEFT JOIN ' .
                            '   _addresses_address_has_group AS hasGroup ON hasGroup.address_id = address.address_id');

            $googleOutput = Moraso_Db::fetchAll('SELECT * FROM _addresses_google_data');
        } else {
            return (object) array(
                        'userInput' => (object) Moraso_Db::fetchRow('SELECT * FROM _addresses WHERE address_id =:address_id', array(':address_id' => $address_id)),
                        'googleOutput' => (object) Moraso_Db::fetchRow('SELECT * FROM _addresses_google_data WHERE address_id =:address_id', array(':address_id' => $address_id)),
                        'groups' => (object) Moraso_Db::fetchCol('SELECT addresses_group_id FROM _addresses_address_has_group WHERE address_id =:address_id', array(':address_id' => $address_id)),
            );
        }

        return (object) array(
                    'userInput' => (object) $userInput,
                    'googleOutput' => (object) $googleOutput
        );
    }

    public static function delete($address_id)
    {
        Moraso_Db::query('DELETE FROM _addresses WHERE address_id =:address_id', array(':address_id' => $address_id));
    }

}