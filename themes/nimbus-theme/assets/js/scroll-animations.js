/**
 * Smuggler Theme — Scroll-triggered animations
 * Adds .is-visible to .smuggler-fade-in elements when they enter the viewport.
 */
( function () {
	if ( ! ( 'IntersectionObserver' in window ) ) {
		document.querySelectorAll( '.smuggler-fade-in' ).forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.15 }
	);

	document.querySelectorAll( '.smuggler-fade-in' ).forEach( function ( el ) {
		observer.observe( el );
	} );
} )();
