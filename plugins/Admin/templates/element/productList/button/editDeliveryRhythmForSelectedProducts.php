<?php
declare(strict_types=1);

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.4.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use Cake\Core\Configure;

if (empty($products)) {
    return false;
}

$this->element('addScript', [
    'script' => Configure::read('app.jsNamespace') . ".ModalProductDeliveryRhythmEdit.initBulk();"
]);
echo '<a id="editDeliveryRhythmForSelectedProducts" class="btn btn-outline-light" href="javascript:void(0);"><i class="far fa-clock"></i> ' . __d('admin', 'Edit_delivery_rhythm') . '</a>';
