<?php
/**
 *
 *
 * @package PhpStorm.
 */

namespace Vantage;

/**
 * Class Vantage_API
 */
class Vantage_API {
	var $application_id;
	var $api_key;
	var $url;

	/**
	 *
	 *
	 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/alarms
	 */
	public function get_activity() {

	}


	public function get_bandwidth_usage() {}


	/**
	 *
	 *
	 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/
	 */
	public function get_environment_data() {}


	public function get_page_generation_time() {}

	/**
	 *
	 *
	 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/pull-requests
	 */
	public function get_pull_requests() {}


	private function query_api() {}
}
