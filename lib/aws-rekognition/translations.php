<?php

namespace HM\Platform\Rekognition;

use Google\Cloud\Translate\TranslateClient;

add_filter( 'hm.aws.rekognition.alt_text', function ( $alt_text, $data, $id ) {
	$translation = get_translation( $alt_text );

	if ( ! $translation ) {
		return $alt_text;
	}

	// Add indicator that text is machine translated.
	update_post_meta( $id, 'hm.google_translated_to_lang', get_language() );

	return $translation;
}, 10, 3 );

add_filter( 'hm.aws.rekognition.keywords', function ( $keywords, $data, $id ) {
	$translation = get_translation( implode( ', ', $keywords ) );

	if ( ! $translation ) {
		return $keywords;
	}

	return [ $translation ];
}, 10, 3 );

// Show google attribution if we've translated alt text.
add_filter( 'attachment_fields_to_edit', function ( $form_fields, $post ) {
	$is_translated_from = get_post_meta( $post->ID, 'hm.google_translated_to_lang', true );

	if ( empty( $is_translated_from ) ) {
		return $form_fields;
	}

	$form_fields['hm-google-translate-attribution'] = [
		'label' => '',
		'input' => 'html',
		'html'  => '
			' . esc_html__( 'Alt text translated by', 'hm-platform' ) . '
			<svg width="50px" height="16px" viewBox="0 0 50 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="vertical-align:middle;">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					<g>
						<g transform="translate(0, 0.127273)" fill="#757575">
							<path d="M6.16,5.45866667 L6.16,7.09733333 L10.082,7.09733333 C9.96466667,8.01933333 9.65733333,8.69266667 9.18933333,9.16066667 C8.618,9.73133333 7.72533333,10.3606667 6.16,10.3606667 C3.74466667,10.3606667 1.85733333,8.41466667 1.85733333,6 C1.85733333,3.58533333 3.74466667,1.63933333 6.16,1.63933333 C7.462,1.63933333 8.41333333,2.15133333 9.116,2.81 L10.272,1.65333333 C9.29133333,0.717333333 7.98933333,7.10542736e-15 6.16,7.10542736e-15 C2.85266667,7.10542736e-15 0.072,2.69266667 0.072,6 C0.072,9.30733333 2.85266667,12 6.16,12 C7.94466667,12 9.29133333,11.4146667 10.3453333,10.3173333 C11.428,9.234 11.7646667,7.712 11.7646667,6.48266667 C11.7646667,6.10266667 11.7353333,5.75133333 11.6766667,5.45866667 L6.16,5.45866667"></path>
							<path d="M16.6666667,4.12733333 C14.5253333,4.12733333 12.7793333,5.756 12.7793333,8 C12.7793333,10.2293333 14.5253333,11.8726667 16.6666667,11.8726667 C18.8086667,11.8726667 20.554,10.2293333 20.554,8 C20.554,5.756 18.8086667,4.12733333 16.6666667,4.12733333 L16.6666667,4.12733333 Z M16.6666667,10.3466667 C15.4933333,10.3466667 14.4806667,9.37866667 14.4806667,8 C14.4806667,6.60666667 15.4933333,5.65266667 16.6666667,5.65266667 C17.84,5.65266667 18.8526667,6.60666667 18.8526667,8 C18.8526667,9.37866667 17.84,10.3466667 16.6666667,10.3466667 L16.6666667,10.3466667 Z"></path>
							<path d="M35.72,4.99333333 L35.6613333,4.99333333 C35.2793333,4.538 34.546,4.12733333 33.622,4.12733333 C31.686,4.12733333 30,5.814 30,8 C30,10.1706667 31.686,11.8726667 33.622,11.8726667 C34.546,11.8726667 35.2793333,11.462 35.6613333,10.9926667 L35.72,10.9926667 L35.72,11.534 C35.72,13.0153333 34.928,13.8073333 33.6513333,13.8073333 C32.61,13.8073333 31.9646667,13.0593333 31.7006667,12.4286667 L30.2193333,13.0446667 C30.6446667,14.0713333 31.774,15.3333333 33.6513333,15.3333333 C35.6466667,15.3333333 37.3333333,14.16 37.3333333,11.2993333 L37.3333333,4.32666667 L35.72,4.32666667 L35.72,4.99333333 L35.72,4.99333333 Z M33.7693333,10.3466667 C32.596,10.3466667 31.702,9.34933333 31.702,8 C31.702,6.636 32.596,5.65266667 33.7693333,5.65266667 C34.9286667,5.65266667 35.8373333,6.65066667 35.8373333,8.01466667 C35.8373333,9.364 34.9286667,10.3466667 33.7693333,10.3466667 L33.7693333,10.3466667 Z"></path>
							<path d="M25.3333333,4.12733333 C23.192,4.12733333 21.446,5.756 21.446,8 C21.446,10.2293333 23.192,11.8726667 25.3333333,11.8726667 C27.4753333,11.8726667 29.2206667,10.2293333 29.2206667,8 C29.2206667,5.756 27.4753333,4.12733333 25.3333333,4.12733333 L25.3333333,4.12733333 Z M25.3333333,10.3466667 C24.16,10.3466667 23.148,9.37866667 23.148,8 C23.148,6.60666667 24.16,5.65266667 25.3333333,5.65266667 C26.5066667,5.65266667 27.5193333,6.60666667 27.5193333,8 C27.5193333,9.37866667 26.5066667,10.3466667 25.3333333,10.3466667 L25.3333333,10.3466667 Z"></path>
							<path d="M38.6666667,0.16 L40.34,0.16 L40.34,11.8726667 L38.6666667,11.8726667 L38.6666667,0.16 Z"></path>
							<path d="M45.51,10.3466667 C44.6446667,10.3466667 44.0286667,9.95133333 43.632,9.174 L48.81,7.032 L48.634,6.592 C48.3113333,5.72666667 47.3286667,4.12733333 45.3193333,4.12733333 C43.324,4.12733333 41.6666667,5.69733333 41.6666667,8 C41.6666667,10.1713333 43.3093333,11.8726667 45.51,11.8726667 C47.2846667,11.8726667 48.3113333,10.7873333 48.7366667,10.156 L47.4166667,9.276 C46.9766667,9.922 46.3753333,10.3466667 45.51,10.3466667 L45.51,10.3466667 Z M45.384,5.57866667 C46.0726667,5.57866667 46.656,5.92933333 46.8506667,6.43266667 L43.3533333,7.88266667 C43.3533333,6.25 44.508,5.57866667 45.384,5.57866667 L45.384,5.57866667 Z"></path>
						</g>
					</g>
				</g>
			</svg>
		',
	];

	return $form_fields;
}, 10, 2 );

/**
 * Return the Google Translate client.
 *
 * @return TranslateClient
 */
function get_client() {
	$key = null;

	if ( defined( 'GOOGLE_API_KEY' ) ) {
		$key = GOOGLE_API_KEY;
	}

	return new TranslateClient( [
		'key' => $key,
	] );
}

/**
 * Get site language if not english.
 *
 * @return false|string
 */
function get_language() {
	$site_language = get_option( 'WPLANG' ) ?: get_locale();

	if ( strpos( $site_language, 'en_' ) === 0 ) {
		return false;
	}

	return $site_language;
}

/**
 * Translate a string in the current site language.
 *
 * @param string $string
 * @return false|string
 */
function get_translation( string $string ) {
	$translate     = get_client();
	$site_language = get_language();

	if ( ! $site_language ) {
		return $string;
	}

	try {
		$result = $translate->translate( $string, [
			'target' => strtok( $site_language, '_' ),
		] );

		if ( empty( $result ) || empty( $result['text'] ) ) {
			return $string;
		}

		return $result['text'];
	} catch ( \Exception $e ) {
		trigger_error( $e->getMessage() ); // phpcs:ignore
	}

	return false;
}