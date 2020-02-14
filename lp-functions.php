<?php

// Planilhas
// https://docs.google.com/spreadsheets/d/1WPRCNkRhXO0bsRotbBCxB1OQPoYspcsrR6Hg2ruePN0/edit?ts=5dc044db#gid=0
// https://docs.google.com/spreadsheets/d/1-gB5jD4o3tMZvuyKm9CE5PqYpp2JceZV1xxqvMNX654/edit#gid=56862676
// https://docs.google.com/spreadsheets/d/1cv9Jqoj32QtwE2RtXl7jiN7K2iOaSn3uegARoG0X4h4/edit#gid=465598149

// Debug

function lp_debug( $debug ) {
	echo '<pre>';
	var_dump( $debug );
	echo '</pre>';
}

function lp_return_horas_tecnicas_status_color( $status = null ) {
	$colors = array(
		'x' => '#22cabf',
		'-' => '#fdb94b',
		'!' => '#ee5744',
	);
	return ( empty( $status ) || is_null( $status ) ) ? null : $colors[ $status ];
}


function lp_return_horas_tecnicas_status_icon( $status = null ) {
	$icons = array(
		'x' => 'icon-check',
		'-' => 'icon-dot3',
		'!' => 'icon-cancel-1',
	);
	// lp_debug( $icons[ $status ] );
	return ( empty( $status ) || is_null( $status ) ) ? null : $icons[ $status ];
}


// Força o usuário a estar logado

function lp_get_url() {
	$url  = isset( $_SERVER[ 'HTTPS' ] ) && 'on' === $_SERVER[ 'HTTPS' ] ? 'https' : 'http';
	$url .= '://' . $_SERVER[ 'SERVER_NAME' ];
	$url .= in_array( $_SERVER[ 'SERVER_PORT' ], array( '80', '443' ) ) ? '' : ':' . $_SERVER[ 'SERVER_PORT' ];
	$url .= $_SERVER[ 'REQUEST_URI' ];
	return $url;
}

add_action( 'init', 'lp_forcelogin' );

function lp_forcelogin() {
	if( !is_user_logged_in() ) {
		$url = lp_get_url();
		$whitelist = apply_filters( 'lp_forcelogin_whitelist', array() );
		$redirect_url = apply_filters( 'lp_forcelogin_redirect', $url );
		if( preg_replace( '/\?.*/', '', $url ) != preg_replace( '/\?.*/', '', wp_login_url() ) && !in_array( $url, $whitelist ) ) {
			wp_safe_redirect( wp_login_url( $redirect_url ), 302 ); exit();
		}
	}
}

// Retorna Lista de usuários

function lp_get_clientes() {
	$clientes_obj = get_users( array( 'role' => 'subscriber' ) );
	$clientes_arr = [];
	foreach ( $clientes_obj as $cliente ) :
		
		$clientes_arr[ $cliente->ID ] = $cliente->user_nicename;

	endforeach;
	
	// lp_debug( $clientes_arr );
	return $clientes_arr;
}

// Exibe os Metaboxes no frontend

add_filter( 'the_content', 'lp_show_cb_projects' );

function lp_show_cb_projects( $content ) {
	$post_id = get_the_ID();
	$post_type = get_post_type( $post_id );

	if( $post_type != 'projects' || is_admin() )
		return $content;

	if( !is_single() )
		return $content;

	$is_site_admin = in_array( 'administrator',  wp_get_current_user()->roles );
	$user_id = get_current_user_id();
	$cliente_id = get_post_meta( $post_id, 'lp_projects_clientes', true );

	if( $user_id != $cliente_id && !$is_site_admin )
		return __( 'Você não possui permissão para visualizar este projeto.', 'lp' );

	$files = get_post_meta( $post_id, 'lp_projects_files', true );
	$files_output = '';
	$files_total = count( $files );

	if( $files ) :
		$files_output .= '<ul class="sc_list sc_list_style_iconed">';
		$i = 0;
		foreach( $files as $file ):		
			$extra_class = '';
			$extra_class .= $i == 0 ? ' first' : '';
			$extra_class .= $i >= ( $files_total - 1 ) ? ' last' : '';
			$extra_class .= $i % 2 ? ' even' : ' odd';
			$files_output .= '<li class="sc_list_item' . $extra_class . '">';
			$files_output .= '<span class="sc_list_icon icon-doc"></span>';
			$files_output .= '<a href="' . $file . '" target="_blank">' . __( 'Baixar', 'lp' ) . '</a>';
			$files_output .= '</li>';
			$i++;
		endforeach;
		$files_output .= '</ul>';
	endif;

	$urls = get_post_meta( $post_id, 'lp_projects_urls', true );

	$urls_output = '';
	$urls_total = count( $urls );

	if( $urls ) :
		$urls_output .= '<ul class="sc_list sc_list_style_iconed">';
		$i = 0;
		foreach( $urls as $file ):		
			$extra_class = '';
			$extra_class .= $i == 0 ? ' first' : '';
			$extra_class .= $i >= ( $urls_total - 1 ) ? ' last' : '';
			$extra_class .= $i % 2 ? ' even' : ' odd';
			$urls_output .= '<li class="sc_list_item' . $extra_class . '">';
			$urls_output .= '<span class="sc_list_icon icon-link"></span>';
			$urls_output .= '<a href="' . $file . '" target="_blank">' . __( 'Acessar', 'lp' ) . '</a>';
			$urls_output .= '</li>';
			$i++;
		endforeach;
		$urls_output .= '</ul>';
	endif;

	if( $files || $urls )
		$content .= do_shortcode( '[trx_columns count="2" margins="" class="columns_lists" top="small" bottom="null"][trx_column_item]' . $files_output . '[/trx_column_item][trx_column_item]' . $urls_output . '[/trx_column_item][/trx_columns]' );

	return $content ;
}

add_action( 'moc_projects_buttons', 'lp_show_projects_buttons' );

function lp_show_projects_buttons( $post_id ) {

	$content = '';
	$post_type = get_post_type( $post_id );

	if( $post_type != 'projects' || is_admin() )
		return;

	$is_site_admin = in_array( 'administrator',  wp_get_current_user()->roles );
	$user_id = get_current_user_id();
	$cliente_id = get_post_meta( $post_id, 'lp_projects_clientes', true );

	if( $user_id != $cliente_id && !$is_site_admin ) :
		_e( 'Você não possui permissão para visualizar este projeto.', 'lp' );
		return;
	endif;

	$urls = get_post_meta( $post_id, 'lp_projects_urls', true );

	$urls_output = '';
	$urls_total = count( $urls );

	if( $urls ) :
		$urls_output .= '<ul class="moc-list sc_list sc_list_style_iconed">';
		$i = 0;
		foreach( $urls as $file ):		
			$extra_class = '';
			$extra_class .= $i == 0 ? ' first' : '';
			$extra_class .= $i >= ( $urls_total - 1 ) ? ' last' : '';
			$extra_class .= $i % 2 ? ' even' : ' odd';
			$urls_output .= '<li class="moc-list-item sc_list_item' . $extra_class . '">';
			// $urls_output .= '<span class="sc_list_icon icon-link"></span>';
			$urls_output .= '<a href="' . $file . '" class="moc-list-btn moc-list-btn-url sc_button sc_button_square sc_button_style_filled sc_button_size_small" target="_blank">' . __( 'Doc', 'lp' ) . '</a>';
			$urls_output .= '</li>';
			$i++;
		endforeach;
		$urls_output .= '</ul>';
	endif;

	$files = get_post_meta( $post_id, 'lp_projects_files', true );
	$files_output = '';
	$files_total = count( $files );

	if( $files ) :
		$files_output .= '<ul class="moc-list sc_list sc_list_style_iconed">';
		$i = 0;
		foreach( $files as $file ):		
			$extra_class = '';
			$extra_class .= $i == 0 ? ' first' : '';
			$extra_class .= $i >= ( $files_total - 1 ) ? ' last' : '';
			$extra_class .= $i % 2 ? ' even' : ' odd';
			$files_output .= '<li class="moc-list-item sc_list_item' . $extra_class . '">';
			// $files_output .= '<span class="sc_list_icon icon-doc"></span>';
			$files_output .= '<a href="' . $file . '" class="moc-list-btn moc-list-btn-file sc_button sc_button_square sc_button_style_filled sc_button_size_small" target="_blank">' . __( 'Peças', 'lp' ) . '</a>';
			$files_output .= '</li>';
			$i++;
		endforeach;
		$files_output .= '</ul>';
	endif;

	if( $files || $urls )
		$content .= do_shortcode( '[trx_columns count="2" margins="" class="columns_lists" top="small" bottom="null"][trx_column_item]' . $files_output . '[/trx_column_item][trx_column_item]' . $urls_output . '[/trx_column_item][/trx_columns]' );

	echo $content;

}

// Filtra os projetos no frontend
add_action( 'pre_get_posts', 'lp_exibe_projetos', 9999 );

function lp_exibe_projetos( $query ) {
	$is_site_admin = in_array( 'administrator',  wp_get_current_user()->roles );
    
    if ( is_admin() || $is_site_admin )
    	return;

    // lp_debug( $query->is_tax );
    // lp_debug( $query->query_vars );

    $post_type = $query->query_vars[ 'post_type' ];

    if( $post_type == 'projects' || ( $query->is_tax && isset( $query->query_vars[ 'projects_group' ] ) ) ) :
    	$user_id = get_current_user_id();
    	$cliente_id = get_post_meta( $post_id, 'lp_projects_clientes', true );
        $query->set( 'meta_key',   'lp_projects_clientes' );
        $query->set( 'meta_value', $user_id );
        // return false;
    endif;
}

// Estilo da tela de login

add_action( 'login_enqueue_scripts', 'lp_login_style' );

function lp_login_style() { 
	?>

    <style type="text/css">
        body.login.login-action-login {
			background-image: url( <?php echo LP_URL; ?>images/bg_login.jpg ) !important;
			background-position: center left !important;
			background-color: #ffc700 !important;
        }

        body.login div#login h1 a {
        	/*background: url( <?php echo LP_URL; ?>images/admin-logo.png ) no-repeat center;*/
        	background: transparent;
			height: 74px;
			width: auto;
        }
        body.login #nav,
        body.login .forgetmenot,
        body.login .privacy-policy-page-link,
        body.login #backtoblog {
        	display: none;
        }
        body.login form#loginform {
        	overflow: visible;
        	padding-bottom: 40px;
        }
        body.login form input#wp-submit {
        	margin: 0;
        	padding: 15px 20px;
        	background: #000;
        	width: auto;
        	height: auto;
        	-webkit-border-radius: 10px;
        	-moz-border-radius: 10px;
        	border-radius: 10px;
        	font-weight: 200;
        	text-transform: capitalize;
        	line-height: 30px;
        }
        body.login form #wp-submit:hover {
			-webkit-transform: none !important;
			-moz-transform: none !important;
			-ms-transform: none !important;
			transform: none !important;
			background-color: #075f88;
        }
        body.login p:not(.forgetmenot) label,
        body.login form #user_pass.input[type=password],
        body.login input[type=text]#user_login {
        	font-size: 0;
			text-align: left;
			font-weight: 200px;
        }
        body.login form #user_pass.input[type=password],
        body.login input[type=text]#user_login {
			font-size: 26px;
			padding-top: 10px;
			padding-bottom: 10px;
			bottom: 0;
			border: 0;
        }
        .lp-login-msg {
        	position: absolute;
        	bottom: -20px;
        	left: 0;
        	color: #a07c00;
        	font-size: 19px;
        }
        @media ( max-width: 1135px ) {
			body.login.login-action-login {
				background-position: center center !important;
			}
			body.login form#loginform {
				position: relative;
			}
        }
    </style>
	
	<?php
}

function lp_get_sheet_progresso_value_shortcode( $atts ) {

	$user_id = get_current_user_id();

	if( !$user_id )
		return __( 'Necessário estar logado para exibir as informações.', 'lp' );

	$google_spreadsheet_url = get_user_meta( $user_id, 'lp_user_google_sheet_url_atendimento_geral', true );

	if( !$google_spreadsheet_url )
		return __( 'Url da planilha "Atendimento | Geral" obrigatório', 'lp' );

	$google_sheet_id_atendimento_geral_dashboard = get_user_meta( $user_id, 'lp_user_google_sheet_id_atendimento_geral_dashboard', true );

	if( !$google_sheet_id_atendimento_geral_dashboard )
		return __( 'ID da página "Dashboard" da planilha "Atendimento | Geral" obrigatório', 'lp' );

	$google_sheet_linha_contrato_tempo_atendimento_geral_dashboard = get_user_meta( $user_id, 'lp_user_google_sheet_linha_contrato_tempo_atendimento_geral_dashboard', true );

	if( !$google_sheet_linha_contrato_tempo_atendimento_geral_dashboard )
		return __( 'Número da linha do "Contrato/Tempo", na página "Dashboard" da planilha "Atendimento | Geral", obrigatório', 'lp' );

	$google_sheet_linha_contrato_jobs_entregues_atendimento_geral_dashboard = get_user_meta( $user_id, 'lp_user_google_sheet_linha_contrato_jobs_entregues_atendimento_geral_dashboard', true );

	if( !$google_sheet_linha_contrato_jobs_entregues_atendimento_geral_dashboard )
		return __( 'Número da linha do "Contrato/Jobs Entregues", na página "Dashboard" da planilha "Atendimento | Geral", obrigatório', 'lp' );

	$google_sheet_linha_meta_a_ser_batida_atendimento_geral_dashboard = get_user_meta( $user_id, 'lp_user_google_sheet_linha_meta_a_ser_batida_atendimento_geral_dashboard', true );

	if( !$google_sheet_linha_meta_a_ser_batida_atendimento_geral_dashboard )
		return __( 'Número da linha do "Meta a ser batida", na página "Dashboard" da planilha "Atendimento | Geral", obrigatório', 'lp' );

	$sheet_url_dashboard = $google_spreadsheet_url . '&gid=' . $google_sheet_id_atendimento_geral_dashboard;

	$rows = [];
	if( ( $handle = fopen( $sheet_url_dashboard, 'r' ) ) !== FALSE ) :
	    while( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) :
	        $num = count( $data );
	        	$rows[] = $data;
	    endwhile;
	    fclose( $handle );
	else :
		return __( 'Não foi possível carregar a planilha "Atendimento | Geral" obrigatório. Verifique se as configurações estão corretas.', 'lp' );
	endif;

	$setup_contrato_tempo_raw = $rows[ $google_sheet_linha_contrato_tempo_atendimento_geral_dashboard - 1 ][ 2 ];
	$setup_contrato_tempo_number = str_replace( '%',	'', $setup_contrato_tempo_raw );
	$setup_contrato_tempo_number = str_replace( ',',	'.', $setup_contrato_tempo_number );

	$setup_contrato_jobs_entregues_raw = $rows[ $google_sheet_linha_contrato_jobs_entregues_atendimento_geral_dashboard - 1 ][ 2 ];
	$setup_contrato_jobs_entregues_number = str_replace( '%',	'', $setup_contrato_jobs_entregues_raw );
	$setup_contrato_jobs_entregues_number = str_replace( ',',	'.', $setup_contrato_jobs_entregues_number );

	$setup_contrato_meta_a_ser_batida_raw = $rows[ $google_sheet_linha_meta_a_ser_batida_atendimento_geral_dashboard - 1 ][ 2 ];
	$setup_contrato_meta_a_ser_batida_number = str_replace( '%',	'', $setup_contrato_meta_a_ser_batida_raw );
	$setup_contrato_meta_a_ser_batida_number = str_replace( ',',	'.', $setup_contrato_meta_a_ser_batida_number );

	$skills_driagram = '[trx_skills columns="3" color="#dd9933" title="Progresso Geral" bottom="40"]';

	$skills_driagram .= '[trx_skills_item title="' . __( 'Contrato / Tempo', 'lp' ) . '" value="' . $setup_contrato_tempo_number . '%" color="#ffb20e" style="1"][/trx_skills_item]';

	$skills_driagram .= '[trx_skills_item title="' . __( 'Contrato / Jobs Entregues', 'lp' ) . '" value="' . $setup_contrato_jobs_entregues_number . '%" color="#1ebeb4" style="1"][/trx_skills_item]';

	$skills_driagram .= '[trx_skills_item title="' . __( 'Meta a ser batida', 'lp' ) . '" value="' . $setup_contrato_meta_a_ser_batida_number . '%" color="#ee5744" style="1"][/trx_skills_item]';

	$skills_driagram .= '[/trx_skills]';

	return do_shortcode( $skills_driagram );
}

add_shortcode( 'lp_progresso', 'lp_get_sheet_progresso_value_shortcode' );

function lp_get_sheet_cronograma_geral_shortcode( $atts ) {

	$user_id = get_current_user_id();

	if( !$user_id )
		return __( 'Necessário estar logado para exibir as informações', 'lp' );

	$google_spreadsheet_url = get_user_meta( $user_id, 'lp_user_google_sheet_url_atendimento_geral', true );

	if( !$google_spreadsheet_url )
		return __( 'Url da planilha "Atendimento | Geral" obrigatório', 'lp' );

	$google_sheet_id_atendimento_geral_cliente = strval( get_user_meta( $user_id, 'lp_user_google_sheet_id_atendimento_geral_cliente', true ) );

	if( !$google_sheet_id_atendimento_geral_cliente && $google_sheet_id_atendimento_geral_cliente !== "0" )
		return __( 'ID da página "Dashboard" da planilha "Atendimento | Geral" obrigatório', 'lp' );

	$sheet_url_dashboard = $google_spreadsheet_url . '&gid=' . $google_sheet_id_atendimento_geral_cliente;

	$rows = [];
	if( ( $handle = fopen( $sheet_url_dashboard, 'r' ) ) !== FALSE ) :
	    while( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) :
	        array_shift( $data );
	        array_splice( $data, 3, 3 );
        	$rows[] = $data;
	    endwhile;
	    fclose( $handle );
	else :
		return __( 'Não foi possível carregar a planilha "Atendimento | Geral" obrigatório. Verifique se as configurações estão corretas.', 'lp' );
	endif;

	array_shift ( $rows );

	$total_cols = count( $rows[ 0 ] );
	$total_rows = count( $rows );

	$output = '';
	$output .= '[trx_table align="left"]
					<div style="overflow: auto;">
						<table summary="Cronograma Geral" cellspacing="0">
							<tbody>
								<tr>';
									$count_th = 0;
									foreach( $rows[ 0 ] as $th ) :
										if( $count_th < 4  ) :
											$output .= ( $count_th != 2 ) ? '<th rowspan="2" class="">' . $th . '</td>' : '<th>' . $th . '</td>';
										elseif( $count_th % 4  == 0 ) :
											// lp_debug( $count_th % 4 );
											$output .= '<th colspan="4">' . $th . '</th>';
											// $output .= '<td colspan="4">' . $th . '</td>';
										endif;
										$count_th++;
									endforeach;
		$output .=				'</tr>';

		// $count_tr = 1;

							$count_tr = 0;
							foreach( $rows as $tr ) :
								if( $count_tr > 0 ) :
									// lp_debug( $td );
			$output .= 				'<tr>';
									$count_td = 0;
									foreach( $tr as $td ) :
										if( $count_tr == 1 ) :
											$output .= ( $count_td != 0 && $count_td != 1 && $count_td != 3 ) ? '<td>' . $td . '</td>' : '';
										else:
											$output .= '<td>' . $td . '</td>';
										endif;
										$count_td++;
									endforeach;

			$output .= 				'</tr>';
									// $count_tr++;
								endif;
								$count_tr++;
							endforeach;
		$output .= 			'</tbody>
						</table>
					</div>
					[/trx_table]';

	return $output;

}

add_shortcode( 'lp_cronograma_geral', 'lp_get_sheet_cronograma_geral_shortcode' );

function lp_get_user_name_shortcode() {
	$current_user = wp_get_current_user();

	if ( !$current_user->exists() )
		return;

	return $current_user->display_name;

}

add_shortcode( 'lp_user_name', 'lp_get_user_name_shortcode' );

function lp_table_ninjas_shortcode( $atts ) {

	$current_user = wp_get_current_user();

	if ( !$current_user->exists() )
		return;

	$lp_user_tn_shortcode_id = get_user_meta( $current_user->ID, 'lp_user_tn_shortcode_id', true );
	return ( $lp_user_tn_shortcode_id && $lp_user_tn_shortcode_id > 0 ) ? do_shortcode( '[ninja_tables id="' . $lp_user_tn_shortcode_id . '"]' ) : __( 'Nenhum shortcode encontrado no perfil de usuário.', 'lp' );

}

add_shortcode( 'lp_tn', 'lp_table_ninjas_shortcode' );

function lp_get_sheet_leads_shortcode() {

	$user_id = get_current_user_id();

	if( !$user_id )
		return __( 'Necessário estar logado para exibir as informações', 'lp' );

	$google_spreadsheet_url_leads = get_user_meta( $user_id, 'lp_user_google_sheet_url_leads', true );

	if( !$google_spreadsheet_url_leads )
		return __( 'Url da planilha "Leads - LAF 2020 (Partner)" obrigatório', 'lp' );

	$google_sheet_id_leads_cards = strval( get_user_meta( $user_id, 'lp_user_google_sheet_id_leads_cards', true ) );

	if( !$google_sheet_id_leads_cards && $google_sheet_id_leads_cards !== "0" )
		return __( 'ID da página "Cards" da planilha "Leads - LAF 2020 (Partner)" obrigatório', 'lp' );

	$sheet_url_dashboard_leads = $google_spreadsheet_url_leads . '&gid=' . $google_sheet_id_leads_cards;

	$google_spreadsheet_url_funil = get_user_meta( $user_id, 'lp_user_google_sheet_url_funil', true );

	if( !$google_spreadsheet_url_funil )
		return __( 'Url da planilha "Leads - LAF 2020 (Partner)" obrigatório', 'lp' );

	$google_sheet_id_funil_cliente = strval( get_user_meta( $user_id, 'lp_user_google_sheet_id_funil_cliente', true ) );

	if( !$google_sheet_id_funil_cliente && $google_sheet_id_funil_cliente !== "0" )
		return __( 'ID da página "Cards" da planilha "Leads - LAF 2020 (Partner)" obrigatório', 'lp' );

	$sheet_url_dashboard_funil = $google_spreadsheet_url_funil . '&gid=' . $google_sheet_id_funil_cliente;

	$count_loop = 0;
	$total_cards = 0;
	$total_sales;
	$total_users;

	if( ( $handle = fopen( $sheet_url_dashboard_leads, 'r' ) ) !== FALSE ) :
	    while( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) :
	        if( $count_loop == 0 ) :
	        	$total_sales = $data[ 3 ];
	        	// $total_users = $data[ 8 ];
	        endif;

			if( $data[ 0 ] == 'Card #' )
        		$total_cards++;
    		
    		$count_loop++;
	    endwhile;
	    fclose( $handle );
	else :
		return __( 'Não foi possível carregar a planilha "Leads - LAF 2020 (Partner)". Verifique se as configurações estão corretas.', 'lp' );
	endif;

	$count_loop = 0;
	if( ( $handle = fopen( $sheet_url_dashboard_funil, 'r' ) ) !== FALSE ) :
	    while( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) :
	        if( $count_loop == 11 ) :
	        	$total_users = $data[ 0 ];
	        endif;

    		$count_loop++;
	    endwhile;
	    fclose( $handle );
	else :
		return __( 'Não foi possível carregar a planilha "Partners - Metricas Funil". Verifique se as configurações estão corretas.', 'lp' );
	endif;


	if( !empty( $total_sales ) && !$total_users && !$total_cards )
		return __( 'Não foi possível gerar os dados', 'lp' );
		// lp_debug( $total_sales );
		// lp_debug( $total_users );
		// lp_debug( $total_cards );

	$shortcode_style = 'style="color: #00b6e9; text-align: center;"';

	$shortcode = '[trx_infobox style="regular" closeable="no" color="#00B6E9" bg_color="#F4F4F4" top="0"]
						[trx_icon icon="icon-rocket" color="#FFB23C" font_size="50" align="center"]';

		if( $total_users ) :
			$shortcode .= '
				<h4 ' . $shortcode_style . '>' . __( 'Usuários', 'lp' ) . '</h4>
				<h1 ' . $shortcode_style . '>' . $total_users . '</h1>
				<h4 ' . $shortcode_style . '>..............</h4>';
		endif;

		if( $total_cards ) :
			$shortcode .= '
				<h4 ' . $shortcode_style . '>' . __( 'Leads', 'lp' ) . '</h4>
				<h1 ' . $shortcode_style . '>' . $total_cards . '</h1>
				<h4 ' . $shortcode_style . '>..........</h4>';
		endif;

		if( !empty( $total_sales ) ) :
			$shortcode .= '
				<h4 ' . $shortcode_style . '>' . __( 'Vendas', 'lp' ) . '</h4>
				<h1 ' . $shortcode_style . '>R$ ' . $total_sales . '</h1>';
		endif;

	$shortcode .= '[/trx_infobox]';

	return do_shortcode( $shortcode );

}

add_shortcode( 'lp_leads', 'lp_get_sheet_leads_shortcode' );

// Add Shortcode
function lp_google_report_shortcode( $atts ) {

	$user_id = get_current_user_id();

	if( !$user_id )
		return __( 'Necessário estar logado para exibir as informações', 'lp' );

	$lp_user_google_report_url = get_user_meta( $user_id, 'lp_user_google_report_url', true );

	// lp_debug( $lp_user_google_report_url );

	if( !$lp_user_google_report_url )
		return __( 'Necessário inserir a Url do Google Report.', 'lp' );

	return '<iframe style="border: 0; width: 100%;" src="' . $lp_user_google_report_url . '" height="700" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';

}

add_shortcode( 'lp_google_report', 'lp_google_report_shortcode' );

function lp_msg() {
	$message = '<div class="lp-login-msg"><p>' . __( '* Contate-nos caso tenha esquecido seu acesso', 'lp' ) . '</p></div>';
	echo $message;
}

add_action( 'login_form', 'lp_msg' );

// function micro_office_custom_login() {}

// add_action( 'wp_head', 'lp_test' );

function lp_test() {
	// lp_debug( lp_ga() );
}
