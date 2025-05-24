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

	const clipboard = new ClipboardJS('.ableplayer-copy-to-clipboard');
	clipboard.on( 'success', function(e) {
		let parent   = e.trigger.parentNode;
		let response = parent.querySelector( '.ableplayer-notice-copied' );
		let text     = response.textContent;
		wp.a11y.speak( text );
		response.classList.add( 'visible' );
	});

	let reset = document.querySelectorAll( '.reset-ableplayer' );
	if ( null !== reset ) {
		reset.forEach( (el) => {
			el.addEventListener( 'click', resetShortcode );
			function resetShortcode( e ) {
				let control    = e.target;
				const controls = document.querySelectorAll( '.ableplayer-generator-inputs input, .ableplayer-generator-inputs select' );
				for (i = 0; i < controls.length; i++) {
					switch ( controls[i].type ) {
						case 'select-multiple':
						case 'select-one':
							controls[i].value = controls[i].querySelector( 'option:first-of-type' ).getAttribute( 'value' );
							break;
						case 'text':
						case 'email':
						case 'date':
						case 'url':
						case 'search':
						case 'textarea':
							controls[i].value = '';
							break;
						case 'checkbox':
						case 'radio':
							controls[i].checked = false;
							break;
					}
				}
				let shortcode = document.querySelectorAll( '.ableplayer-shortcode-container' );
				shortcode.forEach( (el) => {
					el.value = '[' + control.getAttribute( 'data-type' ) + ']';
				});
			}
		});
	}

	$( '.media-sources' ).hide();
	let active = $( '#source_type' ).val();
	$( '.media-sources.' + active ).show();
	$( '.media-sources.' + active ).find( 'input' ).attr( 'required', 'true' );
	$( '#source_type' ).on( 'change', function(e) {
		let current = $( this ).val();
		$( '.media-sources' ).hide();
		$( '.media-sources' ).find( 'input' ).removeAttr( 'required' );
		$( '.media-sources.' + current ).show();
		$( '.media-sources.' + current ).find( 'input' ).attr( 'required', 'true' );
	});

	var mediaPopup = '';

	function clear_existing() {
		if (typeof mediaPopup !== 'string') {
			mediaPopup.detach();
			mediaPopup = '';
		}
	}

	$('.ableplayer-remove-preview').on( 'click', function (e) {
		let controlled  = $( this ).data( 'input' );
		const container = $( this ).parent().parent( '.ableplayer-media-preview' );
		$( '#' + controlled ).val( '' );
		$( '.preview-' + controlled + ' > *').remove();
		container.removeClass( 'has-value' );
		wp.a11y.speak( ableplayer.removed );
	});

	$('.upload-ableplayer-media').on( 'click', function (e) {
		let input          = $( this ).data( 'input' );
		const idField      = document.querySelector( 'input[name="' + input + '"]' );
		const displayField = document.querySelector( '.preview-' + input );
		const container    = $( this ).parent().parent( '.ableplayer-media-preview' );
		console.log( container );
		let library;
		if ( 'media-id' === input || 'media-desc-id' === input || 'media-asl-id' === input ) {
			library = [ 'audio', 'video' ];
		} else if ( 'poster' === input ) {
			library = ['image'];
		} else {
			library = ['text/vtt'];
		}
		clear_existing();
		mediaPopup = wp.media({
			multiple: false, // add, reset, false.
			title: ableplayer[ input + 'Title'],
			library: {
				type: library,
			},
			button: {
				text: ableplayer.buttonName,
			}
		});

		mediaPopup.on('select', function () {
			let selection = mediaPopup.state().get('selection'),
				id = '',
				img = '',
				height = '',
				width = '',
				alt = '';
			if (selection) {
				id                     = selection.first().attributes.id;
				if ( input === 'poster' ) {
					height                 = ableplayer.thumbHeight;
					width                  = Math.round( ( ( selection.first().attributes.width ) / ( selection.first().attributes.height ) ) * height );
					alt                    = selection.first().attributes.alt;
					img                    = "<img id='event_image' src='" + selection.first().attributes.url + "' width='" + width + "' height='" + height + "' alt='" + alt + "' />";
					idField.value          = id;
					displayField.innerHTML = img;
				} else {
					displayField.innerHTML = '<pre>' + selection.first().attributes.url.replace( ableplayer.homeUrl, '' ) + '</pre>';
					idField.value          = id;
				}
				container.addClass( 'has-value' );
			}
		});

		mediaPopup.open();
	});

});
