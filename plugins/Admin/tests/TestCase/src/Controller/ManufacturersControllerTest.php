<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use App\Test\TestCase\AppCakeTestCase;
use Cake\Core\Configure;

class ManufacturersControllerTest extends AppCakeTestCase
{

    public $Manufacturer;

    public function setUp(): void
    {
        parent::setUp();
        $this->Manufacturer = $this->getTableLocator()->get('Manufacturers');
    }

    public function testAdd()
    {
        $this->loginAsSuperadmin();

        $manufacturerData = [
            'Manufacturers' => [
                'name' => 'Test Manufacturer',
                'bank_name' => 'Test Bank',
                'iban' => 'Iban',
                'bic' => 'bic',
                'no_delivery_days' => '',
                'active' => 1,
                'additional_text_for_invoice' => '',
                'uid_number' => '',
                'tmp_image' => '',
                'delete_image' => '',
                'firmenbuchnummer' => '<b>number</b>',
                'firmengericht' => '',
                'aufsichtsbehoerde' => '',
                'kammer' => '',
                'homepage' => '',
                'short_description' => '<i>Test Description</i>',
                'description' => '<b>Text</b><script>alert("evil");</script><img src=n onerror=alert("evil")>',
                'address_manufacturer' => [
                    'firstname' => '',
                    'lastname' => '',
                    'email' => 'fcs-demo-gemuese-hersteller@mailinator.com',
                    'phone_mobile' => '',
                    'phone' => '',
                    'address1' => 'Street 1',
                    'address2' => 'Street 2',
                    'postcode' => '',
                    'city' => 'Test City'
                ]
            ],
            'referer' => '/'
        ];

        $response = $this->add($manufacturerData);

        // provoke errors
        $this->assertRegExpWithUnquotedString(__d('admin', 'Errors_while_saving!'), $response);
        $this->assertRegExpWithUnquotedString('Bitte gib einen gültigen IBAN ein.', $response);
        $this->assertRegExpWithUnquotedString('Bitte gib einen gültigen BIC ein.', $response);
        $this->assertRegExpWithUnquotedString('Ein anderes Mitglied oder ein anderer Hersteller verwendet diese E-Mail-Adresse bereits.', $response);
        $this->assertRegExpWithUnquotedString('Bitte gib den Vornamen des Rechnungsempfängers an.', $response);
        $this->assertRegExpWithUnquotedString('Bitte gib den Nachnamen des Rechnungsempfängers an.', $response);

        // set proper data and post again
        $manufacturerData['Manufacturers']['iban'] = 'AT193357281080332578';
        $manufacturerData['Manufacturers']['bic'] = 'BFKKAT2K';
        $manufacturerData['Manufacturers']['address_manufacturer']['email'] = 'test-manufacturer@mailinator.com';
        $manufacturerData['Manufacturers']['address_manufacturer']['firstname'] = 'Test';
        $manufacturerData['Manufacturers']['address_manufacturer']['lastname'] = 'Manufacturers';
        $manufacturerData['Manufacturers']['homepage'] = 'www.foodcoopshop.com';

        $response = $this->add($manufacturerData);

        $this->assertRegExpWithUnquotedString('Der Hersteller <b>Test Manufacturer</b> wurde erstellt.', $response);

        // get inserted manufacturer from database and check detail page for patterns
        $manufacturer = $this->Manufacturer->find('all', [
            'conditions' => [
                'Manufacturers.name' => $manufacturerData['Manufacturers']['name']
            ],
            'contain' => [
                'AddressManufacturers'
            ]
        ])->first();

        $response = $this->httpClient->get($this->Slug->getManufacturerDetail($manufacturer->id_manufacturer, $manufacturer->name));
        $this->assertRegExpWithUnquotedString('<h1>' . $manufacturer->name, $response->getStringBody());

        $this->doTestCustomerRecord($manufacturer);

        $this->assertEquals($manufacturer->description, '<b>Text</b><img src="n" alt="n" />', 'tags must not be stripped');
        $this->assertEquals($manufacturer->short_description, '<i>Test Description</i>', 'tags must not be stripped');
        $this->assertEquals($manufacturer->firmenbuchnummer, 'number', 'tags must be stripped');
        $this->assertEquals($manufacturer->is_private, true);

        $this->logout();
    }

    public function testEditOptionsMain()
    {
        $this->loginAsSuperadmin();

        $manufacturerId = 4;
        $newSendOrderList = 0;
        $newSendInvoice = 0;
        $newSendOrderedProductPriceChangedNotification = 0;
        $newSendOrderedProductAmountChangedNotification = 0;
        $newSendInstantOrderNotification = 0;
        $newDefaultTaxId = 3;

        $newSendOrderListCc = ['office@rothauer-it.com', 'test@test.com'];
        $emailErrorMsg = 'Mindestens eine E-Mail-Adresse ist nicht gültig. Mehrere bitte mit , trennen (ohne Leerzeichen).';

        $this->httpClient->post(
            $this->Slug->getManufacturerEditOptions($manufacturerId),
            [
                'Manufacturers' => [
                    'send_order_list' => $newSendOrderList, // do not use 0 here
                    'send_invoice' => $newSendInvoice, // do not use 0 here
                    'send_order_list_cc' => 'office@rothauer-it.com;test@test.com', // wrong: comma expected as separator
                ]
            ]
        );
        $this->assertRegExpWithUnquotedString($emailErrorMsg, $this->httpClient->getContent());

        $this->httpClient->post(
            $this->Slug->getManufacturerEditOptions($manufacturerId),
            [
                'Manufacturers' => [
                    'send_order_list' => $newSendOrderList,
                    'send_invoice' => $newSendInvoice,
                    'send_order_list_cc' => 'office@rothauer-it.com,test@testcom' // wrong: no dot in domain
                ]
            ]
        );
        $this->assertRegExpWithUnquotedString($emailErrorMsg, $this->httpClient->getContent());

        $this->httpClient->post(
            $this->Slug->getManufacturerEditOptions($manufacturerId),
            [
                'Manufacturers' => [
                    'send_order_list' => $newSendOrderList,
                    'send_invoice' => $newSendInvoice,
                    'send_order_list_cc' => implode(',', $newSendOrderListCc), // correct
                    'send_ordered_product_price_changed_notification' => $newSendOrderedProductPriceChangedNotification,
                    'send_ordered_product_amount_changed_notification' => $newSendOrderedProductAmountChangedNotification,
                    'send_instant_order_notification' => $newSendInstantOrderNotification,
                    'default_tax_id' => $newDefaultTaxId,
                    // althouth the following property is not tested, it needs to be included in the request to avoid
                    // [InvalidArgumentException] Cannot convert value of type `boolean` to integer
                    'send_ordered_product_deleted_notification' => 1,
                ]
            ]
        );

        $manufacturerNew = $this->Manufacturer->find('all', [
            'conditions' => [
                'Manufacturers.id_manufacturer' => $manufacturerId
            ]
        ])->first();

        $sendOrderList = $this->Manufacturer->getOptionSendOrderList($manufacturerNew->send_order_list);
        $this->assertEquals($sendOrderList, $newSendOrderList, 'saving option send_order_list failed');

        $sendInvoice = $this->Manufacturer->getOptionSendInvoice($manufacturerNew->send_invoice);
        $this->assertEquals($sendInvoice, $newSendInvoice, 'saving option invoice failed');

        $sendOrderListCc = $this->Manufacturer->getOptionSendOrderListCc($manufacturerNew->send_order_list_cc);
        $this->assertEquals($sendOrderListCc, $newSendOrderListCc, 'saving option send_order_list_cc failed');

        $sendOrderedProductPriceChangedNotification = $this->Manufacturer->getOptionSendOrderedProductPriceChangedNotification($manufacturerNew->send_ordered_product_price_changed_notification);
        $this->assertEquals($sendOrderedProductPriceChangedNotification, $newSendOrderedProductPriceChangedNotification, 'saving option send_ordered_product_price_changed_notification failed');

        $sendOrderedProductAmountChangedNotification = $this->Manufacturer->getOptionSendOrderedProductAmountChangedNotification($manufacturerNew->send_ordered_product_amount_changed_notification);
        $this->assertEquals($sendOrderedProductAmountChangedNotification, $newSendOrderedProductAmountChangedNotification, 'saving option send_ordered_product_amount_changed_notification failed');

        $sendInstantOrderNotification = $this->Manufacturer->getOptionSendInstantOrderNotification($manufacturerNew->send_instant_order_notification);
        $this->assertEquals($sendInstantOrderNotification, $newSendInstantOrderNotification, 'saving option send_instant_order_notification failed');

        $defaultTaxId = $this->Manufacturer->getOptionDefaultTaxId($manufacturerNew->default_tax_id);
        $this->assertEquals($defaultTaxId, $newDefaultTaxId, 'saving option default_tax_id failed');

        $this->logout();
    }

    public function testEditOptionsNoDeliveryDays()
    {
        $this->loginAsSuperadmin();

        $manufacturerId = 15;
        $noDeliveryDays = date('Y-m-d', strtotime('friday next week'));
        $noDeliveryDayA = date('Y-m-d', strtotime($noDeliveryDays . ' + 10 day'));
        $noDeliveryDayB = date('Y-m-d', strtotime($noDeliveryDays . ' + 11 day'));

        $this->OrderDetail = $this->getTableLocator()->get('OrderDetails');
        $this->Product = $this->getTableLocator()->get('Products');

        $query = 'UPDATE ' . $this->OrderDetail->getTable().' SET pickup_day = :pickupDay WHERE id_order_detail IN(1);';
        $params = [
            'pickupDay' => $noDeliveryDayA,
        ];
        $statement = $this->dbConnection->prepare($query);
        $statement->execute($params);

        $query = 'UPDATE ' . $this->OrderDetail->getTable().' SET pickup_day = :pickupDay WHERE id_order_detail IN (2,3);';
        $params = [
            'pickupDay' => $noDeliveryDayB,
        ];
        $statement = $this->dbConnection->prepare($query);
        $statement->execute($params);

        $query = 'UPDATE ' . $this->Product->getTable().' SET id_manufacturer = :manufacturerId;';
        $params = [
            'manufacturerId' => $manufacturerId,
        ];
        $statement = $this->dbConnection->prepare($query);
        $statement->execute($params);

        $this->httpClient->followOneRedirectForNextRequest();
        $this->httpClient->post(
            $this->Slug->getManufacturerEditOptions($manufacturerId),
            [
                'Manufacturers' => [
                    'no_delivery_days' => [$noDeliveryDayA, $noDeliveryDayB]
                ],
                'referer' => '/'
            ]
        );
        $this->assertRegExpWithUnquotedString(
            'Für die folgenden Liefertag(e) sind bereits Bestellungen vorhanden: '
            . Configure::read('app.timeHelper')->formatToDateShort($noDeliveryDayA) . ' (1x), '
            . Configure::read('app.timeHelper')->formatToDateShort($noDeliveryDayB) . ' (2x)',
        $this->httpClient->getContent());

        $this->httpClient->followOneRedirectForNextRequest();
        $this->httpClient->post(
            $this->Slug->getManufacturerEditOptions($manufacturerId),
            [
                'Manufacturers' => [
                    'no_delivery_days' => [$noDeliveryDays],
                    // althouth the following property is not tested, it needs to be included in the request to avoid
                    // [InvalidArgumentException] Cannot convert value of type `boolean` to integer
                    'send_invoice' => 1,
                    'send_order_list' => 1,
                    'send_instant_order_notification' => 1,
                    'send_ordered_product_deleted_notification' => 1,
                    'send_ordered_product_price_changed_notification' => 1,
                    'send_ordered_product_amount_changed_notification' => 1,
                ],
                'referer' => '/'
            ]
        );

        $this->assertRegExpWithUnquotedString('wurden erfolgreich gespeichert.', $this->httpClient->getContent());

        $manufacturerNew = $this->Manufacturer->find('all', [
            'conditions' => [
                'Manufacturers.id_manufacturer' => $manufacturerId
            ]
        ])->first();

        $this->assertEquals($noDeliveryDays, $manufacturerNew->no_delivery_days);

        $this->logout();
    }

    public function testEditMain()
    {
        $this->loginAsSuperadmin();

        $manufacturerId = 4;

        $this->httpClient->post(
            $this->Slug->getManufacturerEdit($manufacturerId),
            [
                'Manufacturers' => [
                    'name' => 'Manufacturer & Sons',
                    'address_manufacturer' => [
                        'email' => 'fcs-demo-mitglied@mailinator.com'
                    ]
                ],
                'referer' => '/'
            ]
        );
        // test with valid customer email address must fail
        $this->assertRegExpWithUnquotedString('Ein anderes Mitglied oder ein anderer Hersteller verwendet diese E-Mail-Adresse bereits.', $this->httpClient->getContent());

        // test with valid manufacturer email address must fail
        $this->httpClient->post(
            $this->Slug->getManufacturerEdit($manufacturerId),
            [
                'Manufacturers' => [
                    'name' => 'Manufacturer & Sons',
                    'address_manufacturer' => [
                        'email' => 'fcs-demo-gemuese-hersteller@mailinator.com'
                    ],
                ],
                'referer' => '/'
            ]
        );
        $this->assertRegExpWithUnquotedString('Ein anderes Mitglied oder ein anderer Hersteller verwendet diese E-Mail-Adresse bereits.', $this->httpClient->getContent());

        // test with valid email address
        $this->httpClient->followOneRedirectForNextRequest();
        $this->httpClient->post(
            $this->Slug->getManufacturerEdit($manufacturerId),
            [
                'Manufacturers' => [
                    'name' => 'Manufacturer & Sons',
                    'homepage' => 'https://www.foodcoopshop.com',
                    'address_manufacturer' => [
                        'firstname' => 'firstname',
                        'lastname' => 'lastname',
                        'email' => 'new-email-address@mailinator.com'
                    ],
                ],
                'referer' => '/'
            ]
        );
        $this->assertRegExpWithUnquotedString('Der Hersteller <b>Manufacturer &amp; Sons</b> wurde geändert.', $this->httpClient->getContent());

        $manufacturer = $this->Manufacturer->find('all', [
            'conditions' => [
                'Manufacturers.id_manufacturer' => $manufacturerId
            ],
            'contain' => [
                'AddressManufacturers'
            ]
        ])->first();
        $this->doTestCustomerRecord($manufacturer);

        $this->logout();
    }

    private function doTestCustomerRecord($manufacturer)
    {
        $customerRecord = $this->Manufacturer->getCustomerRecord($manufacturer->address_manufacturer->email);
        $this->assertEquals($manufacturer->address_manufacturer->firstname, $customerRecord->firstname);
        $this->assertEquals($manufacturer->address_manufacturer->lastname, $customerRecord->lastname);
        $this->assertEquals($manufacturer->address_manufacturer->email, $customerRecord->email);
        $this->assertEquals(APP_ON, $customerRecord->active);
    }

    /**
     *
     * @param array $data
     * @return string
     */
    private function add($data)
    {
        $this->httpClient->followOneRedirectForNextRequest();
        $this->httpClient->post($this->Slug->getManufacturerAdd(), $data);
        return $this->httpClient->getContent();
    }
}
