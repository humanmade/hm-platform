<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Help_Center
 */
class WPSEO_Help_Center {
	/**
	 * The tabs in the help center.
	 *
	 * @var WPSEO_Option_Tab[] $tab
	 */
	private $tabs;

	/**
	 * Mount point in the HTML.
	 *
	 * @var string
	 */
	private $identifier = 'yoast-help-center-container';

	/**
	 * Additional help center items.
	 *
	 * @var array
	 */
	protected $help_center_items = array();

	/**
	 * Show premium support tab.
	 *
	 * @var bool
	 */
	protected $premium_support;

	/**
	 * WPSEO_Help_Center constructor.
	 *
	 * @param string                             $unused          Backwards compatible argument.
	 * @param WPSEO_Option_Tabs|WPSEO_Option_Tab $option_tabs     Currently displayed tabs.
	 * @param boolean                            $premium_support Show premium support tab.
	 */
	public function __construct( $unused, $option_tabs, $premium_support = false ) {
		$this->premium_support = false;

		$tabs = new WPSEO_Option_Tabs( '' );

		if ( $option_tabs instanceof WPSEO_Option_Tabs ) {
			$tabs = $option_tabs;
		}

		if ( $option_tabs instanceof WPSEO_Option_Tab ) {
			$tabs = new WPSEO_Option_Tabs( '', $option_tabs->get_name() );
			$tabs->add_tab( $option_tabs );
		}

		$this->tabs = $tabs;
	}

	/**
	 * Localize data required by the help center component.
	 */
	public function localize_data() {
		$this->enqueue_localized_data( $this->format_data( $this->tabs->get_tabs() ) );
	}

	/**
	 * Format the required data for localized script.
	 *
	 * @param WPSEO_Option_Tab[] $tabs Yoast admin pages navigational tabs.
	 *
	 * @return array Associative array containing data for help center component.
	 */
	protected function format_data( array $tabs ) {
		$formatted_data = array( 'tabs' => array() );

		foreach ( $tabs as $tab ) {
			$formatted_data['tabs'][ $tab->get_name() ] = array(
				'label'    => $tab->get_label(),
				'videoUrl' => $tab->get_video_url(),
				'id'       => $tab->get_name(),
			);
		}

		$active_tab = $this->tabs->get_active_tab();
		$active_tab = ( null === $active_tab ) ? $tabs[0] : $active_tab;

		$formatted_data['mountId']    = $this->identifier;
		$formatted_data['initialTab'] = $active_tab->get_name();

		$is_premium = WPSEO_Utils::is_yoast_seo_premium();

		// Will translate to either empty string or "1" in localised script.
		$formatted_data['isPremium']     = $is_premium;
		$formatted_data['pluginVersion'] = WPSEO_VERSION;

		// Open HelpScout on activating this tab ID.
		$formatted_data['shouldDisplayContactForm'] = $this->premium_support;

		$formatted_data['translations'] = self::get_translated_texts();

		$formatted_data['videoDescriptions'] = array();

		$formatted_data['contactSupportParagraphs'] = array();

		$formatted_data['extraTabs'] = $this->get_extra_tabs();

		return $formatted_data;
	}

	/**
	 * Get additional tabs for the help center component.
	 *
	 * @return array Additional help center tabs.
	 */
	protected function get_extra_tabs() {
		$help_center_items = apply_filters( 'wpseo_help_center_items', $this->help_center_items );

		return array_map( array( $this, 'format_helpcenter_tab' ), $help_center_items );
	}

	/**
	 * Convert WPSEO_Help_Center_Item into help center format.
	 *
	 * @param WPSEO_Help_Center_Item $item The item to convert.
	 *
	 * @return array Formatted item.
	 */
	protected function format_helpcenter_tab( WPSEO_Help_Center_Item $item ) {
		return array(
			'identifier' => $item->get_identifier(),
			'label'      => $item->get_label(),
			'content'    => $item->get_content(),
		);
	}

	/**
	 * Enqueue localized script for help center component.
	 *
	 * @param array $data Data to localize.
	 */
	protected function enqueue_localized_data( $data ) {
		wp_localize_script( WPSEO_Admin_Asset_Manager::PREFIX . 'help-center', 'wpseoHelpCenterData', $data );
	}

	/**
	 * Outputs the help center div.
	 */
	public function mount() {
		echo '<div id="' . esc_attr( $this->identifier ) . '">' . esc_html__( 'Loading help center.', 'wordpress-seo' ) . '</div>';
	}

	/**
	 * Pass text variables to js for the help center JS module.
	 *
	 * %s is replaced with <code>%s</code> and replaced again in the javascript with the actual variable.
	 *
	 * @return  array Translated text strings for the help center.
	 */
	public static function get_translated_texts() {
		// Esc_html is not needed because React already handles HTML in the (translations of) these strings.
		return array(
			'locale'                             => WPSEO_Utils::get_user_locale(),
			'videoTutorial'                      => __( 'Video tutorial', 'wordpress-seo' ),
			'knowledgeBase'                      => __( 'Knowledge base', 'wordpress-seo' ),
			'getSupport'                         => __( 'Get support', 'wordpress-seo' ),
			'algoliaSearcher.loadingPlaceholder' => __( 'Loading...', 'wordpress-seo' ),
			'algoliaSearcher.errorMessage'       => __( 'Something went wrong. Please try again later.', 'wordpress-seo' ),
			'searchBar.headingText'              => __( 'Search the Yoast Knowledge Base for answers to your questions:', 'wordpress-seo' ),
			'searchBar.placeholderText'          => __( 'Type here to search...', 'wordpress-seo' ),
			'searchBar.buttonText'               => __( 'Search', 'wordpress-seo' ),
			'searchResultDetail.openButton'      => __( 'View in KB', 'wordpress-seo' ),
			'searchResultDetail.openButtonLabel' => __( 'Open the knowledge base article in a new window or read it in the iframe below', 'wordpress-seo' ),
			'searchResultDetail.backButton'      => __( 'Go back', 'wordpress-seo' ),
			'searchResultDetail.backButtonLabel' => __( 'Go back to the search results', 'wordpress-seo' ),
			'searchResultDetail.iframeTitle'     => __( 'Knowledge base article', 'wordpress-seo' ),
			'searchResultDetail.searchResult'    => __( 'Search result', 'wordpress-seo' ),
			'searchResult.noResultsText'         => __( 'No results found.', 'wordpress-seo' ),
			'searchResult.foundResultsText'      => sprintf(
				/* translators: %s expands to the number of results found . */
				__( 'Number of results found: %s', 'wordpress-seo' ),
				'{ resultsCount }'
			),
			'searchResult.searchResultsHeading'  => __( 'Search results', 'wordpress-seo' ),
			'a11yNotice.opensInNewTab'           => __( '(Opens in a new browser tab)', 'wordpress-seo' ),
			'contactSupport.button'              => __( 'New support request', 'wordpress-seo' ),
			'helpCenter.buttonText'              => __( 'Need help?', 'wordpress-seo' ),
		);
	}

	/**
	 * Outputs the help center.
	 *
	 * @deprecated 5.6
	 */
	public function output_help_center() {
		_deprecated_function( 'WPSEO_Help_Center::output_help_center', 'WPSEO 5.6.0', 'WPSEO_Help_Center::mount()' );
		$this->mount();
	}
}
