<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

use App\Controller\Component\StringComponent;
use App\Lib\Pdf\ListTcpdf;
use Cake\I18n\I18n;

$pdf = new ListTcpdf();
$pdf->SetLeftMargin(12);
$pdf->SetRightMargin(12);

$title = __('Terms_of_use');
$pdf->SetTitle($title);
$pdf->infoTextForFooter = $title;

$pdf->AddPage();

$html = $this->element('legal/'.I18n::getLocale().'/termsOfUse');
$pdf->writeHTML($html, true, false, true, false, '');

echo $pdf->Output(StringComponent::createRandomString().'.pdf', $saveParam);
