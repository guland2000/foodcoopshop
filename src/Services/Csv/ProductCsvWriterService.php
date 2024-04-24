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
namespace App\Services\Csv;

use Cake\Datasource\FactoryLocator;
use App\Services\DomDocumentService;
use App\Model\Entity\Product;

class ProductCsvWriterService extends BaseCsvWriterService
{

    private $productIds;

    public function setProductIds($productIds)
    {
        $this->productIds = $productIds;
    }

    public function getHeader()
    {
        return [
            __('Id'),
            __('Product'),
            __('Manufacturer'),
            __('Unit'),
            __('Amount'),
        ];
    }

    public function getRecords()
    {
        $productsTable =FactoryLocator::get('Table')->get('Products');
        $products = $productsTable->getProductsForBackend(
            productIds: $this->productIds,
            manufacturerId: 'all',
            active: 'all',
            addProductNameToAttributes: true,
        );

        $domDocumentService = new DomDocumentService();
        $records = [];
        foreach ($products as $product) {

            $isMainProduct = $productsTable->isMainProduct($product);
            if ($isMainProduct && !empty($product->product_attributes)) {
                continue;
            }

            $domDocumentService->loadHTML($product->name);
            $productName = $domDocumentService->getItemByClass('product-name')->item(0)?->nodeValue;
            $unit = $domDocumentService->getItemByClass('unity-for-dialog')->item(0)?->nodeValue ?? $domDocumentService->getItemByClass('quantity-in-units')->item(0)?->nodeValue ?? '';
            
            if (!$isMainProduct) {
                $explodedName = explode(Product::NAME_SEPARATOR, $product->name);
                if (count($explodedName) == 2) {
                    $unit = $explodedName[1];
                }   
                if ($product->unit->price_per_unit_enabled) {
                    $productName = $product->unchanged_name;
                    $unit = $product->name;
                }
            }

            $records[] = [
                $product->id_product,
                $productName,
                $product->manufacturer->name,
                $unit,
                $product->stock_available->quantity,
            ];
        }

        return $records;
    }

}