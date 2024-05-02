<?php
declare(strict_types=1);

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 4.1.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

if (!($identity->isSuperadmin() || $identity->isAdmin())) {
    return false;
}

$queryString = '';
$queryParams = $this->request->getQueryParams() ?? [];
$queryString = '?' . http_build_query($queryParams);
$exportUrl = '/admin/customers/export' . $queryString;

echo '<a id="exportCustomersButton" target="_blank" class="dropdown-item" href="'.$exportUrl.'"><i class="fa-fw fas fa-file-export ok"></i> ' . __d('admin', 'Export_{0}', [__d('admin', 'Members')]) . '</a>';
