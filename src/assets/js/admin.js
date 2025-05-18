jQuery(document).ready(function ($) {
	let firstItem = window.location.hash;
	const tabGroups = document.querySelectorAll( '.ableplayer-tabs' );
	for ( let i = 0; i < tabGroups.length; i++ ) {
		const panel = $( tabGroups[i] ).find( firstItem );
		if ( panel.length !== 0 ) {
			showPanel( firstItem );
		} else {
			firstItem = $( tabGroups[i] ).find( '[role=tablist]' ).attr( 'data-default' );
			showPanel( '#' + firstItem );
		}
	}
	const tabs = document.querySelectorAll('.ableplayer-tabs [role=tab]'); // get all role=tab elements as a variable.
	for ( let i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener( 'click', showTabPanel );
		tabs[i].addEventListener( 'keydown', handleKeyPress );
	} // add click event to each tab to run the showTabPanel function.
	/**
	 * Activate a panel from the click event.
	 *
	 * @param event Click event.
	 */
	function showTabPanel(e) {
		const tabContainer   = $( e.currentTarget ).closest( '.tabs' );
		const tabs           = tabContainer.find( '[role=tab]' );
		const container      = $( e.currentTarget ).closest( '.ableplayer-tabs' );
		const inside         = $( e.currentTarget ).parents( '.inside' );
		const tabPanelToOpen = e.target.getAttribute('aria-controls');
		const iframes        = $( 'iframe.mc-iframe' );
		let tabPanels        = [];
		if ( inside.length == 0 && ! container.hasClass( 'mcs-tabs' ) ) {
			tabPanels = container.find( '.ui-sortable > [role=tabpanel]' );
		} else {
			tabPanels = container.find( '[role=tabpanel]' );
		}
		for ( let i = 0; i < tabs.length; i++) {
			tabs[i].setAttribute('aria-selected', 'false');
		} // reset all tabs to aria-selected=false and normal font weight
		e.target.setAttribute('aria-selected', 'true'); //set aria-selected=true for clicked tab
		for ( let i = 0; i < tabPanels.length; i++) {
			tabPanels[i].setAttribute( 'aria-hidden', 'true' );
		} // hide all tabpanels
		// If this is an inner tab panel, don't set the window location.
		if ( inside.length == 0 ) {
			window.location.hash = tabPanelToOpen;
		}
		document.getElementById(tabPanelToOpen).setAttribute( 'aria-hidden', 'false' ); //show tabpanel
		for ( let i = 0; i < iframes.length; i++ ) {
			let iframe = iframes[i];
			resizeIframe(iframe);
		}
		$( '#' + tabPanelToOpen ).attr( 'tabindex', '-1' ).trigger( 'focus' );
		window.scrollTo( 0,0 );
	}

	/**
	 * Activate a panel from panel ID.
	 *
	 * @param string hash Item ID.
	 */
	function showPanel(hash) {
		let id             = hash.replace( '#', '' );
		const control      = $( 'button[aria-controls=' + id + ']' );
		const tabContainer = $( hash ).closest( '.tabs' );
		const tabs         = tabContainer.find( '[role=tab]' );
		const container    = $( hash ).closest( '.ableplayer-tabs' );
		const tabPanels    = container.find( '[role=tabpanel]' );
		const currentPanel = document.getElementById(id);

		for ( let i = 0; i < tabs.length; i++) {
			tabs[i].setAttribute('aria-selected', 'false');
		} //reset all tabs to aria-selected=false and normal font weight
		control.attr('aria-selected', 'true'); //set aria-selected=true for clicked tab
		for ( let i = 0; i < tabPanels.length; i++) {
			tabPanels[i].setAttribute( 'aria-hidden', 'true' );
		}
		if ( null !== currentPanel ) {
			currentPanel.setAttribute( 'aria-hidden', 'false' ); //show tabpanel
		}
	}

	// Arrow key handlers.
	function handleKeyPress(e) {
		if (e.keyCode == 37) { // left arrow
			$( e.currentTarget ).prev().trigger('click').trigger('focus');
			e.preventDefault();
		}
		if (e.keyCode == 38) { // up arrow
			$( e.currentTarget ).prev().trigger('click').trigger('focus');
			e.preventDefault();
		}
		if (e.keyCode == 39) { // right arrow
			$( e.currentTarget ).next().trigger('click').trigger('focus');
			e.preventDefault();
		}
		if (e.keyCode == 40) { // down arrow.
			$( e.currentTarget ).next().trigger('click').trigger('focus');
			e.preventDefault();
		}
	};
});