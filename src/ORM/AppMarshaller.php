<?php
declare(strict_types=1);

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.0.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
namespace App\ORM;

use Cake\ORM\Marshaller;
use Cake\Log\Log;

class AppMarshaller extends Marshaller
{
    /**
     * {@inheritDoc}
     * @see \Cake\ORM\Marshaller::_validate()
     */
    protected function _validate(array $data, array $options, bool $isNew): array
    {
        $errors = parent::_validate($data, $options, $isNew);
        if (!empty($errors) && !(php_sapi_name() == 'cli' && $_SERVER['argv'][0] && preg_match('/phpunit/', $_SERVER['argv'][0]))) {
            Log::write('error', json_encode($errors));
        }
        return $errors;
    }
}
