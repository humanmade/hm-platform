<?php

namespace HM\Platform;

use LudicrousDB;

class DB extends LudicrousDB {
	function query( $query ) {
		$start = microtime( true );
		$result = parent::query( $query );
		//	error_log( $this->last_connection['host'] );
		if ( function_exists( __NAMESPACE__ . '\\XRay\\trace_wpdb_query' ) ) {
			$host = $this->current_host ?: $this->last_connection['host'];
			// Host gets the port number applied, which we don't want to add.
			$host = strtok( $host, ':' );
			XRay\trace_wpdb_query( $query, $start, microtime( true ), $result === false ? $this->last_error : null, $host );
		}
		return $result;
	}
}
