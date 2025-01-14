<?php
declare(strict_types=1);

use App\Lib\Error\Exception\InvalidParameterException;
use App\Model\Table\CronjobLogsTable;
use App\Test\TestCase\AppCakeTestCase;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.3.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class CronjobsTableTest extends AppCakeTestCase
{

    protected $Cronjob;

    public function setUp(): void
    {
        parent::setUp();
        $this->Cronjob = $this->getTableLocator()->get('Cronjobs');
    }

    public function testEditDailyValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'day',
            'day_of_month' => 4,
            'weekday' => 'Sunday',
            'not_before_time' => 'wrong-time',
        ]);
        $errors = $result->getErrors();
        $this->assertEquals('Beim Interval "täglich" bitte keinen Tag (Monat) angeben.', $errors['day_of_month']['time-interval-day-or-week-no-day-of-month']);
        $this->assertEquals('Beim Interval "täglich" bitte keinen Wochentag angeben.', $errors['weekday']['time-interval-day-or-month-no-weekday']);
        $this->assertEquals('Bitte gib eine gültige Uhrzeit ein.', $errors['not_before_time']['time']);
    }

    public function testEditDailyOk()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'day',
            'day_of_month' => '',
            'weekday' => '',
            'not_before_time' => '18:00:00',
        ]);
        $this->assertEquals(false, $result->hasErrors());
    }

    public function testEditWeeklyValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'week',
            'day_of_month' => '2',
            'weekday' => '',
        ]);
        $errors = $result->getErrors();
        $this->assertEquals('Bitte wähle einen Wochentag aus.', $errors['weekday']['_empty']);
        $this->assertEquals('Beim Interval "wöchentlich" bitte keinen Tag (Monat) angeben.', $errors['day_of_month']['time-interval-day-or-week-no-day-of-month']);
    }

    public function testEditWeeklyOk() {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'week',
            'day_of_month' => '',
            'weekday' => 'Sunday',
        ]);
        $this->assertEquals(false, $result->hasErrors());
    }

    public function testPickupReminderValidation()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'week',
            'day_of_month' => '2',
            'weekday' => '',
        ]);
        $errors = $result->getErrors();
        $this->assertEquals('Bitte wähle einen Wochentag aus.', $errors['weekday']['_empty']);
        $this->assertEquals('Beim Interval "wöchentlich" bitte keinen Tag (Monat) angeben.', $errors['day_of_month']['time-interval-day-or-week-no-day-of-month']);
    }

    public function testEditPickupReminderValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'name' => 'PickupReminder',
            'time_interval' => 'month',
            'day_of_month' => '',
            'weekday' => 'Sunday',
        ],
            ['validate' => 'PickupReminder'],
        );
        $errors = $result->getErrors();
        $this->assertEquals('Das Intervall muss "wöchentlich" sein.', $errors['time_interval']['equals']);
    }

    public function testEditEmailOrderReminderValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'name' => 'PickupReminder',
            'time_interval' => 'month',
            'day_of_month' => '',
            'weekday' => 'Sunday',
        ],
            ['validate' => 'EmailOrderReminder'],
        );
        $errors = $result->getErrors();
        $this->assertEquals('Das Intervall muss "wöchentlich" sein.', $errors['time_interval']['equals']);
    }

    public function testEditSendDeliveryNotesValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'name' => 'SendDeliveryNotes',
            'time_interval' => 'week',
            'day_of_month' => '',
            'weekday' => 'Sunday',
        ],
            ['validate' => 'SendDeliveryNotes'],
        );
        $errors = $result->getErrors();
        $this->assertEquals('Das Intervall muss "monatlich" sein.', $errors['time_interval']['equals']);
    }

    public function testEditSendInvoicesToManufacturersValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'name' => 'SendInvoicesToManufacturers',
            'time_interval' => 'day',
            'day_of_month' => '',
            'weekday' => '',
        ],
            ['validate' => 'SendInvoicesToManufacturers'],
        );
        $errors = $result->getErrors();
        $this->assertEquals('Das Intervall muss "monatlich" sein.', $errors['time_interval']['equals']);
    }

    public function testEditSendOrderListsValidations()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'name' => 'SendOrderLists',
            'time_interval' => 'week',
            'day_of_month' => '',
            'weekday' => '',
        ],
            ['validate' => 'SendOrderLists'],
        );
        $errors = $result->getErrors();
        $this->assertEquals('Das Intervall muss "täglich" sein.', $errors['time_interval']['equals']);
    }

    public function testEditMonthlyOk()
    {
        $entity = $this->Cronjob->get(1);
        $result = $this->Cronjob->patchEntity($entity, [
            'time_interval' => 'month',
            'day_of_month' => '2',
            'weekday' => '',
        ]);
        $this->assertEquals(false, $result->hasErrors());
    }

    public function testRunSunday()
    {
        $time = '2018-10-21 23:00:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals($executedCronjobs[0]['created'], $time);

        // run again, no cronjobs called
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(0, count($executedCronjobs));
    }

    public function testRunMonday()
    {
        $time = '2018-10-22 23:00:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(2, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['time_interval'], 'day');
        $this->assertEquals($executedCronjobs[1]['time_interval'], 'week');
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testPreviousCronjobLogFailure()
    {
        $time = '2018-10-22 23:00:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->CronjobLogs->save(
            $this->Cronjob->CronjobLogs->newEntity(
                [
                    'created' => $this->Time->getTimeObjectUTC($time),
                    'cronjob_id' => 1,
                    'success' => CronjobLogsTable::FAILURE,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(2, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['time_interval'], 'day');
        $this->assertEquals($executedCronjobs[1]['time_interval'], 'week');
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testPreviousCronjobLogRunning()
    {
        $time = '2018-10-22 23:00:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->CronjobLogs->save(
            $this->Cronjob->CronjobLogs->newEntity(
                [
                    'created' => $this->Time->getTimeObjectUTC($time),
                    'cronjob_id' => 1,
                    'success' => CronjobLogsTable::RUNNING,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(1, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['time_interval'], 'week');
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testCronjobNotYetExecutedWithinTimeInterval()
    {
        $time = '2018-10-23 22:30:01';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->CronjobLogs->save(
            $this->Cronjob->CronjobLogs->newEntity(
                [
                    'created' => $this->Time->getTimeObjectUTC('2018-10-22 22:30:00'),
                    'cronjob_id' => 1,
                    'success' => CronjobLogsTable::SUCCESS,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(1, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['time_interval'], 'day');
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testCronjobAlreadyExecutedWithinTimeInterval()
    {
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC('2018-10-23 22:29:59')->toUnixString();
        $this->Cronjob->CronjobLogs->save(
            $this->Cronjob->CronjobLogs->newEntity(
                [
                    'created' => $this->Time->getTimeObjectUTC('2018-10-22 22:30:01'),
                    'cronjob_id' => 1,
                    'success' => CronjobLogsTable::SUCCESS,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(0, count($executedCronjobs));
    }

    public function testCronjobWithInvalidParameterException()
    {
        $time = '2018-10-23 22:31:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(1),
                [
                    'name' => 'TestCronjobWithInvalidParameterException'
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(1, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['success'], 0);
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testCronjobAlreadyExecutedOnCurrentDay()
    {
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC('2018-10-25 22:30:02')->toUnixString();
        $this->Cronjob->CronjobLogs->save(
            $this->Cronjob->CronjobLogs->newEntity(
                [
                    'created' => $this->Time->getTimeObjectUTC('2018-10-25 22:30:01'),
                    'cronjob_id' => 1,
                    'success' => CronjobLogsTable::SUCCESS,
                ]
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(0, count($executedCronjobs));
    }

    public function testRunMonthlyBeforeNotBeforeTime()
    {
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC('2018-10-11 07:29:00')->toUnixString();
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(1),
                [
                    'active' => APP_OFF,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(0, count($executedCronjobs));
    }

    public function testRunMonthlyAfterNotBeforeTime()
    {
        $time = '2018-10-11 07:31:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(1),
                [
                    'active' => APP_OFF,
                ],
            )
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(1, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testRunMonthlyLastDayOfMonthAfterNotBeforeTime()
    {
        $time = '2018-11-30 07:31:00';
        $this->Cronjob->cronjobRunDay = (int) $this->Time->getTimeObjectUTC($time)->toUnixString();
        $this->Cronjob->updateAll(
            [
                'active' => APP_OFF,
            ],
            [],
        );
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(3),
                [
                    'day_of_month' => 0,
                    'active' => APP_ON,
                ],
            ),
        );
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(1, count($executedCronjobs));
        $this->assertEquals($executedCronjobs[0]['created'], $time);
    }

    public function testInvalidWeekday()
    {
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(2),
                [
                    'weekday' => '',
                ]
            )
        );
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('weekday not available');
        $executedCronjobs = $this->Cronjob->run();
        $this->assertEquals(0, count($executedCronjobs));
        $this->assertEmpty(0, $this->Cronjob->CronjobLogs->find('all')->all());
    }

    public function testInvalidDayOfMonth()
    {
        $this->Cronjob->save(
            $this->Cronjob->patchEntity(
                $this->Cronjob->get(3),
                [
                    'day_of_month' => '',
                ]
            )
        );
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('day of month not available or not valid');
        $this->Cronjob->run();
        $this->assertEmpty(0, $this->Cronjob->CronjobLogs->find('all')->all());
    }

}