<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

use App\Application;
use App\Test\TestCase\AppCakeTestCase;
use App\Test\TestCase\Traits\AppIntegrationTestTrait;
use App\Test\TestCase\Traits\LoginTrait;
use Cake\Console\CommandRunner;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use Cake\TestSuite\EmailTrait;

class SendInvoicesToCustomersShellTest extends AppCakeTestCase
{

    use AppIntegrationTestTrait;
    use EmailTrait;
    use LoginTrait;

    public $commandRunner;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareSendingInvoices();
        $this->commandRunner = new CommandRunner(new Application(ROOT . '/config'));
    }

    public function testContentOfInvoice()
    {

        $this->changeConfiguration('FCS_SEND_INVOICES_TO_CUSTOMERS', 1);
        $this->loginAsSuperadmin();

        $customerId = Configure::read('test.superadminId');
        $this->prepareOrdersAndPayments($customerId);

        $this->get('/admin/customers/getInvoice.pdf?customerId='.$customerId.'&paidInCash=1&outputType=html');
//         pr($this->_response);
//         exit;
        $expectedResult = file_get_contents(TESTS . 'config' . DS . 'data' . DS . 'customerInvoice.html');
        $expectedResult = $this->getCorrectedLogoPathInHtmlForPdfs($expectedResult);
        $this->assertResponseContains($expectedResult);
    }

    public function testSendInvoices()
    {

        $this->changeConfiguration('FCS_SEND_INVOICES_TO_CUSTOMERS', 1);
        $this->loginAsSuperadmin();

        $customerId = Configure::read('test.superadminId');
        $this->prepareOrdersAndPayments($customerId);

        $cronjobRunDay = '2020-11-10 10:20:30';

        $this->commandRunner->run(['cake', 'send_invoices_to_customers', $cronjobRunDay]);
        $this->commandRunner->run(['cake', 'queue', 'runworker', '-q']);

        $pdfFilename = DS . 'files_private' . DS . 'invoices' . DS . '2020' . DS . '11' . DS . '2020-11-10_Demo-Superadmin_92_Rechnung_2020-000001_FoodCoop-Test.pdf';
        $this->assertFileExists(ROOT . $pdfFilename);

        $this->Invoice = $this->getTableLocator()->get('Invoices');
        $invoice = $this->Invoice->find('all', [
            'conditions' => [
                'Invoices.id_customer' => $customerId,
            ],
            'contain' => [
                'InvoiceTaxes',
            ],
        ])->first();

        $this->assertEquals($invoice->id, 1);
        $this->assertEquals($invoice->id_manufacturer, 0);
        $this->assertEquals($invoice->created, new FrozenDate($cronjobRunDay));
        $this->assertEquals($invoice->invoice_number, '2020-000001');
        $this->assertEquals($invoice->filename, str_replace('\\', '/', $pdfFilename));

        $this->doAssertInvoiceTaxes($invoice->invoice_taxes[0], 0, 4.54, 0, 4.54);
        $this->doAssertInvoiceTaxes($invoice->invoice_taxes[1], 10, 33.69, 3.38, 37.07);
        $this->doAssertInvoiceTaxes($invoice->invoice_taxes[2], 13, 0.55, 0.07, 0.62);
        $this->doAssertInvoiceTaxes($invoice->invoice_taxes[3], 20, -3.50, -0.70, -4.20);

        $this->Payment = $this->getTableLocator()->get('Payments');
        $payments = $this->Payment->getCustomerDepositNotBilled($customerId);

        foreach($payments as $payment) {
            $this->assertEquals($payment->id_invoice, 1);
        }

    }

    protected function doAssertInvoiceTaxes($data, $taxRate, $excl, $tax, $incl)
    {
        $this->assertEquals($data->tax_rate, $taxRate);
        $this->assertEquals($data->total_price_tax_excl, $excl);
        $this->assertEquals($data->total_price_tax, $tax);
        $this->assertEquals($data->total_price_tax_incl, $incl);
    }

    protected function prepareOrdersAndPayments($customerId)
    {

        $pickupDay = '2018-02-02';

        // add product with price pre unit
        $productIdA = 347; // forelle
        $productIdB = '348-11'; // rindfleisch + attribute
        $this->addProductToCart($productIdA, 1);
        $this->addProductToCart($productIdB, 3);
        $this->finishCart(1, 1, '', null, $pickupDay);

        $this->OrderDetail = $this->getTableLocator()->get('OrderDetails');
        $query = 'UPDATE ' . $this->OrderDetail->getTable().' SET pickup_day = :pickupDay WHERE id_order_detail IN(4,5);';
        $params = [
            'pickupDay' => $pickupDay,
        ];
        $statement = $this->dbConnection->prepare($query);
        $statement->execute($params);

        $this->addPayment($customerId, 2.0, 'deposit', 0, '', $pickupDay);
        $this->addPayment($customerId, 3.2, 'deposit', 0, '', $pickupDay);

    }

}
