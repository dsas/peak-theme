/* Visitor colourway choice: sets <html data-style> and remembers it. */
( () => {
	const root = document.documentElement;
	const KEY = 'peak-style';

	document.querySelectorAll( '.peak-switcher' ).forEach( ( el ) => {
		const variations = JSON.parse( el.dataset.variations || '[]' );
		if ( variations.length < 2 ) {
			return;
		}
		const dark = variations.find( ( v ) => v.scheme === 'dark' );
		const prefersDark = window.matchMedia( '(prefers-color-scheme: dark)' );
		const current = () =>
			root.dataset.style || ( dark && prefersDark.matches ? dark.slug : variations[ 0 ].slug );
		const button = el.querySelector( '.peak-switcher__toggle' );
		const select = el.querySelector( '.peak-switcher__select' );

		const sync = () => {
			const slug = current();
			const active = variations.find( ( v ) => v.slug === slug ) || variations[ 0 ];
			if ( button ) {
				const next = variations.find( ( v ) => v.slug !== slug );
				button.setAttribute( 'aria-label', ( el.dataset.label || 'Switch to %s' ).replace( '%s', next.title ) );
				button.dataset.scheme = active.scheme;
			}
			if ( select ) {
				select.value = slug;
			}
		};

		const apply = ( slug ) => {
			root.dataset.style = slug;
			try {
				localStorage.setItem( KEY, slug );
			} catch ( e ) {}
			sync();
		};

		button?.addEventListener( 'click', () => apply( variations.find( ( v ) => v.slug !== current() ).slug ) );
		select?.addEventListener( 'change', () => apply( select.value ) );
		prefersDark.addEventListener?.( 'change', sync );

		el.hidden = false;
		sync();
	} );
} )();
