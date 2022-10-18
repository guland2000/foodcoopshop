<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.3.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class StorageLocationsTable extends AppTable
{

    public function getForDropdown()
    {
        $storageLocations = $this->find('all', [
            'order' => [
                'StorageLocations.rank' => 'ASC',
            ]
        ]);
        $preparedStorageLocations = [];
        foreach ($storageLocations as $storageLocation) {
            $preparedStorageLocations[$storageLocation->id] = $storageLocation->name;
        }
        return $preparedStorageLocations;
    }

}
