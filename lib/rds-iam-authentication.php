<?php

namespace HM\Platform\RDS_IAM_Authentication;

use Aws\Credentials\CredentialProvider;
use Aws\Rds\AuthTokenGenerator;
use Aws\Credentials\Credentials;

function get_db_password( $db_host, $db_port, $db_username, $region ) {
	if ( defined( 'AWS_RDS_IAM_KEY' ) && AWS_RDS_IAM_KEY ) {
		$provider = new Credentials( AWS_RDS_IAM_KEY, AWS_RDS_IAM_SECRET );
	} else {
		$provider = CredentialProvider::defaultProvider();
	}
	$generator = new AuthTokenGenerator( $provider );
	$token = $generator->createToken( $db_host . ':' . $db_port, $region, $db_user );
	return $token;
}
