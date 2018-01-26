<?php
/**
 * The configuration definition for HM Platform.
 *
 * @package hm-platform
 */

namespace HM\Platform\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package HM\Platform\Config
 */
class Configuration implements ConfigurationInterface {
	public function getConfigTreeBuilder() {
		$tree_builder = new TreeBuilder();
		$root_node = $tree_builder->root( 'plugins' );
		$root_node
			->children()
				->arrayNode( 'infrastructure' )
					->children()
						->booleanNode( 'aws_ses_wp_mail' )
							->defaultTrue()
						->end()
						->booleanNode( 'batcache' )
							->defaultTrue()
						->end()
						->booleanNode( 'elasticpress' )
							->defaultFalse()
						->end()
						->booleanNode( 'ludicrousdb' )
							->defaultTrue()
						->end()
						->booleanNode( 'memcached' )
							->defaultTrue()
						->end()
					->end()
				->end()
				->arrayNode( 'plugins' )
					->children()
						->booleanNode( 'bylines' )
							->defaultFalse()
						->end()
						->booleanNode( 'cavalcade' )
							->defaultTrue()
						->end()
						->booleanNode( 'hm-stack' )
							->defaultFalse()
						->end()
						->booleanNode( 'performance' )
							->defaultTrue()
						->end()
						->booleanNode( 'redirects' )
							->defaultFalse()
						->end()
						->booleanNode( 'related_posts' )
							->defaultFalse()
						->end()
						->booleanNode( 'seo' )
							->defaultFalse()
						->end()
						->booleanNode( 's3_uploads' )
							->defaultTrue()
						->end()
						->booleanNode( 'sitemaps' )
							->defaultFalse()
						->end()
						->booleanNode( 'tachyon' )
							->defaultTrue()
						->end()
					->end()
				->end()
			->end()
		;

		return $tree_builder;
	}
}
