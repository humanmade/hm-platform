<?php

namespace HM\Platform\Rekognition;

use Google\Cloud\Translate\TranslateClient;

add_filter( 'hm.aws.rekognition.alt_text', function ( $alt_text, $data, $id ) {
	$translation = get_translation( $alt_text );

	if ( ! $translation ) {
		return $alt_text;
	}

	return $translation;
}, 10, 2 );

add_filter( 'hm.aws.rekognition.keywords', function ( $keywords, $data, $id ) {
	$translation = get_translation( implode( ', ', $keywords ) );

	if ( ! $translation ) {
		return $keywords;
	}

	return [ $translation ];
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
