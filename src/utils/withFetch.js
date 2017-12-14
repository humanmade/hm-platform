import 'whatwg-fetch';
import React from 'react';

/**
 * Simple and perhaps clunky fetch HoC
 */
export default const withFetch = url => {
	return Component => class extends React.Component {
		construct() {
			this.state = {
				loading: true,
				expires: 0,
				data:    {},
				error:   false,
			};
		}

		componentWillMount() {
			const item = this.fetchStore();
			if ( item ) {
				this.setState( item );
			}
		}

		componentDidMount() {
			if ( this.state.expires > Date.now() ) {
				return;
			}

			this.setState( { loading: true } );

			fetch( url )
				.then( response => response.json() )
				.then( data => this.updateStore( data, false ) )
				.catch( error => this.updateStore( {}, false, error ) );
		}

		fetchStore() {
			const store = window.localStorage.getItem( 'HMDocs' );
			return store[ url ] || false;
		}

		updateStore( data, loading = false, error = false ) {
			const update = {
				data,
				loading,
				error,
				expires: Date.now() + ( 5 * 60 * 1000 ) // 5 minutes.
			};

			// Update store.
			const store = window.localStorage.getItem( 'HMDocs' );
			window.localStorage.setItem( 'HMDocs', Object.assign( store, {
				[ url ]: update
			} ) );

			// Update state.
			this.setState( update );
		}

		render() {
			return React.cloneElement( Component, {
				data:    this.state.data,
				loading: this.state.loading,
				error:   this.state.error,
			} );
		}
	}
}
