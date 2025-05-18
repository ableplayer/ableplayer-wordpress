const videoEls = document.querySelectorAll( 'video, audio' );
if ( videoEls ) {
	videoEls.forEach((el,index,listObj) => {
		if ( ! el.hasAttribute( 'data-able-player' ) ) {
			el.setAttribute( 'data-able-player', 'true' );
		}
		if ( ! el.hasAttribute( 'id' ) ) {
			el.setAttribute( 'id', 'able-player-id-' + index );
		}
	});
}