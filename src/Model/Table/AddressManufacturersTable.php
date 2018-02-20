<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

class AddressManufacturersTable extends AddressesTable
{

    public function validationDefault(Validator $validator)
    {
        $validator->notEmpty('firstname', 'Bitte gib den Vornamen des Rechnungsempfängers an.');
        $validator->notEmpty('lastname', 'Bitte gib den Nachnamen des Rechnungsempfängers an.');
        $validator->notEmpty('email', 'Bitte gib eine E-Mail-Adresse an.');
        $validator->email('email', false, 'Die E-Mail-Adresse ist nicht gültig.');
        $validator->add('email', 'unique', [
            'rule' => 'validateUnique',
            'provider' => 'table',
            'message' => 'Ein anderes Mitglied oder ein anderer Hersteller verwendet diese E-Mail-Adresse bereits.'
        ]);
        $validator->allowEmpty('postcode');
        $validator->add('postcode', 'validFormat', [
            'rule' => array('custom', ZIP_REGEX),
            'message' => 'Die PLZ ist nicht gültig.'
        ]);
        $validator->allowEmpty('phone_mobile');
        $validator->add('phone_mobile', 'validFormat', [
            'rule' => array('custom', PHONE_REGEX),
            'message' => 'Die Handynummer ist nicht gültig.'
        ]);
        $validator->allowEmpty('phone');
        $validator->add('phone', 'validFormat', [
            'rule' => array('custom', PHONE_REGEX),
            'message' => 'Die Telefonnummer ist nicht gültig.'
        ]);
        return $validator;
    }

    /**
     * for addresses only
     *
     * @param array $check
     * @return boolean
     */
    public function uniqueEmailWithFlagCheck($check)
    {
        $conditions = [
            $this->getAlias() . '.email' => $check['email']
        ];

        // if manufacturer address already exists
        if ($this->id > 0) {
            $conditions[] = $this->getAlias() . '.id_address <> ' . $this->id;
        }

        $found = $this->find('count', [
            'conditions' => $conditions
        ]);
        if ($found == 0) {
            return true;
        }
        return false;
    }
}
