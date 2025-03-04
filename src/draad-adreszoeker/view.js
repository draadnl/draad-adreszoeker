/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

`use strict`;

(function () {
    var $ = $ || jQuery.noConflict();

    class Draad_Adreszoeker {

        constructor( node ) {

            if ( !node ) {
                throw new Error("No valid element passed to Draad_Adreszoeker class.");
            }

            this.node = node;
            this.streetInputNode = this.node.querySelector( '.draad-adreszoeker__filter.--street input' );
            this.streetSuggestionsNode = this.node.querySelector( '.draad-adreszoeker__filter.--street .draad-adreszoeker__suggestions');
            this.numberInputNode = this.node.querySelector( '.draad-adreszoeker__filter.--number input' );
            this.filterFormNode = this.node.querySelector( '.draad-adreszoeker__filters > form' );

            if ( !this.streetInputNode || !this.numberInputNode || !this.filterFormNode ) {
                throw new Error("Draad_Adreszoeker is missing elements in the DOM.");
            }

            this.eventBinder();

        }

        eventBinder() {
            this.streetInputNode.addEventListener( 'input', this.debounce( this.streetHandler, 500 ) );
            this.filterFormNode.addEventListener( 'submit', this.handleSubmition.bind( this ) );
        }

        streetHandler = () => {            
            const streetInputValue = this.streetInputNode.value.trim();

            const notices = this.streetInputNode.parentNode.querySelectorAll( '.draad-adreszoeker__notice' );
            if ( notices.length >= 1 ) {
                for ( const notice of notices ) {
                    notice.remove();
                }
            }

            if ( streetInputValue.length < 2 ) {
                // Clear suggestions
                this.streetSuggestionsNode.innerHTML = '';

                this.numberInputNode.disabled = true;

                // Add notice
                const noticeElement = document.createElement('span');
                noticeElement.className = 'draad-adreszoeker__notice';
                noticeElement.textContent = 'Straatnaam moet minimaal 2 karakters bevatten.';
                this.streetInputNode.parentNode.appendChild(noticeElement);
                // $( this.streetInputNode.parentNode ).append('<span class="draad-adreszoeker__notice">Straatnaam moet minimaal 2 karakters bevatten.</span>');
            } else {
                this.numberInputNode.disabled = false;
            }

            const formData = new FormData(this.filterFormNode);
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'draad_adreszoeker_get_streets',
                    street: formData.get( 'street' ),
                },
                success: function (response) {
                    console.log( response );

                    this.streetSuggestionsNode.innerHTML = '';

                    // TODO: Add street suggestions
                },
                error: function (xhr, status, error) {
                    console.log('Er gaat iets fout');
                }
            });

        }

        async handleSubmition( event ) {
            event.preventDefault();

            const formData = new FormData(this.filterFormNode);
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: formData.get( 'action' ),
                    street: formData.get( 'street' ),
                    number: formData.get( 'number' ),
                },
                success: function (response) {
                    console.log( response );

                    // TODO: Get Results
                },
                error: function (xhr, status, error) {
                    console.log('Er gaat iets fout');
                }
            });
        }

        debounce( callback, wait ) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    callback.apply(this, args);
                }, wait);
            };
        }
    }

    document.addEventListener( 'DOMContentLoaded', function () {

        const nodes = document.querySelectorAll( '.draad-adreszoeker' );
        nodes?.forEach( node => new Draad_Adreszoeker( node ) );

    });
})();