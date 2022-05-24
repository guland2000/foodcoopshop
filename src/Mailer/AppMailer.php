<?php

namespace App\Mailer;

use App\Lib\OutputFilter\OutputFilter;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\Message;
use Cake\Datasource\FactoryLocator;

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
class AppMailer extends Mailer
{

    public $fallbackEnabled = true;

    public function __construct($addBccBackupAddress = true)
    {
        parent::__construct(null);

        if ($addBccBackupAddress && Configure::read('appDb.FCS_BACKUP_EMAIL_ADDRESS_BCC') != '') {
            $bccRecipients = [];
            $bccs = explode(',', Configure::read('appDb.FCS_BACKUP_EMAIL_ADDRESS_BCC'));
            foreach ($bccs as $bcc) {
                $bccRecipients[] = $bcc;
            }
            $this->addBcc($bccRecipients);
        }
    }

    /**
     * uses fallback transport config if default email transport config is wrong (e.g. password changed party)
     * @see credentials.php
     */
    public function send(?string $action = null, array $args = [], array $headers = []): array
    {
        $this->render();

        if (Configure::check('app.outputStringReplacements')) {
            $replacedSubject = OutputFilter::replace($this->getOriginalSubject(), Configure::read('app.outputStringReplacements'));
            $this->setSubject($replacedSubject);
            $replacedBody = OutputFilter::replace($this->getMessage()->getBodyHtml(), Configure::read('app.outputStringReplacements'));
            $this->getMessage()->setBodyHtml($replacedBody);
        }

        $queuedJobs = FactoryLocator::get('Table')->get('Queue.QueuedJobs');

        $message = new Message();
        $message->setFrom($this->getFrom());
        $message->setCc($this->getCc());
        $message->setBcc($this->getBcc());
        $message->setTo($this->getTo());
        $message->setSubject($this->getOriginalSubject());
        $message->setAttachments($this->getAttachments());
        $message->setBodyHtml($this->getMessage()->getBodyHtml());

        $queuedJobs->createJob('Queue.Email', ['settings' => $message]);

        return [];

    }
}
