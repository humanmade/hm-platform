import 'whatwg-fetch';
import React from 'react';

/**
 * Fetch HoC using localStorage.
 */
const withFetch = url => {
	return Component => {
		class fetchComponent extends React.Component {
			constructor() {
				super();

				// eslint-disable-next-line
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
				return ( store && store[ url ] ) || null;
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
				window.localStorage.setItem( 'HMDocs', Object.assign( store || {}, {
					[ url ]: update
				} ) );

				// Update state.
				this.setState( update );
			}

			render() {
				return <Component {...this.state} {...this.props} />;
			}
		}

		// Set a nice display name.
		fetchComponent.displayName = `withFetch(${url})(${Component.displayName || Component.name || 'Component'})`;

		return fetchComponent;
	}
}

export default withFetch;
