<?php

namespace Admin\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class TimebasedCurrencyPaymentsController extends AdminAppController
{

    public function isAuthorized($user)
    {
        switch ($this->request->action) {
            case 'myPaymentsCustomer':
            case 'add':
                return $this->AppAuth->isTimebasedCurrencyEnabledForCustomer();
                break;
            case 'myPaymentsManufacturer':
            case 'myPaymentDetailsManufacturer':
                return $this->AppAuth->isTimebasedCurrencyEnabledForManufacturer();
                break;
            default:
                return parent::isAuthorized($user);
                break;
        }
    }

    public function beforeFilter(Event $event)
    {
        $this->TimebasedCurrencyPayment = TableRegistry::get('TimebasedCurrencyPayments');
        $this->TimebasedCurrencyOrder = TableRegistry::get('TimebasedCurrencyOrders');
        $this->TimebasedCurrencyOrderDetail = TableRegistry::get('TimebasedCurrencyOrderDetails');
        parent::beforeFilter($event);
    }
    
    public function add()
    {
        $this->RequestHandler->renderAs($this, 'json');
        
        $this->loadComponent('Sanitize');
        $this->request->data = $this->Sanitize->trimRecursive($this->request->getData());
        $this->request->data = $this->Sanitize->stripTagsRecursive($this->request->getData());
        
        $hours = (int) $this->request->getData('hours');
        $minutes = (int) $this->request->getData('minutes');
        $customerId = (int) $this->request->getData('customerId');
        $manufacturerId = (int) $this->request->getData('manufacturerId');
        $text = $this->request->getData('text');
        
        $seconds = $hours * 3600 + $minutes * 60;
        $newPaymentEntity = $this->TimebasedCurrencyPayment->newEntity(
            [
                'status' => APP_ON,
                'text' => $text,
                'id_customer' => $customerId,
                'id_manufacturer' => $manufacturerId,
                'seconds' => $seconds,
                'created_by' => $this->AppAuth->getUserId()
            ]
        );
        
        if (!empty($newPaymentEntity->getErrors())) {
            $status = 0;
            $message = 'Fehler!';
        } else {
            $newPayment = $this->TimebasedCurrencyPayment->save($newPaymentEntity);
            $message = 'timebased currendy payment saved correctly';
        }
        
        $this->set('data', [
            'status' => 1,
            'msg' => $message,
            'seconds' => $seconds,
            'paymentId' => $newPayment->id
        ]);
        
        $this->set('_serialize', 'data');
        
    }
    
    public function myPaymentsManufacturer()
    {
        $this->set('title_for_layout', 'Mein ' . Configure::read('appDb.FCS_TIMEBASED_CURRENCY_NAME') . 'konto');
        
        $timebasedCurrencyOrderDetails = $this->TimebasedCurrencyOrderDetail->find('all', [
            'conditions' => [
                'Products.id_manufacturer' => $this->AppAuth->getManufacturerId()
            ],
            'contain' => [
                'OrderDetails.Products',
                'OrderDetails.Orders.Customers'
            ]
        ]);
        
        $payments = [];
        foreach($timebasedCurrencyOrderDetails as $timebasedCurrencyOrderDetail) {
            $payments[$timebasedCurrencyOrderDetail->order_detail->order->id_customer] = [
                'customerId' => $timebasedCurrencyOrderDetail->order_detail->order->customer->id_customer,
                'customerName' => $timebasedCurrencyOrderDetail->order_detail->order->customer->name
            ];
        }
        
        foreach($payments as &$payment) {
            $payment['text'] = '';
            $payment['secondsDone'] = $this->TimebasedCurrencyPayment->getSum($this->AppAuth->getManufacturerId(), $payment['customerId']);
            $payment['secondsOpen'] = $this->TimebasedCurrencyOrderDetail->getSum($this->AppAuth->getManufacturerId(), $payment['customerId']) * -1;
            $payment['creditBalance'] = $payment['secondsOpen'] + $payment['secondsDone'];
        }
        
        $this->set('payments', $payments);
        
        $sumPayments = $this->TimebasedCurrencyPayment->getSum($this->AppAuth->getManufacturerId());
        $this->set('sumPayments', $sumPayments);
        
        $sumOrders = $this->TimebasedCurrencyOrderDetail->getSum($this->AppAuth->getManufacturerId());
        $this->set('sumOrders', $sumOrders * -1);
        
        $creditBalance = $sumPayments - $sumOrders;
        $this->set('creditBalance', $creditBalance);
        
    }
    
    public function myPaymentsCustomer()
    {
        $this->set('title_for_layout', 'Mein ' . Configure::read('appDb.FCS_TIMEBASED_CURRENCY_NAME') . 'konto');
        $this->paymentListCustomer(null, $this->AppAuth->getUserId());
        $this->set('paymentBalanceTitle', 'Mein Kontostand');
        $this->render('paymentsCustomer');
    }
    
    public function myPaymentDetailsManufacturer($customerId)
    {
        $this->Customer = TableRegistry::get('Customers');
        $customer = $this->Customer->find('all', [
            'conditions' => [
                'Customers.id_customer' => $customerId
            ],
        ])->first();
        
        $this->set('title_for_layout', 'Detail-Ansicht ' . Configure::read('appDb.FCS_TIMEBASED_CURRENCY_NAME') . 'konto von ' . $customer->name);
        $this->set('paymentBalanceTitle', 'Kontostand von ' . $customer->name);
        $this->paymentListCustomer($this->AppAuth->getManufacturerId(), $customerId);
        $this->render('paymentsCustomer');
    }
    
    private function paymentListCustomer($manufacturerId = null, $customerId)
    {
     
        $manufacturersForDropdown = $this->TimebasedCurrencyOrderDetail->getManufacturersForDropdown($customerId);
        $this->set('manufacturersForDropdown', $manufacturersForDropdown);
        
        $timebasedCurrencyOrders = $this->TimebasedCurrencyOrderDetail->getOrders($manufacturerId, $customerId);
        
        $payments = [];
        foreach($timebasedCurrencyOrders as $orderId => $timebasedCurrencyOrder) {
            $payments[] = [
                'dateRaw' => $timebasedCurrencyOrder['order']->date_add,
                'date' => $timebasedCurrencyOrder['order']->date_add->i18nFormat(Configure::read('DateFormat.DatabaseWithTime')),
                'year' => $timebasedCurrencyOrder['order']->date_add->i18nFormat(Configure::read('DateFormat.de.Year')),
                'secondsOpen' => $timebasedCurrencyOrder['SumSeconds'] * - 1,
                'secondsDone' => null,
                'type' => 'order',
                'approval' => APP_OFF,
                'text' => Configure::read('app.htmlHelper')->link(
                    'Bestellung Nr. ' . $orderId . ' (' . 
                        Configure::read('app.htmlHelper')->getOrderStates()[$timebasedCurrencyOrder['order']->current_state] . ')',
                        '/admin/order-details/?dateFrom=' . $timebasedCurrencyOrder['order']->date_add->i18nFormat(Configure::read('DateFormat.de.DateLong2')) . 
                        '&dateTo=' . $timebasedCurrencyOrder['order']->date_add->i18nFormat(Configure::read('DateFormat.de.DateLong2')) .
                        '&orderId=' . $orderId . '&customerId=' . $timebasedCurrencyOrder['order']->id_customer, [
                    'title' => 'Bestellung anzeigen'
                ]),
                'manufacturerName' => '',
                'payment_id' => null
            ];
        }
        
        $conditions = [
            'TimebasedCurrencyPayments.id_customer' => $customerId
        ];
        if ($manufacturerId) {
            $conditions['TimebasedCurrencyPayments.id_manufacturer'] = $manufacturerId;
        }
        $timebasedCurrencyPayments = $this->TimebasedCurrencyPayment->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'Manufacturers'
            ]
        ]);
        foreach($timebasedCurrencyPayments as $timebasedCurrencyPayment) {
            $payments[] = [
                'dateRaw' => $timebasedCurrencyPayment->created,
                'date' => $timebasedCurrencyPayment->created->i18nFormat(Configure::read('DateFormat.DatabaseWithTime')),
                'year' => $timebasedCurrencyPayment->created->i18nFormat(Configure::read('DateFormat.de.Year')),
                'secondsOpen' => null,
                'secondsDone' => $timebasedCurrencyPayment->seconds,
                'type' => 'payment',
                'approval' => $timebasedCurrencyPayment->approval,
                'text' => $timebasedCurrencyPayment->text,
                'manufacturerName' => $timebasedCurrencyPayment->manufacturer->name,
                'payment_id' => $timebasedCurrencyPayment->id_payment
            ];
        }
        
        $payments = Hash::sort($payments, '{n}.date', 'desc');
        $this->set('payments', $payments);
        
        $sumPayments = $this->TimebasedCurrencyPayment->getSum(null, $customerId);
        $this->set('sumPayments', $sumPayments);
        
        $sumOrders = $this->TimebasedCurrencyOrderDetail->getSum(null, $customerId);
        $this->set('sumOrders', $sumOrders * -1);
        
        $creditBalance = $sumPayments - $sumOrders;
        $this->set('creditBalance', $creditBalance);
        
    }
    
}
