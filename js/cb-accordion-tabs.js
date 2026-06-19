/* global document */
( function () {
	'use strict';

	function init( root ) {
		var triggers = Array.prototype.slice.call( root.querySelectorAll( '.cb-accordion-tabs__trigger' ) );
		if ( ! triggers.length ) {
			return;
		}

		var mode = root.getAttribute( 'data-cb-accordion-tabs' ) || 'tabs';

		function panelFor( trigger ) {
			var id = trigger.getAttribute( 'aria-controls' );
			return id ? document.getElementById( id ) : null;
		}

		function setState( trigger, open ) {
			var panel = panelFor( trigger );
			trigger.classList.toggle( 'is-active', open );
			trigger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( trigger.getAttribute( 'role' ) === 'tab' ) {
				trigger.setAttribute( 'aria-selected', open ? 'true' : 'false' );
			}
			if ( panel ) {
				panel.hidden = ! open;
			}
		}

		function open( trigger ) {
			triggers.forEach( function ( item ) {
				setState( item, item === trigger );
			} );
			root.classList.add( 'has-active' );
		}

		function toggle( trigger ) {
			var isOpen = trigger.getAttribute( 'aria-expanded' ) === 'true';

			if ( 'accordion' === mode ) {
				if ( isOpen ) {
					setState( trigger, false );
					root.classList.remove( 'has-active' );
				} else {
					open( trigger );
				}
				return;
			}

			if ( ! isOpen ) {
				open( trigger );
			}
		}

		triggers.forEach( function ( trigger, index ) {
			trigger.addEventListener( 'click', function () {
				toggle( trigger );
			} );

			trigger.addEventListener( 'keydown', function ( event ) {
				var next;

				switch ( event.key ) {
					case 'ArrowDown':
					case 'ArrowRight':
						next = triggers[ index + 1 ] || triggers[ 0 ];
						break;
					case 'ArrowUp':
					case 'ArrowLeft':
						next = triggers[ index - 1 ] || triggers[ triggers.length - 1 ];
						break;
					case 'Home':
						next = triggers[ 0 ];
						break;
					case 'End':
						next = triggers[ triggers.length - 1 ];
						break;
					default:
						return;
				}

				event.preventDefault();
				next.focus();
				if ( 'tabs' === mode ) {
					open( next );
				}
			} );
		} );

		open( triggers[ 0 ] );
	}

	function boot() {
		document.querySelectorAll( '[data-cb-accordion-tabs]' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
