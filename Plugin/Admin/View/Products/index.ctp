<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
?>
<div id="products">
     
     <?php
    $this->element('addScript', array(
        'script' => Configure::read('app.jsNamespace') . ".Admin.init();" . Configure::read('app.jsNamespace') . ".Admin.initProductChangeActiveState();" . Configure::read('app.jsNamespace') . ".Admin.initProductDepositEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductNameEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductQuantityEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductCategoriesEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductTaxEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initChangeNewState();" . Configure::read('app.jsNamespace') . ".Upload.initImageUpload('#products .add-image-button', foodcoopshop.Upload.saveProductImage, foodcoopshop.AppFeatherlight.closeLightbox);" . Configure::read('app.jsNamespace') . ".Admin.initAddProduct('#products');" . Configure::read('app.jsNamespace') . ".Admin.initAddProductAttribute('#products');" . Configure::read('app.jsNamespace') . ".Admin.initDeleteProductAttribute('#products');" . Configure::read('app.jsNamespace') . ".Admin.initSetDefaultAttribute('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductPriceEditDialog('#products');" . Configure::read('app.jsNamespace') . ".Admin.initProductDropdown(" . ($productId != '' ? $productId : '0') . ", " . ($manufacturerId != '' ? $manufacturerId : '0') . ");
        "
    ));
    $this->element('highlightRowAfterEdit', array(
        'rowIdPrefix' => '#product-'
    ));
    ?>
    
    <div class="filter-container">
        <?php
        if ($manufacturerId != '') {
            echo $this->Form->input('productId', array(
                'type' => 'select',
                'label' => '',
                'empty' => 'alle Artikel',
                'options' => array()
            ));
        }
        if (! $appAuth->isManufacturer()) {
            echo $this->Form->input('manufacturerId', array(
                'type' => 'select',
                'label' => '',
                'options' => $manufacturersForDropdown,
                'empty' => 'alle Hersteller',
                'selected' => isset($manufacturerId) ? $manufacturerId : ''
            ));
        }
        echo $this->Form->input('active', array(
            'type' => 'select',
            'label' => '',
            'options' => $this->MyHtml->getActiveStates(),
            'selected' => isset($active) ? $active : ''
        ));
        ?>
        <button id="filter" class="btn btn-success">
			<i class="fa fa-search"></i> Filtern
		</button>

		<div class="right">
            <?php
            // only show button if no manufacturer filter is applied
            if ($manufacturerId > 0) {
                echo '<div id="add-product-button-wrapper" class="add-button-wrapper">';
                echo $this->Html->link('<i class="fa fa-plus-square fa-lg"></i> Neuen Artikel erstellen', 'javascript:void(0);', array(
                    'class' => 'btn btn-default',
                    'escape' => false
                ));
                echo '</div>';
            }
            ?>
        </div>

	</div>

	<div id="help-container">
		<ul>
			<li>Auf dieser Seite werden deine <b>Artikel</b> verwaltet.
			</li>
			<li>
                Du kannst neue Artikel erstellen (Button rechts oben), mit einem Klick auf einen der Bearbeiten-Icons <?php echo $this->Html->image($this->Html->getFamFamFamPath('page_edit.png')); ?> kannst den entsprechenden (z.B. Kategorien, Anzahl, Preis...) ändern.
            </li>
			<li>Hinweis zum Ändern der Beschreibung: <b><i>Kurze</i> Beschreibung</b>
				steht im Shop immer neben dem Bild und ist in den Listen zu lesen. <b><i>Lange</i>
					Beschreibung</b> steht nur auf der Produkt-Detailseite (z.B. für
				das Anführen von Inhaltsstoffen geeignet).
			</li>
			<li>Du kannst deine Artikel <b>online bzw. offline setzen</b> (Icons <?php echo $this->Html->image($this->Html->getFamFamFamPath('accept.png')); ?> bzw. <?php echo $this->Html->image($this->Html->getFamFamFamPath('delete.png')); ?> ganz rechts).
            </li>
			<li><b>Varianten: </b>Mit dem <?php echo $this->Html->image($this->Html->getFamFamFamPath('add.png')); ?>-Icon kannst du eine neue Variante (z.B. 1kg, 2kg und 5kg) zu deinen Artikeln anlegen. Das <?php echo $this->Html->image($this->Html->getFamFamFamPath('star.png')); ?>-Icon sagt dir, welche Variante beim Bestellen standardmäßig ausgewählt ist, diese Standardvariante kannst du ändern.
                Varianten können auf "nicht bestellbar" gesetzt werden, in dem du die Anzahl auf 0 setzt.
            </li>
			<li>Falls eine gewünschte Variante noch nicht zur Verfügung steht,
				sag uns bitte Bescheid. Wir legen sie dann für dich an.</li>
			<li>Wenn du von einem Artikel nur eine <b>beschränkte Anzahl</b>
				liefern kannst, ändere die Anzahl bitte dementsprechend. Unser
				System vermindert bei jeder Bestellung den Lagerbestand und stoppt
				die Bestellmöglichkeit, wenn keine Artikel mehr verfügbar sind,
				automatisch. Somit bekommt jeder, der bestellt, seine Ware und es
				muss nichts storniert werden.
			</li>
			<li><b>Bilder hochladen:</b> Durch Anklicken des <?php echo $this->Html->image($this->Html->getFamFamFamPath('image_add.png')); ?>-Icons kannst ein Bild zu deinem Artikel hochladen. Wenn zu einem Artikel noch kein Bild hochgeladen wurde, ist das Icon rot hinterlegt. Bilder zu Varianten sind nicht möglich. 
            </li>
			<li>Du siehst, für welche Artikel wir Pfand einheben. Möchtest du den
				Pfand ändern, sag uns bitte Bescheid.</li>
			<li><b>Neue Artikel</b> können im Shop als "neu" gekennzeichnet werden und scheinen dann <?php echo Configure::read('app.db_config_FCS_DAYS_SHOW_PRODUCT_AS_NEW'); ?> Tage lang unter <a
				href="<?php echo Configure::read('app.cakeServerName'); ?>/neue-produkte"
				target="_blank">"Neue Produkte"</a> auf.</li>
		</ul>
	</div>
    
    <?php
    
    if (! empty($manufacturer) && $manufacturer['Manufacturer']['holiday'] == 1) {
        echo '<h2 class="info">Urlaubsmodus aktiviert. Im Shop werden keine Produkte angezeigt!</h2>';
    }
    
    echo '<table class="list no-clone-last-row">';
    
    echo '<tr class="sort">';
    echo '<th class="hide">' . $this->Paginator->sort('Product.id_product', 'ID') . '</th>';
    echo '<th>Variante</th>';
    echo '<th>Bild</th>';
    echo '<th>' . $this->Paginator->sort('ProductLang.name', 'Name') . '</th>';
    echo '<th>Kategorien</th>';
    echo '<th>' . $this->Paginator->sort('Stock.quantity', 'Anzahl') . '</th>';
    echo '<th>' . $this->Paginator->sort('ProductShop.price', 'Preis') . '</th>';
    echo '<th>' . $this->Paginator->sort('Tax.rate', 'Steuersatz') . '</th>';
    echo '<th class="center" style="width:69px;">' . $this->Paginator->sort('ProductShop.date_add', 'Neu?') . '</th>';
    echo '<th>Pfand</th>';
    echo '<th>' . $this->Paginator->sort('Product.active', 'Status') . '</th>';
    echo '<th style="width:29px;"></th>';
    echo '</tr>';
    
    $i = 0;
    foreach ($products as $product) {
        
        $i ++;
        
        echo '<tr id="product-' . $product['Product']['id_product'] . '" class="data ' . $product['Product']['rowClass'] . '">';
        
        echo '<td class="hide">';
        echo $product['Product']['id_product'];
        echo '</td>';
        
        echo '<td style="text-align: center;padding-left:16px;width:50px;">';
        if (! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('add.png')), array(
                'class' => 'add-product-attribute-button',
                'title' => 'Neue Variante für Artikel "' . $product['ProductLang']['name'] . '" erstellen'
            ), 'javascript:void(0);');
        }
        echo '</td>';
        
        echo '<td width="29px;" class="' . ((! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) && empty($product['ImageShop']) ? 'not-available' : '') . '">';
        if ((! empty($product['ProductAttributes']) || isset($product['ProductAttributes']))) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('image_add.png')), array(
                'class' => 'add-image-button',
                'title' => 'Neues Bild hochladen bzw. austauschen',
                'data-object-id' => $product['Product']['id_product']
            ), 'javascript:void(0);');
            $imageExists = ! empty($product['ImageShop']);
            echo $this->element('imageUploadForm', array(
                'id' => $product['Product']['id_product'],
                'action' => '/admin/tools/doTmpImageUpload/' . $product['Product']['id_product'],
                'imageExists' => $imageExists,
                'existingImageSrc' => $imageExists ? $this->Html->getProductImageSrc($product['ImageShop']['id_image'], $product['ImageShop']['ImageLang']['legend'], 'thickbox') : ''
            ));
        }
        echo '</td>';
        
        echo '<td>';
        
        if (! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'class' => 'product-name-edit-button',
                'title' => 'Name und Beschreibung ändern'
            ), 'javascript:void(0);');
        }
        
        if (! isset($product['ProductAttributes'])) {
            
            echo '<span style="float:left;margin-right: 5px;">';
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('delete.png')), array(
                'class' => 'delete-product-attribute-button',
                'title' => 'Variante für Artikel "' . $product['ProductLang']['name'] . '" löschen'
            ), 'javascript:void(0);');
            echo '</span>';
            
            echo '<span style="float:left;">';
            if ($product['ProductAttributeShop']['default_on'] == 1) {
                echo $this->Html->image($this->Html->getFamFamFamPath('star.png'), array(
                    'title' => 'Diese Variante ist die Standardvariante.'
                ));
            } else {
                echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('bullet_star.png')), array(
                    'class' => 'set-as-default-attribute-button',
                    'title' => 'Als neue Standard-Variante festlegen'
                ), 'javascript:void(0);');
            }
            echo '</span>';
        }
        
        echo '<span class="name-for-dialog">';
        echo $product['ProductLang']['name'];
        echo '</span>';
        
        // show unity only if article has no attributes and field "unity" is not empty
        if (empty($product['ProductAttributes'])) {
            if (isset($product['ProductShop']) && $product['ProductShop']['unity'] != '') {
                echo ': ';
                echo '<span class="unity-for-dialog">';
                echo $product['ProductShop']['unity'];
                echo '</span>';
            }
        }
        
        echo '<span class="description-short-for-dialog">';
        echo $product['ProductLang']['description_short'];
        echo '</span>';
        
        echo '<span class="description-for-dialog">';
        echo $product['ProductLang']['description'];
        echo '</span>';
        
        echo '</td>';
        
        echo '<td>';
        if (! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) {
            echo '<div class="categories-checkboxes" id="categories-checkboxes-' . $product['Product']['id_product'] . '">';
            echo $this->Form->hidden('Product.id_product', array(
                'value' => $product['Product']['id_product'],
                'id' => 'categories-product-id-' . $product['Product']['id_product'],
                'class' => 'product-id'
            ));
            echo $this->Form->input('Product.CategoryProducts', array(
                'multiple' => 'checkbox',
                'label' => $product['ProductLang']['name'],
                'options' => $categoriesForDropdown,
                'selected' => $product['selectedCategories'],
                'id' => 'cat-' . $product['Product']['id_product']
            ));
            echo '<div class="sc"></div>';
            echo '</div>';
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'class' => 'product-categories-edit-button',
                'title' => 'Kategorien ändern',
                'data-object-id' => $product['Product']['id_product']
            ), 'javascript:void(0);');
            echo '<span class="categories-for-dialog">' . join(', ', $product['Categories']['names']) . '</span>';
            if (! $product['Categories']['allProductsFound']) {
                echo ' - <b>Kategorie "Alle Produkte" fehlt!</b>';
            }
        }
        echo '</td>';
        
        echo '<td class="' . (empty($product['ProductAttributes']) && $product['StockAvailable']['quantity'] == 0 ? 'not-available' : '') . '">';
        
        if (empty($product['ProductAttributes'])) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'class' => 'product-quantity-edit-button',
                'title' => 'Anzahl ändern'
            ), 'javascript:void(0);');
            echo '<span class="quantity-for-dialog">';
            echo $this->Html->formatAsDecimal($product['StockAvailable']['quantity'], 0);
            echo '</span>';
        }
        
        echo '</td>';
        
        echo '<td class="' . (empty($product['ProductAttributes']) && $product['Product']['gross_price'] == 0 ? 'not-available' : '') . '">';
        echo '<div class="table-cell-wrapper price">';
        if (empty($product['ProductAttributes'])) {
            echo '<span class="price-for-dialog">';
            echo $this->Html->formatAsDecimal($product['Product']['gross_price']);
            echo '</span>';
        }
        if (empty($product['ProductAttributes'])) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'class' => 'product-price-edit-button',
                'title' => 'Preis ändern'
            ), 'javascript:void(0);');
        }
        echo '</div>';
        echo '</td>';
        
        echo '<td>';
        if (! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) {
            echo '<div class="tax-dropdown-wrapper" id="tax-dropdown-wrapper-' . $product['Product']['id_product'] . '">';
            echo $this->Form->hidden('Product.id_product', array(
                'value' => $product['Product']['id_product'],
                'id' => 'tax-product-id-' . $product['Product']['id_product'],
                'class' => 'product-id'
            ));
            echo $this->Form->input('Tax.id_tax', array(
                'type' => 'select',
                'label' => 'Steuersatz ändern für:<br />' . $product['ProductLang']['name'],
                'options' => $taxesForDropdown,
                'selected' => $product['Product']['id_tax'],
                'id' => 'tax-dropdown-' . $product['Product']['id_product']
            ));
            echo '</div>';
            $taxRate = $product['Tax']['rate'];
            echo '<span class="tax-for-dialog">' . ($taxRate != intval($taxRate) ? $this->Html->formatAsDecimal($taxRate, 1) : $this->Html->formatAsDecimal($taxRate, 0)) . '%' . '</span>';
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'class' => 'product-tax-edit-button',
                'title' => 'Steuer ändern',
                'data-object-id' => $product['Product']['id_product']
            ), 'javascript:void(0);');
        }
        echo '</td>';
        
        echo '<td>';
        if (! empty($product['ProductAttributes']) || isset($product['ProductAttributes'])) {
            if (! $product['Product']['is_new']) {
                echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('delete.png')) . ' Neu', array(
                    'class' => 'icon-with-text change-new-state change-new-state-active',
                    'id' => 'change-new-state-' . $product['Product']['id_product'],
                    'title' => 'Artikel die nächsten ' . Configure::read('app.db_config_FCS_DAYS_SHOW_PRODUCT_AS_NEW') . ' Tage als "neu" anzeigen?'
                ), 'javascript:void(0);');
            } else {
                echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('accept.png')) . ' Neu', array(
                    'class' => 'icon-with-text change-new-state change-new-state-inactive',
                    'id' => 'change-new-state-' . $product['Product']['id_product'],
                    'title' => 'Artikel nicht mehr als "neu" anzeigen?'
                ), 'javascript:void(0);');
            }
        }
        echo '</td>';
        
        echo '<td>';
        if (empty($product['ProductAttributes'])) {
            echo '<div class="table-cell-wrapper price">';
            if ($appAuth->isSuperadmin() || $appAuth->isAdmin() || Configure::read('app.isDepositPaymentCashless')) {
                echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                    'class' => 'product-deposit-edit-button',
                    'title' => 'Zum Ändern des Pfands anklicken'
                ), 'javascript:void(0);');
            }
            if ($product['Deposit'] > 0) {
                echo '<span class="deposit-for-dialog">';
                echo $this->Html->formatAsDecimal($product['Deposit']);
                echo '</span>';
            }
            echo '</div>';
        }
        echo '</td>';
        
        echo '<td style="text-align: center;padding-left:10px;width:42px;">';
        
        if ($product['Product']['active'] == 1) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('accept.png')), array(
                'class' => 'set-state-to-inactive change-active-state',
                'id' => 'change-active-state-' . $product['Product']['id_product'],
                'title' => 'Zum Deaktivieren anklicken'
            ), 'javascript:void(0);');
        }
        
        if ($product['Product']['active'] == '') {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('delete.png')), array(
                'class' => 'set-state-to-active change-active-state',
                'id' => 'change-active-state-' . $product['Product']['id_product'],
                'title' => 'Zum Aktivieren anklicken'
            ), 'javascript:void(0);');
        }
        
        echo '</td>';
        
        echo '<td>';
        if ($product['Product']['active'] && (! empty($product['ProductAttributes']) || isset($product['ProductAttributes']))) {
            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('arrow_right.png')), array(
                'title' => 'Artikel-Vorschau',
                'target' => '_blank'
            ), $url = $this->Slug->getProductDetail($product['Product']['id_product'], $product['ProductLang']['name']));
        }
        echo '</td>';
        
        echo '</tr>';
    }
    
    echo '<tr>';
    echo '<td colspan="12"><b>' . $i . '</b> Datensätze</td>';
    echo '</tr>';
    
    echo '</table>';
    ?>    
    
    <div class="sc"></div>
    
    <?php
    if ($manufacturerId > 0) {
        echo '<div class="bottom-button-container">';
        $profileLink = $this->Slug->getManufacturerEdit($manufacturerId);
        if ($appAuth->isManufacturer()) {
            $profileLink = $this->Slug->getManufacturerProfile();
        }
        echo '<a href="' . $profileLink . '" class="btn btn-default"><i class="fa fa-check-square-o"></i> Alle Aktivieren aktivieren / deaktivieren? Urlaubsmodus im Hersteller-Profil verwenden!</a>';
        echo '</div>';
        echo '<div class="sc"></div>';
    }
    ?>
    
</div>

<?php echo $this->Form->input('productAttributeId', array('type' => 'select', 'class' => 'hide', 'label' => '', 'options' => $attributesLangForDropdown)); ?>
