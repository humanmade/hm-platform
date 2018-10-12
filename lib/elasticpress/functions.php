<?php
/**
 * Smart defaults and helpers for ElasticPress.
 */

namespace HM\Platform\ElasticPress;

add_filter( 'ep_analyzer_language', __NAMESPACE__ . '\ep_analyzer_language', 10, 2 );

/**
 * Return the correct analyzer language based on the sites configured language code.
 *
 * @author Mike Little
 *
 * @param string $language THe current language.
 * @param string $filter   The specific filter.
 *
 * @return string The language name to use.
 */
function ep_analyzer_language( string $language, string $filter ) : string {

	// all the languages supported by V 5.3 of elastic search
	$supported_languages = [
		'ar'             => 'arabic',
		'hy'             => 'armenian',
		'eu'             => 'basque',
		'pt_br'          => 'brazilian',
		'bg_bg'          => 'bulgarian',
		'ca'             => 'catalan',
		'cs_cz'          => 'czech',
		'da_dk'          => 'danish',
		'nl_be'          => 'dutch',
		'nl_nl'          => 'dutch',
		'nl_nl_formal'   => 'dutch',
		'en_au'          => 'english',
		'en_ca'          => 'english',
		'en_gb'          => 'english',
		'en_nz'          => 'english',
		'en_us'          => 'english',
		'en_za'          => 'english',
		'fi'             => 'finnish',
		'fr_be'          => 'french',
		'fr_ca'          => 'french',
		'fr_fr'          => 'french',
		'gl_es'          => 'galician',
		'de_ch'          => 'german',
		'de_ch_informal' => 'german',
		'de_de'          => 'german',
		'de_de_formal'   => 'german',
		'el'             => 'greek',
		'hi_in'          => 'hindi',
		'hu_hu'          => 'hungarian',
		'id_id'          => 'indonesian',
		'it_it'          => 'italian',
		'lv'             => 'latvian',
		'lt_lt'          => 'lithuanian',
		'nb_no'          => 'norwegian',
		'nn_no'          => 'norwegian',
		'fa_ir'          => 'persian',
		'pt_pt'          => 'portuguese',
		'pt_pt_ao90'     => 'portuguese',
		'ro_ro'          => 'romanian',
		'ru_ru'          => 'russian',
		'ckb'            => 'sorani',
		'es_ar'          => 'spanish',
		'es_cl'          => 'spanish',
		'es_co'          => 'spanish',
		'es_cr'          => 'spanish',
		'es_es'          => 'spanish',
		'es_gt'          => 'spanish',
		'es_mx'          => 'spanish',
		'es_pe'          => 'spanish',
		'es_ve'          => 'spanish',
		'sv_se'          => 'swedish',
		'tr_tr'          => 'turkish',
		'th'             => 'thai',
		'zh_cn'          => 'cjk', // chinese (china)
		'zh_hk'          => 'cjk', // chinese (hong kong)
		'zh_tw'          => 'cjk', // chinese (taiwan)
		'ja'             => 'cjk', // japanese
		'ko_kr'          => 'cjk', // korean
	];

	$lang_code = get_option( 'WPLANG' );
	$lang_code = strtolower( $lang_code );
	if ( isset( $supported_languages[ $lang_code ] ) ) {

		return $supported_languages[ $lang_code ];
	}

	return $language;
}
