<?php
declare(strict_types=1);

namespace Admin\Traits\Customers;

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

trait EditCommentTrait {

    public function editComment()
    {
        $this->request = $this->request->withParam('_ext', 'json');

        $customerId = $this->getRequest()->getData('customerId');
        $customerComment = htmlspecialchars_decode($this->getRequest()->getData('customerComment'));

        $this->Customer = $this->getTableLocator()->get('Customers');
        $oldCustomer = $this->Customer->find('all',
        conditions: [
            'Customers.id_customer' => $customerId
        ],
        contain: [
            'AddressCustomers'
        ])->first();

        $this->Customer->AddressCustomers->save(
            $this->Customer->AddressCustomers->patchEntity(
                $oldCustomer->address_customer,
                [
                    'comment' => $customerComment
                ]
                )
            );

        $this->Flash->success(__d('admin', 'The_comment_was_changed_successfully.'));

        $this->ActionLog = $this->getTableLocator()->get('ActionLogs');
        $this->ActionLog->customSave('customer_comment_changed', $this->identity->getId(), $customerId, 'customers', __d('admin', 'The_comment_of_the_member_{0}_was_changed:', ['<b>' . $oldCustomer->name . '</b>']) . ' <div class="changed">' . $customerComment . ' </div>');

        $this->set([
            'status' => 1,
            'msg' => 'ok',
        ]);
        $this->viewBuilder()->setOption('serialize', ['status', 'msg']);
    }

}