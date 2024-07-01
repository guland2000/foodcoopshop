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

namespace App\Test\Fixture;

class ProductsFixture extends AppFixture
{
    public string $table = 'fcs_product';

    public array $records = [
        [
            'id_product' => 60,
            'id_manufacturer' => 15,
            'id_tax' => 3,
            'id_storage_location' => 1,
            'price' => 0.909091,
            'name' => 'Milch',
            'description' => '',
            'description_short' => '',
            'unity' => '1 Liter',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2014-06-11 21:20:24',
            'modified' => '2014-12-14 19:47:33',
        ],
        [
            'id_product' => 102,
            'id_manufacturer' => 4,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Frankfurter',
            'description' => '',
            'description_short' => '<p>2 Paar</p>',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2016-04-27 21:13:37',
            'modified' => '2014-09-19 14:32:51',
        ],
        [
            'id_product' => 103,
            'id_manufacturer' => 4,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 3.181819,
            'name' => 'Bratwürstel',
            'description' => '',
            'description_short' => '2 Paar',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2016-05-05 08:28:49',
            'modified' => '2014-08-16 14:05:58',
        ],
        [
            'id_product' => 163,
            'id_manufacturer' => 5,
            'id_tax' => 0,
            'id_storage_location' => 1,
            'price' => 1.363637,
            'name' => 'Mangold',
            'description' => '',
            'description_short' => '0,25kg',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2014-07-12 20:41:43',
            'modified' => '2017-07-26 13:24:10',
        ],
        [
            'id_product' => 339,
            'id_manufacturer' => 5,
            'id_tax' => 0,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Kartoffel',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2015-09-07 12:05:38',
            'modified' => '2015-02-26 13:54:07',
        ],
        [
            'id_product' => 340,
            'id_manufacturer' => 4,
            'id_tax' => 0,
            'id_storage_location' => 1,
            'price' => 4.545455,
            'name' => 'Beuschl',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2016-05-05 08:28:45',
            'modified' => '2015-06-23 14:52:53',
        ],
        [
            'id_product' => 344,
            'id_manufacturer' => 5,
            'id_tax' => 0,
            'id_storage_location' => 1,
            'price' => 0.636364,
            'name' => 'Knoblauch',
            'description' => '',
            'description_short' => '',
            'unity' => '100 g',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2015-10-05 17:22:40',
            'modified' => '2015-07-06 10:24:44',
        ],
        [
            'id_product' => 346,
            'id_manufacturer' => 5,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 1.652893,
            'name' => 'Artischocke',
            'description' => '',
            'description_short' => '',
            'unity' => 'Stück',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2015-08-19 09:35:46',
            'modified' => '2015-08-19 09:35:45',
        ],
        [
            'id_product' => 347,
            'id_manufacturer' => 4,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Forelle',
            'description' => '',
            'description_short' => '',
            'unity' => 'Stück',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2016-05-05 08:28:45',
            'modified' => '2015-06-23 14:52:53',
        ],
        [
            'id_product' => 348,
            'id_manufacturer' => 4,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Rindfleisch',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 0,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2018-05-17 16:15:33',
            'modified' => '2018-05-17 16:16:38',
        ],
        [
            'id_product' => 349,
            'id_manufacturer' => 5,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 4.545455,
            'name' => 'Lagerprodukt',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 1,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2018-08-16 12:15:48',
            'modified' => '2018-08-16 12:16:51',
        ],
        [
            'id_product' => 350,
            'id_manufacturer' => 5,
            'id_tax' => 2,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Lagerprodukt mit Varianten',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 1,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2018-08-16 12:19:06',
            'modified' => '2018-08-16 12:19:23',
        ],
        [
            'id_product' => 351,
            'id_manufacturer' => 5,
            'id_tax' => 1,
            'id_storage_location' => 1,
            'price' => 0.000000,
            'name' => 'Lagerprodukt 2',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 1,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2019-06-05 15:09:53',
            'modified' => '2019-06-05 15:10:08',
        ],
        [
            'id_product' => 352,
            'id_manufacturer' => 5,
            'id_tax' => 1,
            'id_storage_location' => 1,
            'price' => 1.200000,
            'name' => 'Lagerprodukt mit Gewichtsbarcode',
            'description' => '',
            'description_short' => '',
            'unity' => '',
            'is_declaration_ok' => 0,
            'is_stock_product' => 1,
            'active' => 1,
            'delivery_rhythm_type' => 'week',
            'delivery_rhythm_count' => 1,
            'delivery_rhythm_first_delivery_day' => NULL,
            'delivery_rhythm_order_possible_until' => NULL,
            'delivery_rhythm_send_order_list_weekday' => 3,
            'delivery_rhythm_send_order_list_day' => NULL,
            'created' => '2019-06-05 15:09:53',
            'modified' => '2019-06-05 15:10:08',
        ],
    ];

}
?>