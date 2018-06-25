<?php
namespace Network\Test\TestCase;

use App\Test\TestCase\AppCakeTestCase;
use Cake\Core\Configure;
use Cake\View\View;
use Network\View\Helper\NetworkHelper;

/**
 * SyncControllerTest
 *
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop Network Plugin 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class SyncsControllerTest extends AppCakeTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->Network = new NetworkHelper(new View());
    }

    public function testDenyAccessIfVariableMemberFeeEnabled()
    {
        $this->loginAsVegetableManufacturer();
        $this->browser->get($this->Network->getSyncProducts());
        $this->assertAccessDeniedWithRedirectToLoginForm();
    }

    public function testDenyAccessIfVariableMemberFeeDisabledAndManufacturerHasNoSyncDomains()
    {
        $manufacturerId = $this->Customer->getManufacturerIdByCustomerId(Configure::read('test.vegetableManufacturerId'));
        $this->changeManufacturer($manufacturerId, 'enabled_sync_domains', null);
        $this->loginAsVegetableManufacturer();
        $this->browser->get($this->Network->getSyncProducts());
        $this->assertAccessDeniedWithRedirectToLoginForm();
    }

    public function testAllowAccessProductsIfVariableMemberFeeDisabled()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();
        $this->browser->get($this->Network->getSyncProducts());
        $this->assert200OkHeader();
    }

    public function testAllowAccessProductDataIfVariableMemberFeeDisabled()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();
        $this->browser->get($this->Network->getSyncProductData());
        $this->assert200OkHeader();
    }

    public function testSaveProductAssociationWithWrongDomain()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();
        $domain = 'http://www.not-available-domain.at';
        $response = $this->saveProductRelation(152, 152, '', $domain);

        $this->assertFalse((boolean) $response->status);
        $this->assertRegExpWithUnquotedString($domain, $response->msg);
        $this->assert200OkHeader();
    }

    public function testSaveProductAssociationForProductThatIsNotOwnedByLoggedInManufacturer()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();
        $manufacturerId = $this->Customer->getManufacturerIdByCustomerId(Configure::read('test.vegetableManufacturerId'));

        $productId = 47; // joghurt, owner: milk manufactuer
        $domain = $this->getCorrectedServerName();
        $productName = 'Joghurt';
        $response = $this->saveProductRelation($productId, $productId, $productName, $domain);

        $this->assertFalse((boolean) $response->status);
        $this->assertRegExpWithUnquotedString('Das Produkt ' . $productId . ' ist kein Produkt von Hersteller ' . $manufacturerId, $response->msg);
        $this->assert200OkHeader();
    }

    public function testCorrectSaveProductAssociation()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();

        $productId = 339;
        $domain = $this->getCorrectedServerName();
        $productName = 'Kartoffel';
        $response = $this->saveProductRelation($productId, $productId, $productName, $domain);
        $this->assertTrue($response->status);
        $this->assertNotEmpty($response->product);
        $this->assertEquals($response->product->localProductId, $productId);
        $this->assertEquals($response->product->remoteProductId, $productId);
        $this->assertEquals($response->product->domain, $domain);
        $this->assertEquals($response->product->productName, strip_tags($productName, '<span>'));
        $this->assert200OkHeader();
    }

    public function testCorrectDeleteProductAssociation()
    {
        $this->disableVariableMemberFee();
        $this->loginAsVegetableManufacturer();

        $productId = 339;
        $domain = $this->getCorrectedServerName();
        $productName = 'Kartoffel';
        $this->saveProductRelation($productId, $productId, $productName, $domain);

        $response = $this->deleteProductRelation($productId, $productId, $productName, $domain);
        $this->assertTrue($response->status);
        $this->assertNotEmpty($response->syncProduct);
        $this->assertEquals($response->syncProduct->local_product_id, $productId);
        $this->assertEquals($response->syncProduct->remote_product_id, $productId);
        $this->assertEquals($response->syncProduct->local_product_attribute_id, 0);
        $this->assertEquals($response->syncProduct->remote_product_attribute_id, 0);
        $this->assert200OkHeader();
    }

    private function getCorrectedServerName()
    {
        return str_replace('https', 'http', Configure::read('app.cakeServerName'));
    }

    /**
     * @param int $productId
     * @return json string
     */
    private function deleteProductRelation($localProductId, $remoteProductId, $productName, $domain)
    {
        $this->browser->ajaxPost($this->Network->getDeleteProductRelation(), [
            'product' =>
                [
                    'localProductId' => $localProductId,
                    'remoteProductId' => $remoteProductId,
                    'domain' => $domain,
                    'productName' => $productName
                ]
            ]);
        return $this->browser->getJsonDecodedContent();
    }

    /**
     * @param int $productId
     * @return json string
     */
    private function saveProductRelation($localProductId, $remoteProductId, $productName, $domain)
    {
        $this->browser->ajaxPost($this->Network->getSaveProductRelation(), [
            'product' =>
                [
                    'localProductId' => $localProductId,
                    'remoteProductId' => $remoteProductId,
                    'domain' => $domain,
                    'productName' => $productName
                ]
            ]);
        return $this->browser->getJsonDecodedContent();
    }

    private function disableVariableMemberFee()
    {
        $this->changeReadOnlyConfiguration('FCS_USE_VARIABLE_MEMBER_FEE', 0);
        $manufacturerId = $this->Customer->getManufacturerIdByCustomerId(Configure::read('test.vegetableManufacturerId'));
        $this->changeManufacturer($manufacturerId, 'variable_member_fee', 0);
    }
}
