/* Visitor colourway choice: sets <html data-style> and remembers it. */
( () => {
	const root = document.documentElement;
	const KEY = 'peak-style';

	/*
	 * Jetpack's Like buttons live in iframes that take the page's text and link colours once,
	 * when they load. After a colourway change, send Jetpack the new colours (the same
	 * "injectStyles" message its own script sends) and reload the buttons so they use them.
	 */
	const refreshJetpackLikes = () => {
		const master = window.frames[ 'likes-master' ];
		const text = document.querySelector( '.sd-text-color' );
		const link = document.querySelector( '.sd-link-color' );
		if ( ! master || ! text || ! link ) {
			return;
		}
		const t = getComputedStyle( text );
		const l = getComputedStyle( link );
		const data = {
			event: 'injectStyles',
			textStyles: { color: t.color, fontFamily: t.fontFamily, fontSize: t.fontSize, direction: t.direction, fontWeight: t.fontWeight, fontStyle: t.fontStyle, textDecoration: t.textDecoration },
			linkStyles: { color: l.color, fontFamily: l.fontFamily, fontSize: l.fontSize, textDecoration: l.textDecoration, fontWeight: l.fontWeight, fontStyle: l.fontStyle },
		};
		try {
			master.postMessage( JSON.stringify( { type: 'likesMessage', data } ), '*' );
		} catch ( e ) {
			return;
		}
		document.querySelectorAll( '.jetpack-likes-widget-loaded iframe' ).forEach( ( frame ) => {
			const src = frame.src;
			frame.src = 'about:blank';
			setTimeout( () => {
				frame.src = src;
			}, 50 );
		} );
	};

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
			refreshJetpackLikes();
		};

		button?.addEventListener( 'click', () => apply( variations.find( ( v ) => v.slug !== current() ).slug ) );
		select?.addEventListener( 'change', () => apply( select.value ) );
		prefersDark.addEventListener?.( 'change', () => {
			sync();
			if ( ! root.dataset.style ) {
				refreshJetpackLikes();
			}
		} );

		el.hidden = false;
		sync();
	} );
} )();
