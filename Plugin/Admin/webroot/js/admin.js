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
foodcoopshop.Admin = {

    init: function() {
        this.initFilter();
        this.improveTableLayout();
        foodcoopshop.Helper.initJqueryUiIcons();
        this.addPrintAndHelpIcon();
        foodcoopshop.Helper.showContent();
        foodcoopshop.Helper.initMenu();
        foodcoopshop.Helper.initLogoutButton();
        this.setMenuFixed();
        this.adaptContentMargin();
        foodcoopshop.Helper.initScrolltopButton();
    },
    
    initCancelSelectionButton : function() {
    	
    	var button = $('#cancelSelectedProductsButton');
    	foodcoopshop.Helper.disableButton(button);
    	
    	$('table.list').find('input.row-marker[type="checkbox"]').on('click', function() {
    		foodcoopshop.Admin.updateCancelSelectionButton(button);
    	});
    	
    	button.on('click', function() {
    		var orderDetailIds = [];
    		$('table.list').find('input.row-marker[type="checkbox"]:checked').each(function() {
    			var orderDetailId = $(this).closest('tr').find('td:nth-child(2)').html();
    			orderDetailIds.push(orderDetailId);
    		});
    		foodcoopshop.Admin.openBulkDeleteOrderDetailDialog(orderDetailIds);
    	});
    	
    },
    
    updateCancelSelectionButton : function(button) {
    	
		foodcoopshop.Helper.disableButton(button);
    	if ($('table.list').find('input.row-marker[type="checkbox"]:checked').length > 0) {
    		foodcoopshop.Helper.enableButton(button);
    	}
    	
    },

    initChangeOrderStateFromOrderDetails: function() {
    	
        $('body.order_details.index .change-order-state-button').on('click', function() {

            var orderIds = [];
            $('table.list td.orderId').each(function() {
                orderIds.push($(this).html());
            });
    
            var customerName = $('table.list tr:nth-child(3) td:nth-child(9)').html();
            var buttons = foodcoopshop.Admin.getOrderStateButtons(
                $.unique(orderIds),
                false,
                null,
                customerName
            );

        });
    },

    initFilter: function(callback) {

        $('button#filter').on('click', function() {
            foodcoopshop.Helper.disableButton($(this));
            foodcoopshop.Helper.addSpinnerToButton($(this), 'fa-search');
            foodcoopshop.Admin.afterFilterCallback();
        });
        $('input:text').keyup(function(e) {
            if (e.keyCode == 13) {
                foodcoopshop.Admin.afterFilterCallback();
            }
        });
        $('.filter-container select').selectpicker({
            liveSearch: true,
            showIcon: true
        });

        // multiple dropdowns (implemented for orderState) need to be selected manually
        // therefore data-val must be set!
        $('.filter-container select[multiple="multiple"]').each(function() {
            $(this).selectpicker('val', $(this).data('val').toString().split(','));
        });

    },

    afterFilterCallback: function() {

        var url = '';
        var sortParams = '';
        var splittedUrl = document.location.pathname.split('/');

        url = '/' + splittedUrl[1] + '/' + splittedUrl[2] + '/';
        if (splittedUrl[3] !== undefined) {
            url += splittedUrl[3];
        } else {
            url += 'index';
        }

        // add additional params only if its no key / value
        // do not add eg. "dateFrom:01.01.2014" but do add "member_fee" as 4th url part
        if (splittedUrl[4] !== undefined) {
            var s = splittedUrl[4].split(':');
            if (s.length == 1) {
                url += '/' + splittedUrl[4];
            }
        }

        //.input-block-level due to bootstrap select
        $('.filter-container').find('input:not(.form-control), select').each(function() {
            switch ($(this).prop('tagName').toLowerCase()) {
                case 'input':
                    switch ($(this).attr('type')) {
                        case 'text':
                            url += '/' + $(this).attr('id').replace(/\#/) + ':' + $(this).val();
                            break;
                        case 'checkbox':
                            url += '/' + $(this).attr('id').replace(/\#/) + ':';
                            url += $(this).is(':checked') ? 1 : 0;
                            break;
                    }
                    break;
                case 'select':
                    url += '/' + $(this).attr('id').replace(/\#/) + ':' + $(this).val();
                    break;
            }
        });

        //add cake pagination urls if exist
        for (var i in splittedUrl) {
            if (splittedUrl[i].match(/sort\:/)) {
                sortParams += '/' + splittedUrl[i];
            }
            if (splittedUrl[i].match(/direction\:/)) {
                sortParams += '/' + splittedUrl[i];
            }
        }
        if (sortParams != '') {
            url += sortParams;
        }

        // sometimes two slashes are in url (browser-dependent), clean it to avoid error and provide valid url
        url = url.replace(/\/\//, '/');
        document.location = url;

    },

    improveTableLayout: function() {

        // copy first row with sums
        var table = $('table.list');
        if (!table.hasClass('no-clone-last-row')) {
            var lastRow = table.find('tr:last-child').clone();
            table.find('tr:first-child').after(lastRow);
        }
        table.show();

        // change color of row on click of checkbox
        table.find('input.row-marker[type="checkbox"]').on('click', function() {
            if ($(this).parent().parent().hasClass('selected')) {
                $(this).parent().parent().removeClass('selected');
            } else {
                $(this).parent().parent().addClass('selected');
            }
        });

    },

    initCustomerCommentEditDialog: function(container) {

        var dialogId = 'customer-comment-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Mitglieder-Kommentar ändern">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<textarea class="ckeditor" name="dialogCustomerComment" id="dialogCustomerComment" />';
        dialogHtml += '</div>';
        dialogHtml += '<input type="hidden" name="dialogCustomerId" id="dialogCustomerId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 410,
            width: 350,
            modal: true,

            close: function() {
                $('#cke_dialogCustomerComment').val('');
                $('#dialogCustomerId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogCustomerId').val() == '') return false;

                    $('#customer-comment-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/customers/editComment/', {
                            customerId: $('#dialogCustomerId').val(),
                            customerComment: CKEDITOR.instances['dialogCustomerComment'].getData()
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                console.log(data);
                            }
                        }
                    );

                }

            }
        });

        $('.customer-comment-edit-button').on('click', function() {
            foodcoopshop.Helper.initCkeditor('dialogCustomerComment');
            CKEDITOR.instances['dialogCustomerComment'].setData($(this).data('title-for-overlay')); // attr title is deleted after toolbar init
            $('#' + dialogId + ' #dialogCustomerId').val($(this).closest('tr').find('td:nth-child(1)').html());
            dialog.dialog('open');
        });

    },

    initOrderEditDialog: function(container) {

        var dialogId = 'order-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Bestellung rückdatieren">';
        dialogHtml += '<form>';
        dialogHtml += '<p style="margin-top: 10px;">Bestellung rückdatieren auf</p>';
        dialogHtml += '<div class="date-dropdown-placeholder"></div>';
        dialogHtml += '<input type="hidden" name="orderId" id="orderId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 280,
            width: 350,
            modal: true,

            close: function() {
                $('#' + dialogId + ' .date-dropdown-placeholder').html('');
                $('#orderId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    var newDate = $('#' + dialogId + ' .date-dropdown-placeholder select').val();

                    if (newDate == '' || $('#orderId').val() == '') return false;

                    $('#order-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/orders/editDate/', {
                            orderId: $('#orderId').val(),
                            date: newDate
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                console.log(data);
                            }
                        }
                    );

                }

            }
        });

        $('.edit-button').on('click', function() {
            $('#' + dialogId + ' .date-dropdown-placeholder').html($(this).parent().parent().parent().parent().find('td.date-icon div.last-n-days-dropdown').html());
            $('#' + dialogId + ' #orderId').val($(this).parent().parent().parent().parent().find('td:nth-child(1)').html());
            dialog.dialog('open');
        });

    },

    initProductDepositEditDialog: function(container) {

        var dialogId = 'product-deposit-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Pfand">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogDepositDeposit">Eingabe in €</label>';
        dialogHtml += '<input type="text" name="dialogDepositDeposit" id="dialogDepositDeposit" value="" />';
        dialogHtml += '<input type="hidden" name="dialogDepositProductId" id="dialogDepositProductId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,

            close: function() {
                $('#dialogDepositDeposit').val('');
                $('#dialogDepositProductId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogDepositDeposit').val() == '' || $('#dialogDepositProductId').val() == '') return false;

                    $('#product-deposit-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/products/editDeposit/', {
                            productId: $('#dialogDepositProductId').val(),
                            deposit: $('#dialogDepositDeposit').val(),
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#product-deposit-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.product-deposit-edit-button').on('click', function() {
            var row = $(this).parent().parent().parent().parent().parent();
            $('#' + dialogId + ' #dialogDepositDeposit').val(row.find('td:nth-child(10) span.deposit-for-dialog').html());
            $('#' + dialogId + ' #dialogDepositProductId').val(row.find('td:nth-child(1)').html());
            $('#' + dialogId + ' label[for="dialogDepositDeposit"]').html(row.find('td:nth-child(2) span.name-for-dialog').html());
            dialog.dialog('open');
        });

    },

    initProductPriceEditDialog: function(container) {

        var dialogId = 'product-price-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Preis">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogPricePrice">Eingabe in €</label>';
        dialogHtml += '<input type="text" name="dialogPricePrice" id="dialogPricePrice" value="" />';
        dialogHtml += '<input type="hidden" name="dialogPriceProductId" id="dialogPriceProductId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,

            close: function() {
                $('#dialogPricePrice').val('');
                $('#dialogPriceProductId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogPricePrice').val() == '' || $('#dialogPriceProductId').val() == '') return false;

                    $('#product-price-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/products/editPrice/', {
                            productId: $('#dialogPriceProductId').val(),
                            price: $('#dialogPricePrice').val(),
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#product-price-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.product-price-edit-button').on('click', function() {
            var row = $(this).parent().parent().parent().parent().parent();
            $('#' + dialogId + ' #dialogPricePrice').val(row.find('td:nth-child(7) span.price-for-dialog').html());
            $('#' + dialogId + ' #dialogPriceProductId').val(row.find('td:nth-child(1)').html());
            $('#' + dialogId + ' label[for="dialogPricePrice"]').html(row.find('td:nth-child(2) span.name-for-dialog').html());
            dialog.dialog('open');
        });

    },

    initProductNameEditDialog: function(container) {

        var dialogId = 'product-name-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Name und Beschreibung ändern">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogName">Name</label><br />';
        dialogHtml += '<input type="text" name="dialogName" id="dialogName" value="" /><span class="overlay-info article-description-rename-info">Wichtig: Bitte keine Artikel in andere Artikel umbenennen, sondern neue Artikel erstellen!</span><br />';
        dialogHtml += '<label id="labelUnity" for="dialogUnity">Einheit <span style="font-weight:normal">(z.B. 1 kg, 0,5 l)</span></label><br />';
        dialogHtml += '<input type="text" name="dialogUnity" id="dialogUnity" value="" /><br />';
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<label for="dialogDescriptionShort">Kurze Beschreibung</label><br />';
        dialogHtml += '<textarea class="ckeditor" name="dialogDescriptionShort" id="dialogDescriptionShort" />';
        dialogHtml += '</div>';
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<label for="dialogDescription">Lange Beschreibung</label>';
        dialogHtml += '<textarea class="ckeditor" name="dialogDescription" id="dialogDescription" />';
        dialogHtml += '</div>';
        dialogHtml += '<input type="hidden" name="dialogProductId" id="dialogProductId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        foodcoopshop.Helper.initCkeditor('dialogDescription');
        foodcoopshop.Helper.initCkeditor('dialogDescriptionShort');

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 570,
            width: 695,
            modal: true,

            close: function() {
                $('#dialogName').val('');
                $('#dialogUnity').val('');
                $('#cke_dialogDescriptionShort').val('');
                $('#cke_dialogDescription').val('');
                $('#dialogProductId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogName').val() == '' || $('#dialogProductId').val() == '') return false;

                    $('#product-name-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/products/editName/', {
                            productId: $('#dialogProductId').val(),
                            name: $('#dialogName').val(),
                            unity: $('#dialogUnity').val(),
                            descriptionShort: CKEDITOR.instances['dialogDescriptionShort'].getData(),
                            description: CKEDITOR.instances['dialogDescription'].getData()
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                console.log(data);
                            }
                        }
                    );

                }

            }
        });

        $('.product-name-edit-button').on('click', function() {

            var nameCell = $(this).parent().parent().parent().parent().find('td:nth-child(4)');
            $('#' + dialogId + ' #dialogName').val(nameCell.find('span.name-for-dialog').html());
            $('#' + dialogId + ' #dialogUnity').val(nameCell.find('span.unity-for-dialog').html());
            CKEDITOR.instances['dialogDescriptionShort'].setData(nameCell.find('span.description-short-for-dialog').html());
            CKEDITOR.instances['dialogDescription'].setData(nameCell.find('span.description-for-dialog').html());
            $('#' + dialogId + ' #dialogProductId').val($(this).parent().parent().parent().parent().find('td:nth-child(1)').html());

            // hide unity field if article has attributes
            var unitySelector = $('#' + dialogId + ' #labelUnity, #' + dialogId + ' #dialogUnity');
            if ($(this).parent().parent().parent().parent().next().hasClass('sub-row')) {
                unitySelector.hide();
            } else {
                unitySelector.show();
            }

            dialog.dialog('open');
        });

    },

    initHighlightedRowId: function(rowId) {
        $.scrollTo(rowId, 1000, {
            offset: {
                top: -100
            }
        });
        $(rowId).css('background', 'orange');
        $(rowId).css('color', 'white');
        $(rowId).one('mouseover', function() {
            $(this).removeAttr('style');
        });
    },

    initProductQuantityEditDialog: function(container) {

        var dialogId = 'product-quantity-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Anzahl ändern">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogQuantityQuantity"></label>';
        dialogHtml += '<input type="text" name="dialogQuantityQuantity" id="dialogQuantityQuantity" value="" />';
        dialogHtml += '<input type="hidden" name="dialogQuantityProductId" id="dialogQuantityProductId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,

            close: function() {
                $('#dialogQuantityQuantity').val('');
                $('#dialogQuantityProductId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogQuantityQuantity').val() == '' || $('#dialogQuantityProductId').val() == '') return false;

                    $('#product-quantity-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/products/editQuantity/', {
                            productId: $('#dialogQuantityProductId').val(),
                            quantity: $('#dialogQuantityQuantity').val(),
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#product-quantity-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.product-quantity-edit-button').on('click', function() {
            $('#' + dialogId + ' #dialogQuantityQuantity').val($(this).parent().parent().parent().parent().find('td:nth-child(6) span.quantity-for-dialog').html().replace(/\./, ''));
            $('#' + dialogId + ' #dialogQuantityProductId').val($(this).parent().parent().parent().parent().find('td:nth-child(1)').html());
            $('#' + dialogId + ' label[for="dialogQuantityQuantity"]').html($(this).parent().parent().parent().parent().find('td:nth-child(3) span.name-for-dialog').html());
            dialog.dialog('open');
        });

    },

    initChangeOrderStateFromOrders: function() {
        $('body.orders.index .change-order-state-button').on('click', function() {
            var dataRow = $(this).parent().parent().parent().parent();
            var buttons = foodcoopshop.Admin.getOrderStateButtons(
                [dataRow.find('td:nth-child(1)').html()],
                true,
                dataRow.find('td:nth-child(5)').html(), //totalSum
                dataRow.find('td:nth-child(2)').html()
            );
        });
    },

    getOrderStateButtons: function(orderIds, showCancelOrderButton, totalSum, customerName) {

        var buttons = {};

        if ($.inArray('cash', foodcoopshop.Helper.paymentMethods) != -1 &&
            foodcoopshop.Admin.visibleOrderStates.hasOwnProperty('2')) {

            buttons['cash'] = {
                text: foodcoopshop.Admin.visibleOrderStates[2],
                class: 'left-button',
                click: function() {
                    $('.ui-dialog .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/orders/changeOrderState/', {
                            orderIds: orderIds,
                            orderState: 2
                        }, {
                            onOk: function(data) {
                                document.location.href = data.redirectUrl;
                            },
                            onError: function(data) {
                                document.location.reload();
                            }
                        }
                    );
                }
            };


        }

        if ($.inArray('cashless', foodcoopshop.Helper.paymentMethods) != -1 &&
            foodcoopshop.Admin.visibleOrderStates.hasOwnProperty('1')) {

            buttons['cashless'] = {
                text: foodcoopshop.Admin.visibleOrderStates[1],
                class: 'left-button',
                click: function() {
                    $('.ui-dialog .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/orders/changeOrderState/', {
                            orderIds: orderIds,
                            orderState: 1
                        }, {
                            onOk: function(data) {
                                document.location.href = data.redirectUrl;
                            },
                            onError: function(data) {
                                document.location.reload();
                            }
                        }
                    );
                }
            };

        };

        buttons['abbrechen'] = function() {
            $(this).dialog('close');
        };

        if (showCancelOrderButton) {
            buttons['storniert'] = function() {

                $('.ui-dialog .ajax-loader').show();
                $('.ui-dialog button').attr('disabled', 'disabled');

                if (totalSum != '€&nbsp;0,00') {
                    $('.ui-dialog .ajax-loader').hide();
                    alert('Bevor du die Bestellung stornieren kannst, storniere bitte alle bestellten Artikel.');
                    $('.ui-dialog button').attr('disabled', false);
                    return;
                }

                foodcoopshop.Helper.ajaxCall(
                    '/admin/orders/changeOrderState/', {
                        orderIds: orderIds,
                        orderState: 6
                    }, {
                        onOk: function(data) {
                            document.location.href = data.redirectUrl;
                        },
                        onError: function(data) {
                            document.location.reload();
                        }
                    }
                );

            };
        }

        buttons['offen'] = function() {

            $('.ui-dialog .ajax-loader').show();
            $('.ui-dialog button').attr('disabled', 'disabled');

            foodcoopshop.Helper.ajaxCall(
                '/admin/orders/changeOrderState/', {
                    orderIds: orderIds,
                    orderState: 3
                }, {
                    onOk: function(data) {
                        document.location.href = data.redirectUrl;
                    },
                    onError: function(data) {
                        document.location.reload();
                    }
                }
            );

        };

        $('<div></div>').appendTo('body')
            .html(foodcoopshop.Admin.additionalOrderStatusChangeInfo + '<p>Willst du den Bestellstatus der Bestellung von <b>' + customerName + '</b> wirklich ändern?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
            .dialog({
                modal: true,
                title: 'Bestellstatus ändern?',
                autoOpen: true,
                width: 620,
                resizable: false,
                buttons: buttons,
                close: function(event, ui) {
                    $(this).remove();
                }
            });

    },

    openBulkDeleteOrderDetailDialog : function(orderDetailIds) {
         
    	var infoText = '<p>Du hast <b>' + orderDetailIds.length + '</b> Artikel zum Stornieren ausgewählt:</p>';
         
         infoText += '<ul>';
         for (var i in orderDetailIds) {
             var dataRow = $('#delete-order-detail-' + orderDetailIds[i]).parent().parent().parent().parent();
             infoText += '<li>- ' + dataRow.find('td:nth-child(4) a').html() + ' / ' + dataRow.find('td:nth-child(9)').html() + '</li>';
    	 }
         infoText += '</ul>';

         var dialogTitle = 'Ausgewählte Artikel wirklich stornieren?';
         var textareaLabel = 'Warum werden die Artikel storniert (Pflichtfeld)?';
         foodcoopshop.Admin.openDeleteOrderDetailDialog(orderDetailIds, infoText, textareaLabel, dialogTitle);    	
    },
    
    openDeleteOrderDetailDialog : function(orderDetailIds, infoText, textareaLabel, dialogTitle) {
        
    	$('#cke_dialogCancellationReason').val('');
        
        var dialogHtml = infoText;
        if (!foodcoopshop.Helper.isManufacturer) {
            dialogHtml += '<p class="overlay-info">Bitte nur stornieren, wenn es mit dem Hersteller abgesprochen ist!</p>';
        }
        
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<label for="dialogCancellationReason">' + textareaLabel +'</label>';
        dialogHtml += '<textarea class="ckeditor" name="dialogCancellationReason" id="dialogCancellationReason" />';
        dialogHtml += '</div>';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';

        $('<div></div>').appendTo('body')
            .html(dialogHtml)
            .dialog({
                modal: true,
                title: dialogTitle,
                autoOpen: true,
                width: 400,
                open: function() {
                    foodcoopshop.Helper.initCkeditor('dialogCancellationReason');
                },
                resizable: false,
                buttons: {
                    'Abbrechen': function() {
                        $(this).dialog('close');
                    },
                    'Ja, stornieren!': function() {

                        var ckeditorData = CKEDITOR.instances['dialogCancellationReason'].getData().trim();
                        if (ckeditorData == '') {
                            alert('Bitte an, warum du den Artikel stornierst.');
                            return;
                        }

                        $('.ui-dialog .ajax-loader').show();
                        $('.ui-dialog button').attr('disabled', 'disabled');
                        foodcoopshop.Helper.ajaxCall(
                            '/admin/order_details/delete', {
                                orderDetailIds: orderDetailIds,
                                cancellationReason: ckeditorData
                            }, {
                                onOk: function(data) {
                                    document.location.reload();
                                },
                                onError: function(data) {
                                    document.location.reload();
                                }
                            }
                        );
                    }
                },
                close: function(event, ui) {
                    foodcoopshop.Helper.destroyCkeditor('dialogCancellationReason');
                }
            });        
    },
    
    initDeleteOrderDetail: function() {

        $('.delete-order-detail').on('click', function() {

            var orderDetailId = $(this).attr('id').split('-');
            orderDetailId = orderDetailId[orderDetailId.length - 1];
            
            var dataRow = $('#delete-order-detail-' + orderDetailId).parent().parent().parent().parent();
            var infoText = '<p>Möchtest du den Artikel <b>' + dataRow.find('td:nth-child(4) a').html() + '</b>';

            if (!foodcoopshop.Helper.isManufacturer) {
            	infoText += ' vom Hersteller <b>' + dataRow.find('td:nth-child(5) a').html() + '</b>';
            }
            infoText += ' wirklich stornieren?</p>';
            
            var dialogTitle = 'Bestellten Artikel wirklich stornieren?';
            var textareaLabel = 'Warum wird der Artikel storniert (Pflichtfeld)?';
            
            foodcoopshop.Admin.openDeleteOrderDetailDialog([orderDetailId], infoText, textareaLabel, dialogTitle);

        });
    },

    initChangeNewState: function() {

        $('.change-new-state').on('click', function() {

            var productId = $(this).attr('id').split('-');
            productId = productId[productId.length - 1];

            var newState = 1;
            var newStateText = 'als "neu" anzeigen';
            if ($(this).hasClass('change-new-state-inactive')) {
                newState = 0;
                var newStateText = 'nicht mehr als "neu" anzeigen';
            }

            var dataRow = $('#change-new-state-' + productId).parent().parent().parent().parent();
            $('<div></div>').appendTo('body')
                .html('<p>Möchtest du den Artikel <b>' + dataRow.find('td:nth-child(4) span.name-for-dialog').html() + '</b> wirklich im Shop ' + newStateText + '?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                .dialog({
                    modal: true,
                    title: 'Artikel ' + newStateText + '?',
                    autoOpen: true,
                    width: 400,
                    resizable: false,
                    buttons: {
                        'Nein': function() {
                            $(this).dialog('close');
                        },
                        'Ja': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            document.location.href = '/admin/products/changeNewStatus/' + productId + '/' + newState;
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });
    },

    initDeleteProductAttribute: function(container) {

        $(container).find('.delete-product-attribute-button').on('click', function() {

            var splittedProductId = $(this).parent().parent().parent().parent().parent().attr('id').replace(/product-/, '').split('-');
            var productId = splittedProductId[0];
            var productAttributeId = splittedProductId[1];

            var dataRow = $(this).parent().parent().parent().parent().parent();
            var htmlCode = '<p>Die Variante <b>' + dataRow.find('td:nth-child(4) span.name-for-dialog').html() + '</b> wirklich löschen?</p>';
            htmlCode += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';

            $('<div></div>').appendTo('body')
                .html(htmlCode)
                .dialog({
                    modal: true,
                    title: 'Variante löschen',
                    autoOpen: true,
                    width: 450,
                    resizable: false,
                    buttons: {
                        'Abbrechen': function() {
                            $(this).dialog('close');
                        },
                        'Löschen': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            document.location.href = '/admin/products/deleteProductAttribute/' + productId + '/' + productAttributeId;
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });

    },

    initAddProductAttribute: function(container) {

        $(container).find('.add-product-attribute-button').on('click', function() {

            var productId = $(this).parent().parent().parent().parent().attr('id').replace(/product-/, '').split('-');
            productId = productId[productId.length - 1];

            var dataRow = $(this).parent().parent().parent().parent();
            var htmlCode = '<p>Bitte wähle die neue Variante für den Artikel <b>' + dataRow.find('td:nth-child(4) span.name-for-dialog').html() + '</b> aus.</p>';
            var productAttributesDropdown = $('#productAttributeId').clone(true);
            productAttributesDropdown.show();
            productAttributesDropdown.removeClass('hide');
            htmlCode += '<select class="product-attributes-dropdown">' + productAttributesDropdown.html() + '</select>';

            htmlCode += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';

            $('<div></div>').appendTo('body')
                .html(htmlCode)
                .dialog({
                    modal: true,
                    title: 'Neue Variante für Artikel erstellen',
                    autoOpen: true,
                    width: 450,
                    resizable: false,
                    buttons: {
                        'Abbrechen': function() {
                            $(this).dialog('close');
                        },
                        'Speichern': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            document.location.href = '/admin/products/addProductAttribute/' + productId + '/' + $('.product-attributes-dropdown').val()
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });

    },

    initSetDefaultAttribute: function(container) {
        $(container).find('.set-as-default-attribute-button').on('click', function() {
            var productIdString = $(this).parent().parent().parent().parent().parent().attr('id').replace(/product-/, '').split('-');
            var productId = productIdString[0];
            var attributeId = productIdString[1];

            var dataRow = $(this).parent().parent().parent().parent().parent();
            var attributeName = dataRow.find('td:nth-child(4) span.name-for-dialog').html();
            var htmlCode = '<p>Neue Standard-Variante wirklich auf <b>' + attributeName + '</b> ändern?</p>';
            htmlCode += '<p>Die Standard-Variante ist die Variante, die beim Bestellen vorausgewählt ist.</p>';
            htmlCode += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';

            $('<div></div>').appendTo('body')
                .html(htmlCode)
                .dialog({
                    modal: true,
                    title: 'Neue Standard-Variante für Artikel ändern',
                    autoOpen: true,
                    width: 450,
                    resizable: false,
                    buttons: {
                        'Abbrechen': function() {
                            $(this).dialog('close');
                        },
                        'Speichern': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            document.location.href = '/admin/products/changeDefaultAttributeId/' + productId + '/' + attributeId;
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });

        });
    },

    initAddProduct: function(container) {

        $(container).find('#add-product-button-wrapper a').on('click', function() {

            $('<div></div>').appendTo('body')
                .html('<p>Möchtest du wirklich einen neuen Artikel erstellen?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                .dialog({
                    modal: true,
                    title: 'Neuen Artikel erstellen',
                    autoOpen: true,
                    width: 400,
                    resizable: false,
                    buttons: {
                        'Nein': function() {
                            $(this).dialog('close');
                        },
                        'Ja': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            document.location.href = '/admin/products/add/' + $(container).find('#manufacturerId').val();
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });

    },

    initManualOrderListSend: function(container, weekday) {

        $(container).on('click', function() {
            if ($.inArray(foodcoopshop.Helper.cakeServerName, ['http://www.foodcoopshop.dev', 'http://demo.foodcoopshop.com']) == -1 &&
                $.inArray(weekday, foodcoopshop.Admin.weekdaysBetweenOrderSendAndDelivery) == -1) {
                alert('Diese Funktion steht heute nicht zur Verfügung.');
                return;
            }

            var manufacturerId = $(this).parent().parent().parent().parent().attr('id').replace(/manufacturer-/, '');
            var dataRow = $('#manufacturer-' + manufacturerId);

            $('<div></div>').appendTo('body')
                .html('<p>Willst du wirklich eine aktuelle Bestellliste an <b>' + dataRow.find('td:nth-child(3) b').html() + '</b> versenden?</p><p>Bestellzeitraum: <b>' + $('#dateFrom').val() + ' bis ' + $('#dateTo').val() + ' </b></p><p>Eine bereits bestehende Bestellliste wird überschrieben!</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                .dialog({
                    modal: true,
                    title: 'Bestellliste manuell versenden?',
                    autoOpen: true,
                    width: 400,
                    resizable: false,
                    buttons: {
                        'Nein': function() {
                            $(this).dialog('close');
                        },
                        'Ja': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            var url = '/admin/manufacturers/sendOrderList/' + manufacturerId + '/' + $('#dateFrom').val() + '/' + $('#dateTo').val();
                            document.location.href = url;
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });

    },

    initEmailToAllButton: function() {
        $('button.email-to-all').on('click', function() {
            var emailColumn = $(this).data('column');
            var emails = [];
            $('table.list tr.data').each(function() {
                emails.push($(this).find('td:nth-child(' + emailColumn + ') span.email').html());
            });
            emails = $.unique(emails);

            $('<div></div>').appendTo('body')
                .html('<p>' + emails.join(',') + '</p>')
                .dialog({
                    modal: true,
                    title: 'E-Mail-Adressen',
                    autoOpen: true,
                    width: 800,
                    resizable: false,
                    buttons: {
                        'OK': function() {
                            $(this).dialog('close');
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });

        });
    },

    initForm: function(id, objectClass) {

        $('.filter-container .right a.submit').on('click', function() {
            foodcoopshop.Helper.disableButton($(this));
            foodcoopshop.Helper.addSpinnerToButton($(this), 'fa-check');
            $(this).closest('#content').find('form.fcs-form').submit();
        });

        $('.filter-container .right a.cancel').on('click', function() {

            foodcoopshop.Helper.disableButton($(this));
            foodcoopshop.Helper.addSpinnerToButton($(this), 'fa-remove');

            foodcoopshop.Helper.ajaxCall(
                '/admin/tools/ajaxCancelFormPage/', {
                    id: id,
                    objectClass: objectClass,
                    referer: $('#referer').val()
                }, {
                    onOk: function(data) {
                        document.location.href = data.referer
                    },
                    onError: function(data) {
                        alert(data.message);
                    }
                }
            );
        });

        // copy save and cancel button below form
        $('form.fcs-form').after('<div class="form-buttons"></div>');
        $('#content .form-buttons').append($('.filter-container .right > a').clone(true)); // true clones events

        // submit form on enter in text fields
        $('form.fcs-form input[type=text], form.fcs-form input[type=number], form.fcs-form input[type=password], form.fcs-form input[type="tel"]').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
                $('.filter-container .right a.submit').trigger('click');
            }
        });

        $('form.fcs-form select').selectpicker({
            liveSearch: true,
            showIcon: true
        });

    },

    initProductTaxEditDialog: function(container) {

        var button = $(container).find('.product-tax-edit-button');

        $(button).on('click', function() {

            var objectId = $(this).data('objectId');
            var formHtml = $('#tax-dropdown-wrapper-' + objectId);
            $.featherlight(
                foodcoopshop.AppFeatherlight.initLightboxForForms(
                    foodcoopshop.Admin.editTaxFormSave,
                    null,
                    foodcoopshop.AppFeatherlight.closeAndReloadLightbox,
                    formHtml
                )
            );
        });

    },

    editTaxFormSave: function() {

        var productId = $('.featherlight-content .product-id').val();

        foodcoopshop.Helper.ajaxCall(
            '/admin/products/editTax/', {
                productId: productId,
                taxId: $('.featherlight-content #tax-dropdown-' + productId).val()
            }, {
                onOk: function(data) {
                    document.location.reload();
                },
                onError: function(data) {
                    document.location.reload();
                    alert(data.msg);
                }
            }
        );

    },

    editCategoriesFormSave: function() {

        var selectedCategories = [];
        $('.featherlight-content .categories-checkboxes input:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        foodcoopshop.Helper.ajaxCall(
            '/admin/products/editCategories/', {
                productId: $('.featherlight-content .product-id').val(),
                selectedCategories: selectedCategories
            }, {
                onOk: function(data) {
                    document.location.reload();
                },
                onError: function(data) {
                    document.location.reload();
                    alert(data.msg);
                }
            }
        );

    },

    initProductCategoriesEditDialog: function(container) {

        var button = $(container).find('.product-categories-edit-button');

        $(button).on('click', function() {

            var objectId = $(this).data('objectId');
            var formHtml = $('#categories-checkboxes-' + objectId);

            $.featherlight(
                foodcoopshop.AppFeatherlight.initLightboxForForms(
                    foodcoopshop.Admin.editCategoriesFormSave,
                    null,
                    foodcoopshop.AppFeatherlight.closeAndReloadLightbox,
                    formHtml
                )
            );

            // fix for strange behavior: click on label resets form
            $(container).find('label').on('click', function(e) {
                e.preventDefault();
                $(this).closest('.checkbox').find('input').trigger('click');
            });

        });

    },

    initNextAndPreviousDayLinks: function() {
        $('.btn-previous-day').on('click', function() {
            var datepicker = $(this).next();
            var date = datepicker.datepicker('getDate');
            date.setDate(date.getDate() - 1)
            datepicker.datepicker('setDate', date);
        });
        $('.btn-next-day').on('click', function() {
            var datepicker = $(this).prev();
            var date = datepicker.datepicker('getDate');
            date.setDate(date.getDate() + 1)
            datepicker.datepicker('setDate', date);
        });
    },

    initOrderDetailProductPriceEditDialog: function(container) {

        $('#cke_dialogPriceEditReason').val('');

        var dialogId = 'order-detail-product-price-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Preis korrigieren">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogOrderDetailProductPrice"></label><br />';
        dialogHtml += '<input type="text" name="dialogOrderDetailProductPricePrice" id="dialogOrderDetailProductPricePrice" value="" />';
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<label for="dialogEditPriceReason">Warum wird der Preis korrigiert (Pflichtfeld)?</label>';
        dialogHtml += '<textarea class="ckeditor" name="dialogEditPriceReason" id="dialogEditPriceReason" />';
        dialogHtml += '</div>';
        dialogHtml += '<input type="hidden" name="dialogOrderDetailProductPriceOrderDetailId" id="dialogOrderDetailProductPriceOrderDetailId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            width: 400,
            modal: true,
            close: function() {
                $('#dialogOrderDetailProductPricePrice').val('');
                $('#dialogOrderDetailProductPriceOrderDetailId').val('');
                foodcoopshop.Helper.destroyCkeditor('dialogEditPriceReason');
            },
            open: function() {
                foodcoopshop.Helper.initCkeditor('dialogEditPriceReason');
            },
            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogOrderDetailProductPricePrice').val() == '' || $('#dialogOrderDetailProductPriceOrderDetailId').val() == '') return false;

                    var ckeditorData = CKEDITOR.instances['dialogEditPriceReason'].getData().trim();
                    if (ckeditorData == '') {
                        alert('Bitte an, warum der Preis geändert wird.');
                        return;
                    }

                    $('#order-detail-product-price-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/order_details/editProductPrice/', {
                            orderDetailId: $('#dialogOrderDetailProductPriceOrderDetailId').val(),
                            productPrice: $('#dialogOrderDetailProductPricePrice').val(),
                            editPriceReason: ckeditorData
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#order-detail-product-price-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.order-detail-product-price-edit-button').on('click', function() {
            var row = $(this).parent().parent().parent().parent().parent();
            $('#' + dialogId + ' #dialogOrderDetailProductPricePrice').val(row.find('td:nth-child(6) span.product-price-for-dialog').html());
            $('#' + dialogId + ' #dialogOrderDetailProductPriceOrderDetailId').val(row.find('td:nth-child(2)').html());
            $('#' + dialogId + ' label[for="dialogOrderDetailProductPrice"]').html(row.find('td:nth-child(4) a.name-for-dialog').html() + ' <span style="font-weight:normal;">(von ' + row.find('td:nth-child(9)').html() + ')');
            dialog.dialog('open');
        });

    },

    setVisibleOrderStates: function(visibleOrderStates) {
        this.visibleOrderStates = $.parseJSON(visibleOrderStates);
    },

    setWeekdaysBetweenOrderSendAndDelivery: function(weekdaysBetweenOrderSendAndDelivery) {
        this.weekdaysBetweenOrderSendAndDelivery = $.parseJSON(weekdaysBetweenOrderSendAndDelivery);
    },

    setAdditionalOrderStatusChangeInfo: function(additionalOrderStatusChangeInfo) {
        this.additionalOrderStatusChangeInfo = additionalOrderStatusChangeInfo;
    },

    setUseManufacturerCompensationPercentage: function(useManufacturerCompensationPercentage) {
        this.useManufacturerCompensationPercentage = useManufacturerCompensationPercentage;
    },

    setDefaultCompensationPercentage: function(defaultCompensationPercentage) {
        this.defaultCompensationPercentage = defaultCompensationPercentage;
    },

    setDefaultSendOrderList: function(defaultSendOrderList) {
        this.defaultSendOrderList = defaultSendOrderList;
    },

    setDefaultSendInvoice: function(defaultSendInvoice) {
        this.defaultSendInvoice = defaultSendInvoice;
    },

    setDefaultTaxId: function(defaultTaxId) {
        this.defaultTaxId = defaultTaxId;
    },

    setDefaultBulkOrdersAllowed: function(defaultBulkOrdersAllowed) {
        this.defaultBulkOrdersAllowed = defaultBulkOrdersAllowed;
    },

    initEditManufacturerOptions: function(container) {

        $(container).on('click', function() {

            var manufacturerId = $(this).closest('tr').attr('id').replace(/manufacturer-/, '');

            var dataRow = $('#manufacturer-' + manufacturerId);
            var dialogId = 'manufacturer-options-edit-form';
            var dialogHtml = '<div id="' + dialogId + '">';
            dialogHtml += '<h3>' + dataRow.find('td:nth-child(4) b').html() + '</h3>';

            if (foodcoopshop.Admin.useManufacturerCompensationPercentage) {
                dialogHtml += '<label for="dialogManufacturerOptionsCompensationPercentage">Aufwandsentschädigung in % (nur ganze Zahlen)</label>';
                dialogHtml += '<input type="text" name="dialogManufacturerOptionsCompensationPercentage" id="dialogManufacturerOptionsCompensationPercentage" value="" />';
            }

            dialogHtml += '<label for="dialogManufacturerOptionsSendOrderList">Bestell-Listen wöchentlich als PDF versenden? (0 oder 1)</label>';
            dialogHtml += '<input type="text" name="dialogManufacturerOptionsSendOrderList" id="dialogManufacturerOptionsSendOrderList" value="" />';

            dialogHtml += '<label for="dialogManufacturerOptionsSendInvoice">Rechnungen monatlich als PDF versenden? (0 oder 1)</label>';
            dialogHtml += '<input type="text" name="dialogManufacturerOptionsSendInvoice" id="dialogManufacturerOptionsSendInvoice" value="" />';

            dialogHtml += '<label style="width: 300px;" for="dialogManufacturerOptionsDefaultTaxId">Voreingestellter Steuersatz für neue Artikel</label>';
            var tax = dataRow.find('div.tax-wrapper select');
            dialogHtml += '<select name="dialogManufacturerOptionsDefaultTaxId" id="dialogManufacturerOptionsDefaultTaxId">' + tax.html() + '</select>';

            dialogHtml += '<label class="full-width" for="dialogManufacturerOptionsSendOrderListCc">CC-Empfänger für Bestell-Listen-Versand - mehrere getrennt mit ;</label>';
            dialogHtml += '<input class="full-width" type="text" name="dialogManufacturerOptionsSendOrderListCc" id="dialogManufacturerOptionsSendOrderListCc" value="" />';

            dialogHtml += '<label for="dialogManufacturerOptionsBulkOrdersAllowed">Sammelbestellungen möglich? (0 oder 1)</label>';
            dialogHtml += '<input type="text" name="dialogManufacturerOptionsBulkOrdersAllowed" id="dialogManufacturerOptionsBulkOrdersAllowed" value="" />';

            dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
            dialogHtml += '</div>';

            $('#' + dialogId).remove();

            var manufacturerOptions = $(this).data('title-for-overlay');

            $('<div></div>').appendTo('body')
                .html(dialogHtml)
                .dialog({
                    modal: true,
                    title: 'Hersteller-Einstellungen ändern für',
                    autoOpen: true,
                    open: function() {

                        if (foodcoopshop.Admin.useManufacturerCompensationPercentage !== undefined) {

                            if (manufacturerOptions === undefined || manufacturerOptions.compensationPercentage === undefined) {
                                var compensationPercentage = foodcoopshop.Admin.defaultCompensationPercentage;
                            } else {
                                var compensationPercentage = manufacturerOptions.compensationPercentage;
                            }
                            $('#dialogManufacturerOptionsCompensationPercentage').val(compensationPercentage);
                        }

                        if (manufacturerOptions === undefined || manufacturerOptions.sendInvoice === undefined) {
                            var sendInvoice = foodcoopshop.Admin.defaultSendInvoice;
                        } else {
                            var sendInvoice = manufacturerOptions.sendInvoice;
                        }
                        $('#dialogManufacturerOptionsSendInvoice').val(sendInvoice);

                        if (manufacturerOptions === undefined || manufacturerOptions.sendOrderList === undefined) {
                            var sendOrderList = foodcoopshop.Admin.defaultSendOrderList;
                        } else {
                            var sendOrderList = manufacturerOptions.sendOrderList;
                        }
                        $('#dialogManufacturerOptionsSendOrderList').val(sendOrderList);

                        if (manufacturerOptions === undefined || manufacturerOptions.defaultTaxId === undefined) {
                            var defaultTaxId = foodcoopshop.Admin.defaultTaxId;
                        } else {
                            var defaultTaxId = manufacturerOptions.defaultTaxId;
                        }
                        $('#dialogManufacturerOptionsDefaultTaxId').val(defaultTaxId);

                        if (manufacturerOptions !== undefined && manufacturerOptions.sendOrderListCc) {
                            $('#dialogManufacturerOptionsSendOrderListCc').val(manufacturerOptions.sendOrderListCc);
                        }

                        if (manufacturerOptions === undefined || manufacturerOptions.bulkOrdersAllowed === undefined) {
                            var bulkOrdersAllowed = foodcoopshop.Admin.defaultBulkOrdersAllowed;
                        } else {
                            var bulkOrdersAllowed = manufacturerOptions.bulkOrdersAllowed;
                        }
                        $('#dialogManufacturerOptionsBulkOrdersAllowed').val(bulkOrdersAllowed);

                    },
                    width: 550,
                    height: 450,
                    resizable: false,
                    buttons: {
                        'Abbrechen': function() {
                            $(this).dialog('close');
                        },
                        'Speichern': function() {
                            $('.ui-dialog .ajax-loader').show();
                            $('.ui-dialog button').attr('disabled', 'disabled');
                            foodcoopshop.Helper.ajaxCall(
                                '/admin/manufacturers/editOptions', {
                                    manufacturerId: manufacturerId,
                                    compensationPercentage: foodcoopshop.Admin.useManufacturerCompensationPercentage ? $('#dialogManufacturerOptionsCompensationPercentage').val() : null,
                                    sendInvoice: $('#dialogManufacturerOptionsSendInvoice').val(),
                                    sendOrderList: $('#dialogManufacturerOptionsSendOrderList').val(),
                                    defaultTaxId: $('#dialogManufacturerOptionsDefaultTaxId').val(),
                                    sendOrderListCc: $('#dialogManufacturerOptionsSendOrderListCc').val(),
                                    bulkOrdersAllowed: $('#dialogManufacturerOptionsBulkOrdersAllowed').val()
                                }, {
                                    onOk: function(data) {
                                        document.location.reload();
                                    },
                                    onError: function(data) {
                                        $('.ui-dialog .ajax-loader').hide();
                                        $('.ui-dialog button').attr('disabled', false);
                                        alert(data.msg);
                                    }
                                }
                            );
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });

        });
    },

    initOrderDetailProductQuantityEditDialog: function(container) {

        $('#cke_dialogEditQuantityReason').val('');

        var dialogId = 'order-detail-product-quantity-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Anzahl vermindern">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogOrderDetailProductQuantity"></label><br />';
        dialogHtml += '<select name="dialogOrderDetailProductQuantityQuantity" id="dialogOrderDetailProductQuantityQuantity" /></select>';
        dialogHtml += '<div class="textarea-wrapper">';
        dialogHtml += '<label for="dialogEditQuantityReason">Warum wird Anzahl korrigiert (Pflichtfeld)?</label>';
        dialogHtml += '<textarea class="ckeditor" name="dialogEditQuantityReason" id="dialogEditQuantityReason" />';
        dialogHtml += '</div>';
        dialogHtml += '<input type="hidden" name="dialogOrderDetailProductQuantityOrderDetailId" id="dialogOrderDetailProductQuantityOrderDetailId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            height: 600,
            width: 450,
            modal: true,

            close: function() {
                $('#dialogOrderDetailProductQuantityQuantity').val('');
                $('#dialogOrderDetailProductQuantityOrderDetailId').val('');
                foodcoopshop.Helper.destroyCkeditor('dialogEditQuantityReason');
            },
            open: function() {
                foodcoopshop.Helper.initCkeditor('dialogEditQuantityReason');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogOrderDetailProductQuantityQuantity').val() == '' || $('#dialogOrderDetailProductQuantityOrderDetailId').val() == '') return false;

                    var ckeditorData = CKEDITOR.instances['dialogEditQuantityReason'].getData().trim();
                    if (ckeditorData == '') {
                        alert('Bitte an, warum die Anzahl geändert wird.');
                        return;
                    }


                    $('#order-detail-product-quantity-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/order_details/editProductQuantity/', {
                            orderDetailId: $('#dialogOrderDetailProductQuantityOrderDetailId').val(),
                            productQuantity: $('#dialogOrderDetailProductQuantityQuantity').val(),
                            editQuantityReason: ckeditorData
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#order-detail-product-quantity-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.order-detail-product-quantity-edit-button').on('click', function() {
            var currentQuantity = $(this).closest('tr').find('td:nth-child(3) span.product-quantity-for-dialog').html();
            var select = $('#' + dialogId + ' #dialogOrderDetailProductQuantityQuantity');
            select.find('option').remove();
            for (var i = 1; i < currentQuantity; i++) {
                select.append($('<option>', {
                    value: i,
                    text: i
                }));
            }
            $('#' + dialogId + ' #dialogOrderDetailProductQuantityOrderDetailId').val($(this).closest('tr').find('td:nth-child(2)').html());
            $('#' + dialogId + ' label[for="dialogOrderDetailProductQuantity"]').html('<span style="font-weight:normal"><br />Die Anzahl kann nur vermindert werden.<br />Um die Anzahl zu erhöhen, bitte den Artikel nachbuchen.<br /><br /></span>' + $(this).closest('tr').find('td:nth-child(4) a.name-for-dialog').html() + ' <span style="font-weight:normal;">(von ' + $(this).closest('tr').find('td:nth-child(9)').html() + ')<br />Neue Anzahl:');
            dialog.dialog('open');
        });

    },

    initCustomerGroupEditDialog: function(container) {

        var dialogId = 'customer-group-edit-form';
        var dialogHtml = '<div id="' + dialogId + '" class="dialog" title="Gruppe ändern">';
        dialogHtml += '<form onkeypress="return event.keyCode != 13;">';
        dialogHtml += '<label for="dialogCustomerGroupEditText" id="dialogCustomerGroupEditText"></label><br />';
        dialogHtml += '<select name="dialogCustomerGroupEditGroup" id="dialogCustomerGroupEditGroup" /></select>';
        dialogHtml += '<input type="hidden" name="dialogCustomerGroupEditCustomerId" id="dialogCustomerGroupEditCustomerId" value="" />';
        dialogHtml += '<img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';
        dialogHtml += '</form>';
        dialogHtml += '</div>';
        $(container).append(dialogHtml);

        var dialog = $('#' + dialogId).dialog({

            autoOpen: false,
            width: 400,
            modal: true,

            close: function() {
                $('#dialogCustomerGroupEditGroupId').val('');
                $('#dialogCustomerGroupEditCustomerId').val('');
            },

            buttons: {

                'Abbrechen': function() {
                    dialog.dialog('close');
                },

                'Speichern': function() {

                    if ($('#dialogCustomerGroupEditGroupId').val() == '' || $('#dialogCustomerGroupEditCustomerId').val() == '') return false;

                    $('#customer-group-edit-form .ajax-loader').show();
                    $('.ui-dialog button').attr('disabled', 'disabled');

                    foodcoopshop.Helper.ajaxCall(
                        '/admin/customers/ajaxEditGroup/', {
                            customerId: $('#dialogCustomerGroupEditCustomerId').val(),
                            groupId: $('#dialogCustomerGroupEditGroup').val(),
                        }, {
                            onOk: function(data) {
                                document.location.reload();
                            },
                            onError: function(data) {
                                dialog.dialog('close');
                                $('#customer-group-edit-form .ajax-loader').hide();
                                alert(data.msg);
                            }
                        }
                    );

                }

            }
        });

        $('.customer-group-edit-button').on('click', function() {
            var selectedGroupId = $(this).closest('tr').find('td:nth-child(3) span.group-for-dialog').html();
            var select = $('#' + dialogId + ' #dialogCustomerGroupEditGroup');
            select.find('option').remove();
            select.append($('#selectGroupId').html());
            select.val(selectedGroupId);
            $('#' + dialogId + ' #dialogCustomerGroupEditText').html('Gruppe ändern für ' + $(this).closest('tr').find('td:nth-child(2) a').text() + '<p style="font-weight: normal;"><br />Er/Sie muss sich nach der Änderung neu einloggen.</p>');
            $('#' + dialogId + ' #dialogCustomerGroupEditCustomerId').val($(this).closest('tr').find('td:nth-child(1)').html());
            dialog.dialog('open');
        });

    },

    initAddOrder: function(button, weekday) {
        // auf dev und demo seite immer zulassen (zum testen)
        if ($.inArray(foodcoopshop.Helper.cakeServerName, [
                'http://www.foodcoopshop.dev',
                'http://demo.foodcoopshop.com'
            ]) == -1 &&
            $.inArray(weekday, foodcoopshop.Admin.weekdaysBetweenOrderSendAndDelivery) == -1) {
            $(button).on('click', function(event) {
                alert('Diese Funktion steht heute nicht zur Verfügung.');
                $.featherlight.close();
            });
        } else {

            $(button).on('click', function() {

                var configuration = foodcoopshop.AppFeatherlight.initLightbox({
                    iframe: foodcoopshop.Helper.cakeServerName + '/admin/orders/iframeStartPage',
                    iframeWidth: $(window).width() - 50,
                    iframeMaxWidth: '100%',
                    iframeHeight: $(window).height() - 100,
                    afterClose: function() {
                        foodcoopshop.Helper.ajaxCall(
                            '/carts/ajaxDeleteShopOrderCustomer', {}, {
                                onOk: function(data) {},
                                onError: function(data) {}
                            });
                    },
                    afterContent: function() {

                        var header = $('<div class="message-container"><span class="start"><b>Sofort-Bestellung </b> tätigen für: </span> Nach dem Abschließen der Bestellung wird sie automatisch rückdatiert.</div>');
                        $('.featherlight-close').after(header);

                        // only clone dropdown once
                        if ($('.message-container span.start select').length == 0) {
                            var customersDropdown = $(
                                    '#add-order-button-wrapper select')
                                .clone(true);
                            customersDropdown.attr('id',
                                'customersDropdown');
                            customersDropdown
                                .change(function() {
                                    var newSrc = foodcoopshop.Helper.cakeServerName +
                                        '/admin/orders/initShopOrder/' +
                                        $(this).val();
                                    $('iframe.featherlight-inner')
                                        .attr('src', newSrc);
                                    $.featherlight.showLoader();
                                });
                            $('iframe.featherlight-inner')
                                .load(
                                    function() {
                                        // called after each url change in iframe!
                                        $.featherlight.hideLoader();
                                        var currentUrl = $(this).get(0).contentWindow.document.URL;
                                        if (currentUrl.match(/warenkorb\/abgeschlossen/)) {
                                            $.featherlight.showLoader();
                                            document.location.href = '/admin/orders/correctShopOrder?url=' + encodeURIComponent(currentUrl);
                                        }
                                    });
                            customersDropdown.show();
                            customersDropdown.removeClass('hide');
                            customersDropdown.appendTo('.message-container span.start');
                        }
                    }
                });

                $.featherlight(configuration);

            });
        }
    },

    addPrintAndHelpIcon: function() {

        var html = '<div class="icons">';
        html += '<a class="btn btn-default" title="Drucken" href="javascript:window.print();"><i class="fa fa-print fa-lg"></i></a>';
        html += '<a class="btn btn-default help" title="Hilfe" class="help" href="javascript:void(0);"><i class="fa fa-question fa-lg"></i></a>';
        html += '</div>';

        $('.filter-container div.right').append(html);

        $('.filter-container div.right a.help').on('click', function() {
            $('#help-container').stop(true).animate({
                height: 'toggle'
            }, 0);
            $.scrollTo('body', 1000, {
                offset: {
                    top: 0
                }
            });
        });

    },

    setMenuFixed: function() {
        $(window).scroll(function() {
            $('#menu').css('left', -$(window).scrollLeft());
            $('.filter-container').css('margin-left', -$(window).scrollLeft());
        });
        $('#menu').show();
    },

    adaptContentMargin: function() {
        var marginTop = $('.filter-container').height() + 10;
        $('#content').css('margin-top', marginTop);
        $('#menu').css('min-height', marginTop + $('#content').height() + 4);
    },

    initCustomerChangeActiveState: function() {

        $('.change-active-state').on('click', function() {

            var customerId = $(this).attr('id').split('-');
            customerId = customerId[customerId.length - 1];

            var newState = 1;
            var newStateText = 'aktivieren';
            if ($(this).hasClass('set-state-to-inactive')) {
                newState = 0;
                var newStateText = 'deaktivieren';
            }

            var dataRow = $('#change-active-state-' + customerId).parent().parent().parent().parent();

            var buttons = {};
            buttons['no'] = {
                text: 'Nein',
                click: function() {
                    $(this).dialog('close');
                }
            }

            if (newState == 1) {
                buttons['yes'] = {
                    text: 'Ja (Info-Mail wird versendet)',
                    click: function() {
                        $('.ui-dialog .ajax-loader').show();
                        $('.ui-dialog button').attr('disabled',
                            'disabled');
                        document.location.href = '/admin/customers/changeStatus/' +
                            customerId +
                            '/' +
                            newState +
                            '/1';
                    }
                };
            } else {
                buttons['yes'] = {
                    text: 'Ja',
                    click: function() {
                        $('.ui-dialog .ajax-loader').show();
                        $('.ui-dialog button').attr('disabled',
                            'disabled');
                        document.location.href = '/admin/customers/changeStatus/' +
                            customerId +
                            '/' +
                            newState +
                            '/0';
                    }
                };
            }

            $('<div></div>')
                .appendTo('body')
                .html(
                    '<p>Möchtest du das Mitglied <b>' +
                    dataRow
                    .find(
                        'td:nth-child(2) a')
                    .html() +
                    '</b> wirklich ' +
                    newStateText +
                    '?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                .dialog({
                    modal: true,
                    title: 'Mitglied ' +
                        newStateText + '?',
                    autoOpen: true,
                    width: 400,
                    resizable: false,
                    buttons: buttons,
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });
    },

    initProductChangeActiveState: function() {

        $('.change-active-state').on('click', function() {

            var productId = $(this).attr('id').split('-');
            productId = productId[productId.length - 1];

            var newState = 1;
            var newStateText = 'aktivieren';
            if ($(this).hasClass('set-state-to-inactive')) {
                newState = 0;
                var newStateText = 'deaktivieren';
            }

            var dataRow = $('#change-active-state-' + productId).parent().parent().parent().parent();
            $('<div></div>')
                .appendTo('body')
                .html('<p>Möchtest du den Artikel <b>' +
                    dataRow
                    .find(
                        'td:nth-child(4) span.name-for-dialog')
                    .html() +
                    '</b> wirklich ' +
                    newStateText +
                    '?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                .dialog({
                    modal: true,
                    title: 'Artikel ' +
                        newStateText + '?',
                    autoOpen: true,
                    width: 400,
                    resizable: false,
                    buttons: {
                        'Nein': function() {
                            $(this).dialog('close');
                        },
                        'Ja': function() {
                            $('.ui-dialog .ajax-loader')
                                .show();
                            $('.ui-dialog button')
                                .attr(
                                    'disabled',
                                    'disabled');
                            document.location.href = '/admin/products/changeStatus/' +
                                productId +
                                '/' +
                                newState;
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
        });
    },

    /**
     * @deprecated
     */
    initCloseOrdersButton: function(container) {

        $('#closeOrdersButton')
            .on(
                'click',
                function() {

                    var orderIdsContainer = $('table.list td.order-id');
                    orderIds = [];
                    orderIdsContainer.each(function() {
                        orderIds.push($(this).html());
                    });

                    $('<div></div>')
                        .appendTo('body')
                        .html(
                            '<p>Möchtest du wirklich alle angezeigten Bestellungen <b>abschließen</b>?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                        .dialog({
                            modal: true,
                            title: 'Alle Bestellungen abschließen',
                            autoOpen: true,
                            width: 400,
                            resizable: false,
                            buttons: {
                                'Nein': function() {
                                    $(this).dialog('close');
                                },
                                'Ja': function() {

                                    $('.ui-dialog .ajax-loader')
                                        .show();
                                    $('.ui-dialog button')
                                        .attr(
                                            'disabled',
                                            'disabled');

                                    if ($
                                        .inArray(
                                            'cash',
                                            foodcoopshop.Helper.paymentMethods) != -1) {
                                        var orderState = 2;
                                    }
                                    if ($
                                        .inArray(
                                            'cashless',
                                            foodcoopshop.Helper.paymentMethods) != -1) {
                                        var orderState = 1;
                                    }

                                    foodcoopshop.Helper
                                        .ajaxCall(
                                            '/admin/orders/changeOrderStateToClosed/', {
                                                orderIds: orderIds,
                                                orderState: orderState
                                            }, {
                                                onOk: function(
                                                    data) {
                                                    document.location
                                                        .reload();
                                                },
                                                onError: function(
                                                    data) {
                                                    document.location
                                                        .reload();
                                                }
                                            });

                                }

                            },
                            close: function(event, ui) {
                                $(this).remove();
                            }
                        });
                });

    },

    initGenerateOrdersAsPdf: function() {

        $('button.generate-orders-as-pdf')
            .on(
                'click',
                function() {

                    var orderIdsContainer = $('table.list td.order-id');
                    orderIds = [];
                    orderIdsContainer.each(function() {
                        orderIds.push($(this).html());
                    });

                    $('<div></div>')
                        .appendTo('body')
                        .html(
                            '<p>Möchtest du wirklich alle Bestellungen als PDF generieren?</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />')
                        .dialog({
                            modal: true,
                            title: 'Bestellungen als PDF generieren',
                            autoOpen: true,
                            width: 400,
                            resizable: false,
                            buttons: {
                                'Nein': function() {
                                    $(this).dialog('close');
                                },
                                'Ja': function() {
                                    $('.ui-dialog .ajax-loader')
                                        .show();
                                    $('.ui-dialog button')
                                        .attr(
                                            'disabled',
                                            'disabled');
                                    window
                                        .open('/admin/orders/ordersAsPdf/orderIds:' +
                                            orderIds
                                            .join(',') +
                                            '.pdf');
                                    $(this).dialog('close');
                                }

                            },
                            close: function(event, ui) {
                                $(this).remove();
                            }
                        });
                });

    },

    initAddPaymentInList: function(button) {

        $(button).each(function() {

            var buttonClass = button.replace(/\./, '');
            var buttonClass = buttonClass.replace(/-button/, '');
            var formHtml = $('#' + buttonClass + '-form-' + $(this).data('objectId'));

            $(this).featherlight(
                foodcoopshop.AppFeatherlight.initLightboxForForms(
                    foodcoopshop.Admin.addPaymentFormSave,
                    null,
                    foodcoopshop.AppFeatherlight.closeLightbox,
                    formHtml
                )
            );

        });

    },

    initAddPayment: function(button) {

        $(button).featherlight(
            foodcoopshop.AppFeatherlight.initLightboxForForms(
                foodcoopshop.Admin.addPaymentFormSave,
                null,
                foodcoopshop.AppFeatherlight.closeLightbox,
                $('.add-payment-form')
            )
        );

    },

    addPaymentFormSave: function() {

        var amount = $('.featherlight-content #CakePaymentAmount').val();
        if (isNaN(parseFloat(amount.replace(/,/, '.')))) {
            alert('Bitte gib eine Zahl ein.');
            foodcoopshop.AppFeatherlight.enableSaveButton();
            return;
        }

        var type = $('.featherlight-content #CakePaymentType').val();
        var customerIdDomElement = $('.featherlight-content #CakePaymentCustomerId');
        var manufacturerIdDomElement = $('.featherlight-content #CakePaymentManufacturerId');

        var text = '';
        if ($('.featherlight-content #CakePaymentText').length > 0) {
            text = $('.featherlight-content #CakePaymentText').val().trim();
        }

        // radio buttons only if deposit is added to manufacurers
        if ($('.featherlight-content input[type="radio"]').length > 0) {
        	var selectedRadioButton = $('.featherlight-content input[type="radio"]:checked');
        	
        	// check if radio buttons are in deposit form or product form
        	if ($('.featherlight-content .add-payment-form').hasClass('add-payment-deposit-form')) {
        		var message = 'Bitte wähle die Art der Pfand-Rücknahme aus.';
            	var isDepositForm = true;
        	} else {
        		var message = 'Bitte wähle aus, ob es sich um eine Aufladung oder ein Rückzahlung handelt.';
        		isDepositForm = false;
        	}
        	
        	if (selectedRadioButton.length == 0) {
                alert(message);
                foodcoopshop.AppFeatherlight.enableSaveButton();
                return;
        	}
        	
        	var selectedRadioButtonValue = $('.featherlight-content input[type="radio"]:checked').val();
        	if (isDepositForm) {
            	text = selectedRadioButtonValue;
        	} else {
        		type = selectedRadioButtonValue;
        	}

        }
        
        var months_range = [];
        if ($('.featherlight-content input[type="checkbox"]').length > 0) {
            $('.featherlight-content input[type="checkbox"]:checked').each(
                function() {
                    months_range.push($(this).val());
                }
            );
            if (months_range.length == 0) {
                alert('Bitte wähle zumindest ein Monat aus.');
                foodcoopshop.AppFeatherlight.enableSaveButton();
                return;
            }
        }

        foodcoopshop.Helper.ajaxCall('/admin/payments/add/', {
            amount: amount,
            type: type,
            text: text,
            months_range: months_range,
            customerId: customerIdDomElement.length > 0 ? customerIdDomElement.val() : 0,
            manufacturerId: manufacturerIdDomElement.length > 0 ? manufacturerIdDomElement.val() : 0
        }, {
            onOk: function(data) {
                document.location.reload();
            },
            onError: function(data) {
                alert(data.msg);
                document.location.reload();
            }
        });

    },

    initDeletePayment: function() {

        $('.delete-payment-button')
            .on(
                'click',
                function() {

                    var dataRow = $(this).parent().parent().parent()
                        .parent();

                    var dialogHtml = '<p>Willst du deine Zahlung wirklich löschen?<br />';
                    dialogHtml += 'Datum: <b>' +
                        dataRow.find('td:nth-child(2)').html() +
                        '</b> <br />';
                    dialogHtml += 'Betrag: <b>' +
                        dataRow.find('td:nth-child(4)').html()
                    if (dataRow.find('td:nth-child(6)').length > 0) {
                        dialogHtml += dataRow.find('td:nth-child(6)')
                            .html();
                    }
                    dialogHtml += '</b>';
                    dialogHtml += '</p><img class="ajax-loader" src="/img/ajax-loader.gif" height="32" width="32" />';

                    $('<div></div>')
                        .appendTo('body')
                        .html(dialogHtml)
                        .dialog({
                            modal: true,
                            title: 'Zahlung löschen?',
                            autoOpen: true,
                            width: 400,
                            resizable: false,
                            buttons: {

                                'Abbrechen': function() {
                                    $(this).dialog('close');
                                },

                                'Ja': function() {

                                    $('.ui-dialog .ajax-loader')
                                        .show();
                                    $('.ui-dialog button')
                                        .attr(
                                            'disabled',
                                            'disabled');

                                    var paymentId = dataRow
                                        .find(
                                            'td:nth-child(1)')
                                        .html();

                                    foodcoopshop.Helper
                                        .ajaxCall(
                                            '/admin/payments/changeState/', {
                                                paymentId: paymentId
                                            }, {
                                                onOk: function(
                                                    data) {
                                                    document.location
                                                        .reload();
                                                },
                                                onError: function(
                                                    data) {
                                                    alert(data.msg);
//                                                    document.location
//                                                        .reload();
                                                }
                                            });

                                }

                            },
                            close: function(event, ui) {
                                $(this).remove();
                            }
                        });
                });

    },

    initProductDropdown: function(selectedProductId, manufacturerId) {

        var manufacturerId = manufacturerId || 0;
        var productDropdown = $('select#productId').closest('.bootstrap-select').find('.dropdown-toggle');

        // one removes itself after one execution
        productDropdown.one('click', function() {

            $(this).parent().find('span.filter-option').append('<i class="fa fa-spinner fa-spin"></i>');

            foodcoopshop.Helper
                .ajaxCall('/admin/products/ajaxGetProductsForDropdown/' +
                    selectedProductId + '/' + manufacturerId, {}, {
                        onOk: function(data) {
                            var select = $('select#productId');
                            select.append(data.products);
                            select.attr('disabled', false);
                            select.selectpicker('refresh');
                            select.find('i.fa-spinner').remove();
                        },
                        onError: function(data) {
                            console.log(data.msg);
                        }
                    });

        });

        if (selectedProductId > 0) {
            // one click for opening and loading the products
            productDropdown.trigger('click');
            // and another click for closing the dropdown
            productDropdown.trigger('click');
        }

    }

}