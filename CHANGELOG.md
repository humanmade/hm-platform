## Changelog

### HEAD

### 1.4.8

- Upgrade AWS SDK 3.101.1 -> 3.150.0 #186

### 1.4.7

- Update S3 Uploads to version 2.2.2 #182

### 1.4.6

- Update Aws X-Ray plugin to v1.2.11 (#181)

### 1.4.5

- Update AWS Xray to 1.2.11
- Update hm-require-login to version 1.0.1

### 1.4.4

- Update AWS X-Ray plugin to 1.2.7 #175, #176, #177

### 1.4.3

- Set dbname when a writer endpoint is missing.
- Update S3 Uploads plugin to version 2.1.0.
- Update AWS X-Ray plugin to 1.2.2.

### 1.4.2
- Upgrade AWS SDK to version 3.101.1

### 1.4.1
- Enable Batcache caching on logged-out `admin-ajax.php` GET requests.

### 1.4.0
- Send fatal errors to CloudWatch on ECS apps.
- X-Ray enabled by default on all infrastructure types.

### 1.3.0
- When using Redis, clear the alloptions cache whenever an option is added/updated/deleted.
- Add new db.php to support multi mysql server via `DB_READ_REPLICA_HOST`
- Add new PHP Error Handler to send PHP logs to CloudWatch in ECS infrastructure
- Update AWS SDK to 3.73.0
- X-Ray is not on by default on the ECS infrastructure
- Don't run Elasticsearch Healthcheck test when it's not enabled
- Disable Redis' failback flush
- Fix undefined variable in `inc/class-db.php`
- Update XRay plugin to 1.1.0. Enables catching of fatal errors.
- XRay `SELECT` Queries with leading whitespace cause Trace ID to be added
- Update wp-redis-predis-client version with support for persistent connections

### 1.2.23
- Fix healthcheck status code
- Add Cavalcade / Cron healthcheck
- Added require-login feature and plugin

### 1.2.22
- Update Elasticsearch request signing to support ECS

### 1.2.21
- Update Cavalcade
    - Updates the Cavalcade documentation
    - Adds support for the WP Cron API's named schedules.
    - Includes the interval when generating keys for comparing old and new values of the cron array so events with a changed recurrence are re-saved.

### 1.2.20
- Use Redis by default on ECS architecture
- Update XRay plugin to 1.0.3
	- Use local declaration of wp_debug_backtrace_summary.

### 1.2.19
- Increase execution timeout for async-upload.php.

### 1.2.18
- Update XRay plugin to 1.0.2
  - Track Remote Requests made via the WordPress HTTP API.
- Update S3 Uploads to latest
  - Fix warning when using local streamwrapper

### 1.2.17
- Update Tachyon plugin to v0.9.2
    - Latest version allows for enabling Tachyon in the WP Admin

### 1.2.16
- Log errors from ElasticPress communication with Elasticsearch
- Don't fallback to MySQL search when Elasticsearch reqeusts fail

### 1.2.15
- Update S3 Uploads to latest
  - Fixes wp_tempnam not being defined

### 1.2.14
- Change AWS Xray submodule to `https` instead of `git` protocol.
  - Fix occassional provisioning error due to SSH's `known_hosts` not accepting the Github public key.

### 1.2.13
- Fix a PHP Warning in the plugins list table.
- Update memcached to latest:
    - Ensure cache servers keys and values match up when using multiple nodes.
- Disable XHProf for Cavalcade-Runner.
- Add composer.json for Composer compatibility.
- Update Batcache to latest:
    - Fix version check logic for cached documents.

### 1.2.12
- Update XRay to 1.0.1
    - Only add Trace ID to non-SELECT MySQL queries

### 1.2.11
- Update S3 Uploads
    - Fix for processing images in PDFs

### 1.2.10
- Include DB dropin for Xray
- Update S3 Uploads
    - Includes getID3 library fixes

### 1.2.9
- Update AWS Xray plugin to 1.0.0
    - Split up segments into chunks to avoid socket_sento error.

### 1.2.8
- Add `/healthcheck/` endpoint

### 1.2.7
- Disable search engine indexing by default on non-production environments
    - This feature can be disabled by setting `HM_DISABLE_INDEXING` to `false`


### 1.2.6
- Update AWS SES plugin to 0.1.1
    - Fix escaping in From email address

### 1.2.5

- Update Tachyon
    - Fix bug with srcset and custom gravity

### 1.2.4

- Update Tachyon
    - Add support for gravity / crop positions on custom image sizes

### 1.2.3

- Update S3 Uploads
	- Fix deleting original attachments.
	- Improve memory usage when copying large files.

### 1.2.2

- Update batcache
	- Merge with upstream
	- Update deprecated constructor
- Update luidicrousdb
	- Fixes compatibilty with 4.6
	- Updates to `query()` method
	- Major versioh bump to 4.x
	- Fixes WordPress 4.8.3 SQLi vulnerability
- Update wordpress-pecel-memcached-object-cache
	- Reintroduce warning with `trigger_error()` call
	- Ensure `alloptionskeys` doesn't return false from cache
- Update S3 Uploads
	- Added ability to disable rewriting of file upload url
	- Added ability to set S3 object permissions
	- Ignore copy of a file path if it is a directory
- Update Tachyon
	- Add hook to be able to save image dimensions

### 1.2.1

- Update AWS SES plugin to latest version
    - Adds a filter to get more logging details
- Send logs from SES and Cavalcade to CloudWatch
- Add ElasticSearch support and plugin

### 1.2.0

- Include common AWS SDK to avoid potential loading issues
- Update wordpress-pecel-memcached-object-cache
	- Avoid error with clean cache
	- Return correct value from `wp_load_alloptions()`
	- Avoid undefined variable error
- Update aws-ses-wp-mail
	- Update AWS SDK to v3
	- Avoid warning when domain is not set
	- Avoid warning with `trigger_error()` call
- Update S3 Uploads
	- Avoid loading error with multiple AWS SDK instances

### 1.1.3

- Update S3 Uploads to 2.0.0-beta3 which fixed an issue with regenerating thumbs.


### 1.1.2

- Update Cavalcade to include fix for failed jobs never recovering.
