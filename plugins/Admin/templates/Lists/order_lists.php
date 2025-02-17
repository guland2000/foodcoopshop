<?php
declare(strict_types=1);

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
use Cake\Core\Configure;

?>
<div id="lists-list">

        <?php
        $this->element('addScript', [
        'script' => Configure::read('app.jsNamespace') . ".Helper.initDatepicker();
            $('input.datepicker').datepicker();".
            Configure::read('app.jsNamespace') . ".Admin.init();
        "
        ]);
    ?>

    <div class="filter-container">
        <?php echo $this->Form->create(null, ['type' => 'get']); ?>
            <?php echo __d('admin', 'Pickup_day'); ?> <?php echo $this->element('dateFields', ['dateFrom' => $dateFrom, 'showDateTo' => false, 'nameFrom' => 'dateFrom']); ?>
            <div class="right">
            <?php
                echo $this->element('headerIcons', ['helperLink' => $this->Html->getDocsUrl(__d('admin', 'docs_route_pick_up_products'))]);
            ?>
            </div>
        <?php echo $this->Form->end(); ?>
    </div>

    <?php
    echo '<h2 class="info">';
        if ($appAuth->isManufacturer()) {
            echo __d('admin', 'Here_you_find_the_unchanged_order_lists_that_were_sent_to_you.');
        } else {
            echo __d('admin', 'Here_you_find_the_unchanged_order_lists_that_were_sent_to_the_manufacturers.');
        }
    echo '</h2>';

    echo '<table class="list">';

    echo '<tr class="sort">';
    echo '<th>'.__d('admin', 'Pickup_day').'</th>';
    echo '<th>'.__d('admin', 'Manufacturer').'</th>';
    echo '<th>'.__d('admin', 'Order_list_by_product').'</th>';
    echo '<th>'.__d('admin', 'Order_list_by_member').'</th>';
    echo '</tr>';

    $i = 0;
    foreach ($files as $file) {
        $i ++;

        echo '<tr class="data">';

        echo '<td>';
        echo $this->Time->formatToDateShort($file['delivery_date']);
        echo '</td>';

        echo '<td>';
        echo $file['manufacturer_name'];
        echo '</td>';

        echo '<td>';
            foreach($file['product_lists'] as $productList) {
                echo $this->Html->link(
                    '<i class="fas ' . $productList['icon'] .' ok"></i> ' . $productList['label'],
                    $productList['link'],
                    [
                        'class' => 'btn btn-outline-light',
                        'target' => '_blank',
                        'escape' => false,
                    ]
                );
            }
        echo '</td>';

        echo '<td>';
            foreach($file['customer_lists'] as $customerList) {
                echo $this->Html->link(
                    '<i class="fas ' . $customerList['icon'] .' ok"></i> ' . $customerList['label'],
                    $customerList['link'],
                    [
                        'class' => 'btn btn-outline-light',
                        'target' => '_blank',
                        'escape' => false,
                    ]
                );
            }
        echo '</td>';

        echo '</tr>';
    }

    echo '<tr>';
    echo '<td colspan="4"><b>' . $i . '</b> '.__d('admin', '{0,plural,=1{record} other{records}}', $i).'</td>';
    echo '</tr>';

    echo '</table>';
    ?>

    <div class="sc"></div>

</div>
