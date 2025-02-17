<?php
declare(strict_types=1);

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.2.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

use Cake\Core\Configure;

$multiplePickupDayMode = count($pickupDay) == 2;
$queryParams = $this->request->getQueryParams();
if ($multiplePickupDayMode) {
    unset($queryParams['pickupDay'][1]);
} else {
    if (!isset($queryParams['pickupDay'])) {
        $queryParams['pickupDay'] = $pickupDay;
    }
    $queryParams['pickupDay'][] = date(Configure::read('DateFormat.DateShortAlt'), Configure::read('app.timeHelper')->getCurrentDay());
}
$queryString = '';
if (!empty($queryParams)) {
    $queryString = '?' . http_build_query($queryParams);
}
$hrefMultiplePickupDay = $this->request->getUri()->getPath() . $queryString;

echo '<a class="btn btn-outline-light button" href="'.$hrefMultiplePickupDay.'"><i class="far fa-calendar-' . ($multiplePickupDayMode ? 'plus' : 'minus') .'"></i> ' . __d('admin', 'Multiple_pickup_days') . ': ' . $this->Html->getYesNo($multiplePickupDayMode) . '</a>';

?>