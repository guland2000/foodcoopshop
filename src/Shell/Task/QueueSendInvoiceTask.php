<?php
namespace App\Shell\Task;

use App\Mailer\AppMailer;
use Queue\Shell\Task\QueueTask;
use Queue\Shell\Task\QueueTaskInterface;

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

class QueueSendInvoiceTask extends QueueTask implements QueueTaskInterface {


    use UpdateActionLogTrait;

    public $timeout = 30;

    public $retries = 2;

    public function run(array $data, $jobId) : void
    {

        
    }

}
?>