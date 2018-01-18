<?php
/**
 * Stub return data from HM Stack.
 *
 * @package HMStackIntegration.
 */

namespace HM_Stack\Tests;

/**
 * Function that returns mock HM Stack API responses. Used as a callback for `pre_http_request`.
 *
 * @param mixed  $preempt Unused. Whether to preempt an HTTP request's return value.
 * @param array  $r       Unused. HTTP request arguments.
 * @param string $url     The request URL.
 * @return string|array Mock API return.
 */
function mock_returns( $preempt, $r, $url ) {
	// Verify that we're parsing the correct URL structure.
	if ( -1 === strpos( $url, HM_STACK_API_URL ) ) {
		return 'error';
	}

	// Get stubbed data.
	$return = raw_stubs( str_replace( HM_STACK_API_URL, '', $url ) );

	// If our array key fails to find a proper match, still return a non-false to short-circuit the request.
	if ( false === $return ) {
		return 'error';
	}

	// Map the return URL into a proper response.
	return [
		'body'     => $return,
		'response' => [ 'code' => 200 ],
	];
}

/**
 * Raw HM Stack data stubs.
 *
 * @param $type string
 * @return string|bool
 */
function raw_stubs( $type ) {
	switch ( $type ) {
		case '':
			return '{"id":"wp-api-demo-production","git-deployment":{"url":"git@github.com:humanmade\/wp-api-demo.git","ref":"master","is_autoupdating":"yes","branch_details":{"latest_commit":{"rev":"f332b6be6b8a4ba964444d9b17604475f417ec22","date":"2017-12-26T00:53:52Z","description":"Merge pull request #4 from humanmade\/fix-console\n\nUpdate plugins and Add Restsplain","user":{"name":"Ryan McCue","avatar_urls":{"96":"https:\/\/avatars3.githubusercontent.com\/u\/19864447?v=4"}}}}},"version":"2.4.2","proxy-ssh-details":{"hostname":"eu-west-1.aws.hmn.md","port":22,"username":"ubuntu"},"aws-console-url":"https:\/\/console.aws.amazon.com\/cloudformation\/home?region=eu-west-1#\/stacks?stackId=arn:aws:cloudformation:eu-west-1:577418818413:stack%2Fwp-api-demo-production%2Fc6fbf490-0a3a-11e5-be44-5001411350e0","architecture":"ami-application-stack","status":"available","ami":"ami-c56296bc","instance-type":"t2.nano","has-bastion":true,"ssh-connection-string":"ssh ubuntu@eu-west-1.aws.hmn.md -A -p 22 -t ssh www-data@bastion.wp-api-demo-production.eu-west-1.aws.hmn.md","domains":["wp-api.org","*.wp-api.org"],"_links":{"self":[{"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production"}],"collection":[{"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications"}],"https:\/\/hm-stack.hm\/web-server":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/web-servers"}],"https:\/\/hm-stack.hm\/elasticsearch-cluster":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/elasticsearch-cluster"}],"https:\/\/hm-stack.hm\/database-server":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/database-server"}],"https:\/\/hm-stack.hm\/load-balancer":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/load-balancer"}],"https:\/\/hm-stack.hm\/deploy":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/deploys"}],"https:\/\/hm-stack.hm\/php-log":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/logs\/php"}],"https:\/\/hm-stack.hm\/nginx-log":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/logs\/nginx"}],"https:\/\/hm-stack.hm\/pull-request":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/pull-requests"}],"https:\/\/hm-stack.hm\/backup":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/backups"}],"https:\/\/hm-stack.hm\/alarm":[{"embeddable":true,"href":"https:\/\/eu-west-1.aws.hmn.md\/api\/stack\/applications\/wp-api-demo-production\/alarms"}]}}';

		case 'alarms':
			return '[]';

		case 'pull-requests':
			return '[{"id":161234510,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/281","title":"[WIP] #125\/#127 Events","number":281,"user":{"name":"sambulance","avatar_urls":{"96":"https:\/\/avatars3.githubusercontent.com\/u\/5014023?v=4"}},"date":"2018-01-04T22:45:46Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/281","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/281.diff","status":"PENDING"},{"id":161018982,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/275","title":"[WIP] Newsroom Theme patterns","number":275,"user":{"name":"alexcavender","avatar_urls":{"96":"https:\/\/avatars0.githubusercontent.com\/u\/5915265?v=4"}},"date":"2018-01-04T23:29:18Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/275","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/275.diff","status":"PENDING"},{"id":160922533,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/272","title":"[WIP] #125 Mosaic","number":272,"user":{"name":"sambulance","avatar_urls":{"96":"https:\/\/avatars3.githubusercontent.com\/u\/5014023?v=4"}},"date":"2018-01-03T14:31:42Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/272","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/272.diff","status":"PENDING"},{"id":159561415,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/215","title":"#185: [WIP] Search Results","number":215,"user":{"name":"tdlm","avatar_urls":{"96":"https:\/\/avatars1.githubusercontent.com\/u\/424090?v=4"}},"date":"2018-01-04T20:41:36Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/215","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/215.diff","status":"CHANGES_REQUESTED"},{"id":158464741,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/200","title":"61 - API Newswire release ingestion","number":200,"user":{"name":"tcrsavage","avatar_urls":{"96":"https:\/\/avatars0.githubusercontent.com\/u\/907521?v=4"}},"date":"2017-12-21T09:32:03Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/200","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/200.diff","status":"CHANGES_REQUESTED"},{"id":157991926,"url":"https:\/\/github.com\/humanmade\/encompass\/pull\/187","title":"[WIP] Global navigation pattern","number":187,"user":{"name":"joemcgill","avatar_urls":{"96":"https:\/\/avatars1.githubusercontent.com\/u\/801097?v=4"}},"date":"2018-01-02T08:18:37Z","link":"https:\/\/github.com\/humanmade\/encompass\/pull\/187","diff_url":"https:\/\/github.com\/humanmade\/encompass\/pull\/187.diff","status":"COMMENTED"}]';

		case 'environment':
		case 'page_generation':
		default :
			return false;
	}
}
