<?php

namespace HM\Platform;

use LudicrousDB;

class DB extends LudicrousDB {
	public $check_tcp_responsiveness = false;
	function query( $query ) {
		$start = microtime( true );
		$result = parent::query( $query );
		if ( function_exists( __NAMESPACE__ . '\\XRay\\trace_wpdb_query' ) ) {
			$host = $this->current_host ?: $this->last_connection['host'];
			// Host gets the port number applied, which we don't want to add.
			$host = strtok( $host, ':' );
			XRay\trace_wpdb_query( $query, $start, microtime( true ), $result === false ? $this->last_error : null, $host );
		}
		return $result;
	}

	/**
	 * Determines the best charset and collation to use given a charset and collation.
	 *
	 * For example, when able, utf8mb4 should be used instead of utf8.
	 *
	 * @param string $charset The character set to check.
	 * @param string $collate The collation to check.
	 * @return array The most appropriate character set and collation to use.
	 */
	public function determine_charset( $charset, $collate ) {
		$charset_collate = parent::determine_charset( $charset, $collage );
		$charset = $charset_collate['charset'];
		$collate = $charset_collate['collate'];

		if ( 'utf8mb4' === $charset ) {
			// _general_ is outdated, so we can upgrade it to _unicode_, instead.
			if ( ! $collate || 'utf8_general_ci' === $collate ) {
				$collate = 'utf8mb4_unicode_ci';
			} else {
				$collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
			}
		}
		return compact( 'charset', 'collate' );
	}
}
