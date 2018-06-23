## Changelog

### 2.0.0
- `hm.json` configuration support
    - New config handler, backwards compatible with `$hm_platform` global
    - New plugin loader
    - New plugin manifest file, defines names, default enabled value, file to load, and optional loader function
    - New settings file, certain plugins can be configured via `hm.json`
	- Ensure Batcache is loaded after cache providers
- Enterprise Kit
    - Added Bylines
    - Added CMB2
    - Added ElasticPress
    - Added Extended CPTs
    - Added Gutenberg
    - Added HM GTM
    - Added Platform UI
    - Added HM Redirects
    - Added HM Related Posts
    - Added HM Stack
    - Added Media Explorer
    - Added MSM Sitemap
    - Added Performance plugin (platform wide defaults eg. hiding custom fields metabox)
    - Added Polylang
    - Added Publishing Checklist
    - Added Query Monitor
    - Added WordPress SEO & bootstrap code
    - Added Workflows

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
