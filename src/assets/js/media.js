let   ableplayer_selectors = [];
if ( ableplayer.settings.replace_video === 'true' ) {
	ableplayer_selectors.push( 'video' );
}
if ( ableplayer.settings.replace_audio === 'true' ) {
	ableplayer_selectors.push( 'audio' );
}
if ( 0 !== ableplayer_selectors.length ) {
	const mediaEls = document.querySelectorAll( ableplayer_selectors );
	if ( mediaEls ) {
		mediaEls.forEach((el,index,listObj) => {
			if ( ! el.hasAttribute( 'data-able-player' ) ) {
				el.setAttribute( 'data-able-player', 'true' );
			}
			if ( ! el.hasAttribute( 'id' ) ) {
				el.setAttribute( 'id', 'able-player-id-' + index );
			}
		});
	}
}

const ablePlayers = document.querySelectorAll( '[data-able-player]' );
if ( ablePlayers ) {
	ablePlayers.forEach((el,index,listObj) => {
		if ( 'true' === ableplayer.settings.youtube_nocookie ) {
			if ( ! el.hasAttribute( 'data-youtube-nocookie' ) ) {
				el.setAttribute( 'data-youtube-nocookie', 'true' );
			}
		}
		if ( 'auto' !== ableplayer.settings.default_heading ) {
			if ( ! el.hasAttribute( 'data-heading-level' ) ) {
				el.setAttribute( 'data-heading-level', ableplayer.settings.default_heading );
			}
		}
		if ( 'animals' !== ableplayer.settings.default_heading ) {
			if ( ! el.hasAttribute( 'data-speed-icons' ) ) {
				el.setAttribute( 'data-speed-icons', 'arrows' );
			}
		}
		if ( 'true' === ableplayer.settings.hide_controls ) {
			if ( ! el.hasAttribute( 'data-hide-controls' ) ) {
				el.setAttribute( 'data-hide-controls', 'true' );
			}
		}
	});
}