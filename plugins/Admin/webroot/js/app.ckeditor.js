/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under the GNU Affero General Public License version 3
 * For full copyright and license information, please see LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.6.0
 * @license       https://opensource.org/licenses/AGPL-3.0
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
foodcoopshop.AppCkeditor = {

    init: function (name, startupFocus) {

        this.destroyCkeditor(name);

        /*
        config.enterMode = CKEDITOR.ENTER_BR;
        config.startupOutlineBlocks = false;
        */
        
        startupFocus = startupFocus|| false;

        ClassicEditor
            .create(document.querySelector('textarea#' + name + '.ckeditor'), {
                format_tags: 'p',
                toolbar: {
                    items: [
                        'Bold', 'Italic',
                    ],
                },
                language: foodcoopshop.LocalizedJs.helper.defaultLocaleShort,
            })
            // .then(editor => {
                // console.log(editor);
            // })
            .catch(error => {
                console.error( error );
            });        

    },

    destroyCkeditor: function (name) {

    },

    initBig: function (name) {

        return;

        if (!CKEDITOR.env.isCompatible) {
            return false;
        }

        this.destroyCkeditor(name);

        CKEDITOR.timestamp = 'v4.20.1';
        $('textarea#' + name + '.ckeditor').ckeditor({
            customConfig: '/js/ckeditor/config-big.js'
        });

    },

    initSmallWithUpload: function (name) {

        if (!CKEDITOR.env.isCompatible) {
            return false;
        }

        this.destroyCkeditor(name);

        CKEDITOR.timestamp = 'v4.20.1';
        $('textarea#' + name + '.ckeditor').ckeditor({
            customConfig: '/js/ckeditor/config-small-with-upload.js'
        });

    }

};