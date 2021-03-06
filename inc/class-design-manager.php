<?php
class Lightning_Design_Manager {

	static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_skin_css' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_php' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_callback' ) );
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
	}

	// Set default skin
	static function get_skins() {
		$skins = array(
			'origin' => array(
				'label'           => 'Origin',
				'css_path'        => get_template_directory_uri() . '/design_skin/origin/css/style.css',
				'editor_css_path' => get_template_directory_uri() . '/design_skin/origin/css/editor.css',
				'php_path'        => get_parent_theme_file_path( '/design_skin/origin/origin.php' ),
				'js_path'         => '',
				'callback'        => '',
				'version'         => LIGHTNING_THEME_VERSION,
			),
		);
		return apply_filters( 'lightning-design-skins', $skins );
	}

	static function load_skin_css() {
		$skins        = self::get_skins();
		$current_skin = get_option( 'lightning_design_skin' );
		if ( ! $current_skin ) {
			$current_skin = 'origin';
		}

		if ( ! empty( $skins[ $current_skin ]['version'] ) ) {
			$version = $skins[ $current_skin ]['version'];
		} else {
			$version = '';
		}

		if ( ! empty( $skins[ $current_skin ]['css_path'] ) ) {
			wp_enqueue_style( 'lightning-design-style', $skins[ $current_skin ]['css_path'], array(), $version );
		}

		if ( ! empty( $skins[ $current_skin ]['editor_css_path'] ) ) {
			add_editor_style( $skins[ $current_skin ]['editor_css_path'] . '?ver=' . $version );
		}

		if ( ! empty( $skins[ $current_skin ]['js_path'] ) ) {
			wp_enqueue_script( 'lightning-design-js', $skins[ $current_skin ]['js_path'], array( 'jquery' ), $version, true );
		}

	}

	static function load_skin_php() {
		$skins        = self::get_skins();
		$current_skin = get_option( 'lightning_design_skin' );
		if ( ! $current_skin ) {
			$current_skin = 'origin';
		}
		if ( ! empty( $skins[ $current_skin ]['php_path'] ) && file_exists( $skins[ $current_skin ]['php_path'] ) ) {
			require $skins[ $current_skin ]['php_path'];
		}
	}

	static function load_skin_callback() {
		$skins        = self::get_skins();
		$current_skin = get_option( 'lightning_design_skin' );
		if ( ! empty( $skins[ $current_skin ]['callback'] ) and $skins[ $current_skin ]['callback'] ) {
			call_user_func_array( $skins[ $current_skin ]['callback'], array() );
		}
	}

	static function customize_register( $wp_customize ) {
		$wp_customize->add_setting(
			'lightning_design_skin', array(
				'default'           => 'origin',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$skins = self::get_skins();
		foreach ( $skins as $k => $v ) {
			$skins[ $k ] = isset( $v['label'] ) ? $v['label'] : $k;
		}

		$wp_customize->add_control(
			'lightning_design_skin', array(
				'label'       => __( 'Design skin', 'lightning' ),
				'section'     => 'lightning_design',
				'settings'    => 'lightning_design_skin',
				'description' => '<span style="color:red;font-weight:bold;">' . __( 'If you change the skin, please save once and reload the page.', 'lightning' ) . '</span><br/>' .
					__( 'If you reload after the saving, it will be displayed skin-specific configuration items.', 'lightning' ) . '<br/> ' .
					__( '*There is also a case where there is no skin-specific installation item.', 'lightning' ),
				'type'        => 'select',
				'priority'    => 100,
				'choices'     => $skins,
			)
		);
	}
}

Lightning_Design_Manager::init();
