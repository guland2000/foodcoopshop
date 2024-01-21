<?php
declare(strict_types=1);

namespace App\Policy;

use Cake\Http\ServerRequest;
use Authorization\Policy\RequestPolicyInterface;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.7.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class CartsPolicy implements RequestPolicyInterface
{

    public function canAccess($identity, ServerRequest $request)
    {
        switch ($request->getParam('action')) {
            case 'emptyCart':
            case 'addOrderToCart':
            case 'addLastOrderToCart':
            case 'detail':
            case 'finish':
            case 'orderSuccessful':
                return $identity !== null;
                break;
        }

        return true;
    }

}