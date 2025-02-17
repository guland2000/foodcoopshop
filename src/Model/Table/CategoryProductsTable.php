<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Traits\ProductCacheClearAfterSaveAndDeleteTrait;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class CategoryProductsTable extends AppTable
{

    use ProductCacheClearAfterSaveAndDeleteTrait;

    public function initialize(array $config): void
    {
        $this->setTable('category_product');
        parent::initialize($config);
        $this->setPrimaryKey('id_product');
        $this->belongsTo('Categories', [
            'foreignKey' => 'id_category'
        ]);
    }

}
