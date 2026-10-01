/* Highlights the year currently in view. The links work without this. */
( () => {
	const rail = document.querySelector( '.peak-year-rail' );
	if ( ! rail || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}
	const links = new Map(
		[ ...rail.querySelectorAll( 'a[data-year]' ) ].map( ( a ) => [ 'y' + a.dataset.year, a ] )
	);
	const setCurrent = ( id ) => {
		links.forEach( ( a, key ) => {
			if ( key === id ) {
				a.setAttribute( 'aria-current', 'location' );
			} else {
				a.removeAttribute( 'aria-current' );
			}
		} );
	};
	const observer = new IntersectionObserver(
		( entries ) => entries.filter( ( e ) => e.isIntersecting ).forEach( ( e ) => setCurrent( e.target.id ) ),
		{ rootMargin: '0px 0px -70% 0px' }
	);
	links.forEach( ( a, id ) => {
		const section = document.getElementById( id );
		if ( section ) {
			observer.observe( section );
		}
	} );
	setCurrent( links.keys().next().value );
} )();
