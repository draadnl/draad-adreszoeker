/**
 * Shared Draad Adreszoeker functionality
 * Used by both formulier and output blocks
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import { Heading2, Heading3, Surface, UnorderedList, UnorderedListItem } from '@utrecht/component-library-react';
import { ArrowRightIcon, Link } from '@gemeente-denhaag/components-react';

function debounce ( callback, wait ) {
	let timeout;
	return (...args) => {
		clearTimeout(timeout);
		timeout = setTimeout(function () {
			callback.apply(this, args);
		}, wait);
	};
}

// Safe HTML sanitization function
const sanitizeHtml = (htmlString) => {
	if (!htmlString) return '';
	
	// Create a temporary div element
	const tempDiv = document.createElement('div');
	tempDiv.innerHTML = htmlString;
	
	// Remove all script tags and event handlers
	const scripts = tempDiv.querySelectorAll('script');
	scripts.forEach(script => script.remove());
	
	// Remove dangerous attributes
	const allElements = tempDiv.querySelectorAll('*');
	allElements.forEach(element => {
		const attributes = [...element.attributes];
		attributes.forEach(attr => {
			if (attr.name.startsWith('on') || attr.name === 'javascript:') {
				element.removeAttribute(attr.name);
			}
		});
	});
	
	return tempDiv.innerHTML;
};

// Convert HTML string to React elements safely
const HtmlContent = ({ content, className }) => {
	if (!content) return null;
	
	const sanitizedContent = sanitizeHtml(content);
	
	return (
		<div 
			className={className}
			dangerouslySetInnerHTML={{ __html: sanitizedContent }}
		/>
	);
};

// AdviceOutput Component
const AdviceOutput = ({ data }) => {
	const { query, neighbourhood, neighbourhoodData, tabs, tiles } = data;
	
	if (!neighbourhood) {
		return null;
	}
	
	return (
		<div>
			<Surface>
				<div className="draad-adreszoeker__result-heading">
					<Heading2 className="draad-adreszoeker__result-title">{query.street}, {query.number}</Heading2>
				</div>

				<UnorderedList className="draad-adreszoeker__result-list">
					{neighbourhoodData.energielabel && (
						<UnorderedListItem className="draad-adreszoeker__result-list-item">
							<span className="draad-adreszoeker__result-label">Energielabel</span>
							<span className={`draad-adreszoeker__result-value draad-adreszoeker__energylabel --${neighbourhoodData.energielabel?.toLowerCase() || ''}`}>{neighbourhoodData.energielabel}</span>
						</UnorderedListItem>
					)}
					{neighbourhoodData.bouwjaar && (
						<UnorderedListItem className="draad-adreszoeker__result-list-item">
							<span className="draad-adreszoeker__result-label">Bouwjaar</span>
							<span className="draad-adreszoeker__result-value">{neighbourhoodData.bouwjaar}</span>
						</UnorderedListItem>
					)}
					{neighbourhood.post_title && (
						<UnorderedListItem className="draad-adreszoeker__result-list-item">
							<span className="draad-adreszoeker__result-label">Buurt</span>
							<span className="draad-adreszoeker__result-value">{neighbourhood.post_title}</span>
						</UnorderedListItem>
					)}
					{neighbourhoodData.heatSolutionLabel && (
						<UnorderedListItem className="draad-adreszoeker__result-list-item">
							<span className="draad-adreszoeker__result-label">Aardgasvrije oplossing</span> 
							<span className="draad-adreszoeker__result-value">{neighbourhoodData.heatSolutionLabel === 'Hybride warmtepomp' ? 'Hybride warmtepomp (tijdelijk)' : neighbourhoodData.heatSolutionLabel}</span>
						</UnorderedListItem>
					)}
				</UnorderedList>
			</Surface>

			<div className="draad-adreszoeker__result-content">

				{neighbourhoodData.addressTitle && (
					<Heading3 id={neighbourhoodData.addressTitleSanitized} className="draad-adreszoeker__result-advice-title">
						{neighbourhoodData.addressTitle}
					</Heading3>
				)}

				{/* Basis content */}
				{neighbourhoodData.baseContent && (
					<HtmlContent 
						content={neighbourhoodData.baseContent} 
						className="draad-adreszoeker__result-base" 
					/>
				)}
				
				<div className="draad-adreszoeker__result-advice">

					<Surface className="--full">

						<Heading2>Advies voor jouw woning per onderwerp</Heading2>
						
						{neighbourhoodData.buildPeriodDescription && (
							<p className="draad-adreszoeker__result-year-content">
								{neighbourhoodData.buildPeriodDescription}
							</p>
						)}
					
						<div role="tablist" aria-labelledby="tablist-1" className="draad-tabs__tablist">
							{tabs && tabs.map((tab, index) => (
								<button 
									key={tab.key}
									className="draad-tabs__tab" 
									id={`draad-tab-${tab.key}`} 
									type="button" 
									role="tab" 
									aria-controls={`draad-tabpanel-${tab.key}`}
								>
									<span className="focus">
										{tab.label}
									</span>
								</button>
							))}
						</div>
						
						{tabs && tabs.map((tab) => (
							<div 
								key={`panel-${tab.key}`}
								className="draad-tabs__tabpanel" 
								id={`draad-tabpanel-${tab.key}`} 
								role="tabpanel" 
								aria-labelledby={`draad-tab-${tab.key}`}
							>
								<div className="draad-tabs__tabpanel-heading">
									<Heading3 className="draad-tabs__tabpanel-title">
										{tab.label}
									</Heading3>
								</div>
								
								{tab.intro && (
									<HtmlContent content={tab.intro} className="draad-tabs__intro" />
								)}

								<div className="draad-accordion">
									{ typeof tab.advice !== 'undefined' && Array.isArray(tab.advice) && tab.advice.length > 0 && tab.advice.map((advice, index) => (
										<div className="draad-accordion__container" key={index}>
											<h3 className="draad-accordion__panel">
												<button
													aria-controls={`draad-accordion-details-${index}`}
													aria-expanded="false"
													className="draad-accordion__title"
													id={`draad-accofdion-title-${index}`}
												>
													{advice.post_title}
												</button>
												<svg
													aria-hidden="true"
													className="denhaag-icon"
													fill="none"
													focusable="false"
													height="1em"
													shapeRendering="auto"
													viewBox="0 0 24 24"
													width="1em"
													xmlns="http://www.w3.org/2000/svg"
												>
													<path
													d="M5.293 8.293a1 1 0 011.414 0L12 13.586l5.293-5.293a1 1 0 111.414 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414z"
													fill="currentColor"
													/>
												</svg>
											</h3>
											<div
												aria-labelledby={`draad-accofdion-title-${index}`}
												className="draad-accordion__details"
												id={`draad-accordion-details-${index}`}
												role="region"
												>
												<div className="draad-accordion__details-content">
													{advice.content && <HtmlContent content={advice.content} />}
													{advice.excerpt && <HtmlContent content={advice.excerpt} />}
												</div>
											</div>
										</div>
									))}
								</div>
							</div>
						))}

					</Surface>

				</div>
				
				{tiles.tiles && tiles.tiles.length > 0 ? (
					<div className="denhaag-link-grid denhaag-link-grid__no-image">
						<div className="denhaag-link-grid__container">

							{tiles.content.label && (
								<Heading2>
									{tiles.content.label}
								</Heading2>
							)}

							{/* Basis content */}
							{tiles.content.intro && (
								<HtmlContent 
									content={tiles.content.intro} 
								/>
							)}

							<div className="denhaag-link-grid__grid">
								{tiles.tiles.map((tile, index) => (
									<div key={index} className="denhaag-link-grid__item">
										<div className="denhaag-link-grid__item-content">
											{tile.label && <p className="denhaag-link-grid__item-label">{tile.label}</p>}
											{tile.title && (
												<h3 className="denhaag-link-grid__item-title utrecht-heading-4">
													<a className="denhaag-link-grid__item-link" href={tile.link || "/"} rel="noopener noreferrer">
														{tile.title}
													</a>
												</h3>
											)}
											{tile.description && (
												<p className="denhaag-link-grid__item-description utrecht-paragraph">
													{tile.description}
												</p>
											)}
										</div>
										<div className="denhaag-link-grid__item-action">
											<svg aria-hidden="true" className="denhaag-icon" focusable="false" height="1em" viewBox="0 0 24 24" width="1em" xmlns="http://www.w3.org/2000/svg">
												<path d="M12.293 5.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L16.586 13H5a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" fill="currentColor" />
											</svg>
										</div>
									</div>
								))}
							</div>

							{tiles.button && (
								<Link
									href={tiles.button.url}
									icon={<ArrowRightIcon />}
									iconAlign="start"
									target={tiles.button.target}
									>
									{tiles.button.title}
								</Link>
							)}
						</div>
					</div>
				) : ''}

			</div>
		</div>
	);
};

class Draad_Adreszoeker {

	constructor( node ) {

		if ( !node ) {
			throw new Error("No valid element passed to Draad_Adreszoeker class.");
		}
		
		this.node = node;
		this.streetInputNode = this.node.querySelector( '.draad-adreszoeker__field.--street input' );
		this.streetSuggestionsNode = this.node.querySelector( '.draad-adreszoeker__field.--street .draad-adreszoeker__suggestions');
		this.numberInputNode = this.node.querySelector( '.draad-adreszoeker__field.--housenumber input' );
		this.filterFormNode = this.node.querySelector( 'form' );
		this.outputNode = document.querySelector( '.draad-adreszoeker__output' );

		if ( !this.streetInputNode || !this.numberInputNode || !this.filterFormNode ) {
			throw new Error("Draad_Adreszoeker is missing elements in the DOM.");
		}

		this.eventBinder();
		this.populateFromUrlParams();
		this.autoSubmitIfPopulated();

	}

	eventBinder() {
		this.streetInputNode.addEventListener( 'input', debounce( this.streetHandler, 500 ) );
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
		const url = window.draadAdreszoekerAjaxUrl || formData.get('admin-ajax');

		fetch(url + '?action=draad_adreszoeker_get_streets', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams({
				street: formData.get('straatnaam'),
			})
		})
		.then(response => response.json())
		.then(response => {
			const options = response.data;

			// Clear suggestions
			this.streetSuggestionsNode.innerHTML = '';

			if (!options || options.length <= 0) {
				// If there are no results add an empty option making that clear.
				const optionNode = document.createElement('option');
				optionNode.value = '';
				optionNode.textContent = 'Geen resultaten gevonden';
				this.streetSuggestionsNode.appendChild(optionNode);

				this.streetInputNode.setAttribute( 'aria-invalid', 'true' );
				this.numberInputNode.disabled = true;

				// Add notice
				const noticeElement = document.createElement('span');
				noticeElement.className = 'draad-adreszoeker__notice';
				noticeElement.textContent = 'Geen resultaten gevonden';
				this.streetInputNode.parentNode.appendChild(noticeElement);

				return;
			} else if (typeof options === 'object') {
				options.forEach(option => {
					// Add an option for each street.
					const optionNode = document.createElement('option');
					optionNode.value = option.street;
					optionNode.textContent = option.street;
					this.streetSuggestionsNode.appendChild(optionNode);
				});
			}
		})
		.catch(error => {
			throw new Error(error);
		});

	}

	async handleSubmition( event ) {
		event.preventDefault();

		const formData = new FormData(this.filterFormNode);
		const redirectUrl = this.filterFormNode.getAttribute('data-redirect');
		const streetname = formData.get('straatnaam');
		const housenumber = formData.get('huisnummer');
			
		// Check if streetname is valid
		if ( typeof this.streetInputNode === 'undefined' || !streetname || this.streetInputNode.ariaInvalid ) {
			return;
		}

		// Check if housenumber is valid
		if ( typeof this.numberInputNode === 'undefined' || !housenumber || this.numberInputNode.ariaInvalid ) {
			return;
		}

		// Check if output element exists, if not and redirect is set, redirect with parameters
		if (!this.outputNode && redirectUrl) {
			const params = new URLSearchParams();
			if (streetname) {
				params.append('straatnaam', streetname);
			}
			if (housenumber) {
				params.append('huisnummer', housenumber);
			}
														  
			const separator = redirectUrl.includes('?') ? '&' : '?';
			window.location.href = redirectUrl + separator + params.toString();
			return;									   
		}

		const ajaxUrl = window.draadAdreszoekerAjaxUrl || formData.get('admin-ajax');
		fetch(ajaxUrl + '?action=' + formData.get('action'), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams({
				street: streetname,
				number: housenumber,
			})
		})
		.then(response => response.json())
		.then(response => {

			if ( !response.success ) {
				const noResultsMessage = document.createElement('div');
				noResultsMessage.classList.add('draad-adreszoeker__no-results');
				noResultsMessage.innerHTML = `
					<div class="utrecht-surface">
						<div class="draad-adreszoeker__result-heading">
							<h2 class="utrecht-heading-2 draad-adreszoeker__result-title">${response.data}</h2>
						</div>
					</div>`;
				this.outputNode.innerHTML = '';
				this.outputNode.appendChild(noResultsMessage);
				return;
			}

			// Render React component using React 18 API
			if (!this.outputNode._reactRoot) {
				this.outputNode._reactRoot = createRoot(this.outputNode);
			}
			this.outputNode._reactRoot.render(
				React.createElement(AdviceOutput, { data: response.data })
			);

			this.addNotice( 'Het advies is geopend.' );

			// Initialize tabs after React component is rendered
			setTimeout(() => {
				this.outputNode.querySelectorAll(".draad-tabs__tablist")?.forEach((tablist) => new Draad_Tabs(tablist));
				this.outputNode.querySelectorAll(".draad-accordion__container")?.forEach((accordion) => new Draad_Accordion(accordion));
			}, 100);
		})
		.catch(error => {
			
			throw new Error(error);
		});
	}

	populateFromUrlParams() {
		const urlParams = new URLSearchParams(window.location.search);
		const inputs = this.node.querySelectorAll('input[name]');
		
		inputs.forEach(input => {
			const paramValue = urlParams.get(input.name);
			if (paramValue) {
				input.value = paramValue;
			}
		});
	}

	autoSubmitIfPopulated() {
		const streetValue = this.streetInputNode.value.trim();
		const numberValue = this.numberInputNode.value.trim();
		
		// If both required fields are populated, submit automatically
		if (streetValue && numberValue) {
			// Small delay to ensure DOM is fully ready
			setTimeout(() => {
				this.handleSubmition(new Event('submit'));
			}, 100);
		}
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

class Draad_Accordion {

    constructor(node) {

        this.rootEl = node;
        this.buttonEl = this.rootEl.querySelector('.draad-accordion__title');

        const controlsId = this.buttonEl.getAttribute('aria-controls');
        this.contentEl = document.getElementById(controlsId);

        this.open = this.buttonEl.getAttribute('aria-expanded') === 'true';

        // add event listeners
        this.buttonEl.addEventListener('click', this.onButtonClick.bind(this));

    }

    onButtonClick() {
        this.toggle(!this.open);
    }

    toggle(open) {

        // don't do anything if the open state doesn't change
        if (open === this.open) {
            return;
        }

        // update the internal state
        this.open = open;

        // handle DOM updates
        this.buttonEl.setAttribute('aria-expanded', `${open}`);
        if (open) {
            this.contentEl.removeAttribute('hidden');
            this.contentEl.setAttribute('aria-hidden', 'false');
        } else {
            this.contentEl.setAttribute('aria-hidden', 'true');
            this.contentEl.setAttribute('hidden', 'hidden');
        }

    }

    // Add public open and close methods for convenience
    open() {
        this.toggle(true);
    }

    close() {
        this.toggle(false);
    }

}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
	const nodes = document.querySelectorAll('.draad-adreszoeker');
	nodes?.forEach(node => new Draad_Adreszoeker(node));
});

// Export for use by other modules
export { Draad_Adreszoeker, Draad_Tabs, Draad_Accordion, AdviceOutput, HtmlContent, sanitizeHtml };