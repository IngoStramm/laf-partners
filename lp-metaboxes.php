<?php

add_action( 'cmb2_admin_init', 'lp_register_metabox' );

function lp_register_metabox() {
	$prefix = 'lp_projects_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Opções Extras', 'cmb2' ),
		'object_types'  => array( 'projects' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
	) );

	$cmb_demo->add_field( array(
		'name'       => esc_html__( 'Cliente', 'lp' ),
		'desc'       => esc_html__( 'Selecione o cliente a qual este projeto pertence', 'lp' ),
		'id'         => $prefix . 'clientes',
		'type'       => 'select',
		'show_option_none' => true,
		'required'		=> true,
		'options'          => 'lp_get_clientes',
	) );

	$cmb_demo->add_field( array(
		'name'       => esc_html__( 'Arquivos', 'lp' ),
		'desc'       => esc_html__( 'Adicione os arquivos do projeto', 'lp' ),
		'id'         => $prefix . 'files',
		'type'         => 'file_list',
		'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		'show_option_none' => true,
	) );

	$cmb_demo->add_field( array(
		'name'       => esc_html__( 'Links', 'lp' ),
		'desc'       => esc_html__( 'Links para o cliente acessar', 'lp' ),
		'id'         => $prefix . 'urls',
		'type'       => 'text_url',
		'repeatable'       => true,
		'attributes' => array(
			'placeholder'	=> 'https://'
		)
	) );

}

add_action( 'cmb2_admin_init', 'lp_register_user_profile_metabox' );
/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function lp_register_user_profile_metabox() {
	$prefix = 'lp_user_';

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => esc_html__( 'Integração com Planilha do Google', 'lp' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
	) );

	$cmb_user->add_field( array(
		'name'     => esc_html__( 'Integração com Planilha do Google', 'lp' ),
		'id'       => $prefix . 'title',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Url da Planilha "Atendimento | Geral"', 'lp' ),
		'desc' => esc_html__( 'Publicado para Web - valores separados por vírgulas (.csv)', 'lp' ),
		'id'   => $prefix . 'google_sheet_url_atendimento_geral',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'ID da página "Dashboard", da Planilha "Atendimento | Geral"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_id_atendimento_geral_dashboard',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Número da linha do "Contrato/Tempo", na página "Dashboard", da Planilha "Atendimento | Geral"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_linha_contrato_tempo_atendimento_geral_dashboard',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Número da linha do "Contrato/Jobs Entregues", na página "Dashboard", da Planilha "Atendimento | Geral"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_linha_contrato_jobs_entregues_atendimento_geral_dashboard',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Número da linha do "Meta a ser batida", na página "Dashboard", da Planilha "Atendimento | Geral"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_linha_meta_a_ser_batida_atendimento_geral_dashboard',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'ID da página do Cliente, da Planilha "Atendimento | Geral"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_id_atendimento_geral_cliente',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Url da Planilha "Leads - LAF 2020 (Partner)"', 'lp' ),
		'desc' => esc_html__( 'Publicado para Web - valores separados por vírgulas (.csv)', 'lp' ),
		'id'   => $prefix . 'google_sheet_url_leads',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'ID da página de Cards, da planilha "Leads - LAF 2020 (Partner)"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_id_leads_cards',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Url da Planilha "Partners - Metricas Funil"', 'lp' ),
		'desc' => esc_html__( 'Publicado para Web - valores separados por vírgulas (.csv)', 'lp' ),
		'id'   => $prefix . 'google_sheet_url_funil',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'ID da página do Cliente, da planilha "Partners - Metricas Funil"', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_sheet_id_funil_cliente',
		'type' => 'text',
		'attributes'	=> array(
			'type'		=> 'number',
			'pattern'	=> '\d*',
		)
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'ID do shortcode do Ninja Tables', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'tn_shortcode_id',
		'type' => 'text',
		'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
	        'escape_cb'       => 'absint',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Url do Google Report', 'lp' ),
		// 'desc' => esc_html__( 'field description (optional)', 'lp' ),
		'id'   => $prefix . 'google_report_url',
		'type' => 'text_url',
		'attributes' => array(
			'placeholder' => 'https://'
		)
	) );

}

// add_action( 'cmb2_admin_init', 'lp_register_google_sheet_api' );
/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function lp_register_google_sheet_api() {

	/**
	 * Registers options page menu item and form.
	 */
	$cmb_options = new_cmb2_box( array(
		'id'           => 'lp_settings',
		'title'        => esc_html__( 'Integração Planilhas Google', 'lp' ),
		'object_types' => array( 'options-page' ),

		/*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

		'option_key'      => 'lp_settings', // The option key and admin menu page slug.
		'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
		// 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
		'parent_slug'     => 'options-general.php', // Make options page a submenu item of the themes menu.
		// 'capability'      => 'manage_options', // Cap required to view options-page.
		// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
		// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
		// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
		// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
		// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
		// 'message_cb'      => 'yourprefix_options_page_message_callback',
		// 'tab_group'       => '', // Tab-group identifier, enables options page tab navigation.
		// 'tab_title'       => null, // Falls back to 'title' (above).
		// 'autoload'        => false, // Defaults to true, the options-page option will be autloaded.
	) );

	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$cmb_options->add_field( array(
		'name'    => esc_html__( 'API Key da Planilha do Google', 'lp' ),
		'id'      => 'google_sheet_api_key',
		'type'    => 'text',
	) );

}

function lp_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( 'lp_settings', $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'lp_settings', $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

