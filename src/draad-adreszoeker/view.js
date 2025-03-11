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

    document.addEventListener( 'DOMContentLoaded', function () {

        const nodes = document.querySelectorAll( '.draad-adreszoeker' );
        nodes?.forEach( node => new Draad_Adreszoeker( node ) );

    });

    class Draad_Adreszoeker {

        constructor( node ) {

            if ( !node ) {
                throw new Error("No valid element passed to Draad_Adreszoeker class.");
            }

            this.node = node;
            this.streetInputNode = this.node.querySelector( '.draad-adreszoeker__filter.--street input' );
            this.streetSuggestionsNode = this.node.querySelector( '.draad-adreszoeker__filter.--street .draad-adreszoeker__suggestions');
            this.numberInputNode = this.node.querySelector( '.draad-adreszoeker__filter.--number input' );
            this.filterFormNode = this.node.querySelector( 'form' );
            this.outputNode = this.node.querySelector( '.draad-adreszoeker__output' );

            if ( !this.streetInputNode || !this.numberInputNode || !this.filterFormNode || !this.outputNode ) {
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

            // Remove old notices
            const notices = this.streetInputNode.parentNode.querySelectorAll( '.draad-adreszoeker__notice' );
            if ( notices.length >= 1 ) {
                for ( const notice of notices ) {
                    notice.remove();
                }
            }

            if ( streetInputValue.length < 2 ) {
                // Clear suggestions
                this.streetSuggestionsNode.innerHTML = '';

                this.streetInputNode.setAttribute( 'aria-invalid', 'true' );
                this.numberInputNode.disabled = true;

                // Add notice
                const noticeElement = document.createElement('span');
                noticeElement.className = 'draad-adreszoeker__notice';
                noticeElement.textContent = 'Straatnaam moet minimaal 2 karakters bevatten.';
                this.streetInputNode.parentNode.appendChild(noticeElement);
            } else {
                this.streetInputNode.removeAttribute( 'aria-invalid' );
                this.numberInputNode.disabled = false;
            }

            const formData = new FormData(this.filterFormNode);
            $.ajax({
                url: formData.get( 'admin-ajax' ),
                type: 'POST',
                data: {
                    action: 'draad_adreszoeker_get_streets',
                    street: formData.get( 'street' ),
                },
                success: (response) => {
                    const options = response.data;
                    // Clear suggestions
                    this.streetSuggestionsNode.innerHTML = '';

                    if ( !options || options.length <= 1 ) {
                        // If there are no results add an empty option making that clear.
                        const optionNode = document.createElement( 'option' );
                        optionNode.value = '';
                        optionNode.textContent = 'Geen resultaten gevonden';
                        this.streetSuggestionsNode.appendChild(optionNode);
                        return;
                    } else if ( typeof options === 'object' ) {              
                        options?.forEach( option => {
                            // Add an option for each street.
                            const optionNode = document.createElement( 'option' );
                            optionNode.value = option.street;
                            optionNode.textContent = option.street;
                            this.streetSuggestionsNode.appendChild(optionNode);
                        } );
                    }
                },
                error: function (xhr, status, error) {
                    throw new Error( error );
                }
            });

        }

        async handleSubmition( event ) {
            event.preventDefault();

            const formData = new FormData(this.filterFormNode);
            $.ajax({
                url: formData.get( 'admin-ajax' ),
                type: 'POST',
                data: {
                    action: formData.get( 'action' ),
                    street: formData.get( 'street' ),
                    number: formData.get( 'housenumber' ),
                },
                success: (response) => {
                    this.outputNode.innerHTML = response.data;

                    this.addNotice( 'Het advies is geopend.' );

                    const closeButtonNode = this.outputNode.querySelector( '.draad-adreszoeker__close-advice' );
                    closeButtonNode?.addEventListener( 'click', event => {
                        this.outputNode.innerHTML = '';
                        this.streetInputNode.value = '';
                        this.numberInputNode.value = '';
                        this.addNotice( 'Het advies is gesloten' );
                    } );

                    this.outputNode.querySelectorAll(".draad-tabs__tablist")?.forEach((tablist) => new Draad_Tabs(tablist));

                    // initializeTabs();
                    // initializeToggles();
                    // scrollToResult();
                },
                error: function (xhr, status, error) {
                    throw new Error( error );
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

        addNotice( message ) {
            // Remove old notices
            const notices = this.node.querySelectorAll( ':scope > .draad-adreszoeker__notice' );
            if ( notices.length >= 1 ) {
                for ( const notice of notices ) {
                    notice.remove();
                }
            }

            // Make new announcement that there is a new advice showing.
            const noticeElement = document.createElement('span');
            noticeElement.classList.add('draad-adreszoeker__notice');
            noticeElement.classList.add('sr-only');
            noticeElement.setAttribute('aria-live', 'polite');
            noticeElement.textContent = message;
            this.node.appendChild(noticeElement);
        }
    }

    class Draad_Tabs {
        /**
         * Constructor
         *
         * @param {HTMLElement} groupNode The HTML element tat contains the tabs.
         */
        constructor(groupNode) {
            if (!groupNode) {
                throw new Error("Draad Tabs: No tablist node provided.");
            }

            this.tablistNode = groupNode;
            this.tabs = Array.from(this.tablistNode.querySelectorAll("[role=tab]"));
            this.tabPanels = this.tabs?.map((tab) =>
                document.getElementById(tab.getAttribute("aria-controls")),
            );
            this.firstTab = this.tabs[0] || null;
            this.lastTab = this.tabs[this.tabs.length - 1] || null;

            this.tabs?.forEach((tab) => {
                tab.setAttribute("aria-selected", "false");
                tab.addEventListener("keydown", this.onKeydown.bind(this));
                tab.addEventListener("click", this.onClick.bind(this));
            });

            this.setSelectedTab(this.firstTab);
        }

        /**
         * Set the selected tab.
         *
         * @param {HTMLElement} currentTab the current tab element.
         */
        setSelectedTab(currentTab) {
            this.tabs?.forEach((tab, i) => {
                const isSelected = tab === currentTab;
                tab.setAttribute("aria-selected", isSelected);
                this.tabPanels[i].hidden = !isSelected;
            });
        }

        /**
         * Move focus to a tab.
         *
         * @param {HTMLElement} currentTab The current tab element.
         */
        moveFocusToTab(currentTab) {
            currentTab.focus();
        }

        /**
         * Moves the focus to the adjacent tab based on teh spcified direction.
         *
         * @param {HTMLElement} currentTab The current tab element.
         * @param {number} direction -	The direction to move the focus.
         * 								-1: Move to the previous tab.
         * 								1: Move to the next tab.
         */
        moveFocusToAdjacentTab(currentTab, direction) {
            let index = this.tabs.indexOf(currentTab);
            index = (index + direction + this.tabs.length) % this.tabs.length;
            this.moveFocusToTab(this.tabs[index]);
        }

        /**
         * onKeyDown event handler for tabs.
         *
         * @param {object} event The keyboard event object.
         */
        onKeydown(event) {
            const tgt = event.currentTarget;
            let stopPropagation = false;

            switch (event.key) {
                case "ArrowLeft":
                    this.moveFocusToAdjacentTab(tgt, -1);
                    stopPropagation = true;
                    break;
                case "ArrowRight":
                    this.moveFocusToAdjacentTab(tgt, 1);
                    stopPropagation = true;
                    break;
                case "Home":
                    this.moveFocusToTab(this.firstTab);
                    stopPropagation = true;
                    break;
                case "End":
                    this.moveFocusToTab(this.lastTab);
                    stopPropagation = true;
                    break;
            }

            if (stopPropagation) {
                event.stopPropagation();
            }
        }

        /**
         * onClick event handler for tabs.
         *
         * @param {object} event The click event object.
         */
        onClick(event) {
            this.setSelectedTab(event.currentTarget);
        }
    }

    class Draad_Toggle {

        constructor(domNode) {

            this.toggle = domNode;
            this.target = document.getElementById(this.toggle.getAttribute('aria-controls'));

            this.toggle.addEventListener('click', this.toggleTarget());

        }

        toggleTarget() {

            const open = new CustomEvent('DraadToggleOpen');
            const close = new CustomEvent('DraadToggleClose');

            const expanded = this.toggle.getAttribute('aria-expanded') === 'true';

            if (expanded) {
                this.toggle.setAttribute('aria-expanded', 'false');
                this.target.setAttribute('aria-hidden', 'true');
                this.target.setAttribute('hidden', 'hidden');
                this.toggle.focus();

                this.toggle.dispatchEvent(close);
            } else {
                this.toggle.setAttribute('aria-expanded', 'true');
                this.target.setAttribute('aria-hidden', 'false');
                this.target.removeAttribute('hidden');
                this.target.focus();

                this.toggle.dispatchEvent(open);
            }

        }

    }

})();