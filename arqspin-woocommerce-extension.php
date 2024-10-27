<?php
/**
 * Plugin Name: Arqspin WooCommerce Extension
 * Plugin URI: http://woothemes.com/products/woocommerce-extension/
 * Description: Allows Arqspin users to embed their 360 degree spins into product pages.
 * Version: 1.0.0
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * Developer: Nathan Looney/Arqball, LLC
 * Developer URI: http://www.arqball.com/
 * Text Domain: arqspin
 * Domain Path: /languages
 *
 * Copyright: © 2009-2015 WooThemes.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
//-----------------------------------------------------------------------------------------------------//

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		//Add Arqspin tab to the produect settings area.
		add_action( 'woocommerce_product_write_panel_tabs', 'woo_add_custom_admin_product_tab' );

		function woo_add_custom_admin_product_tab() {
			?>
			<li class="custom_tab"><a href="#custom_tab_data"><?php _e( 'Arqspin', 'woocommerce-arqspin' ); ?></a></li>
			<?php
			//Add fields to the Arqspin tab.
			add_action( 'woocommerce_product_write_panels', 'woo_add_custom_general_fields' );
		}

		// Save Arqspin option fields
		add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

	//-----------------------------------Define all the fields for the Arqspin tab.-------------------------------//
		function woo_add_custom_general_fields() {

			global $woocommerce, $post;
			echo '<div id="custom_tab_data" class="panel woocommerce_options_panel" style="display: block;">';
			echo '<div class="options_group">';
		  
			woocommerce_wp_checkbox( 
				array( 
					'id'            => '_arqspin_use_embed', 
					'wrapper_class' => 'show_if_simple', 
					'label'         => __('Use Arqspin Embed', 'woocommerce-arqspin' )
					)
			);  
			woocommerce_wp_text_input( 
				array( 
					'id'          => '_arqspin_spin_id', 
					'label'       => __( 'Spin ID', 'woocommerce-arqspin' ),
					'desc_tip'    => 'true',
				)
			);
			woocommerce_wp_text_input(
				array( 
					'id'                => '_arqspin_spin_width', 
					'label'             => __( 'Width', 'woocommerce-arqspin' ),
					'placeholder'       => '', 
					'type'              => 'number', 
					'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						) 
				)
			);
			woocommerce_wp_text_input( 
				array( 
					'id'                => '_arqspin_spin_height', 
					'label'             => __( 'Height', 'woocommerce-arqspin' ),
					'placeholder'       => '', 
					'type'              => 'number', 
					'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						) 
				)
			);
			woocommerce_wp_checkbox( 
			array( 
				'id'            => '_arqspin_auto_rotate', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __('Auto-Rotate', 'woocommerce-arqspin' )
				)
			);  
			woocommerce_wp_checkbox( 
			array( 
				'id'            => '_arqspin_auto_stop', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __('Auto-Stop', 'woocommerce-arqspin' )
				)
			);  
			woocommerce_wp_checkbox( 
			array( 
				'id'            => '_arqspin_auto_load', 
				'wrapper_class' => 'show_if_simple', 
				'label'         => __('Auto-Load', 'woocommerce-arqspin' )
				)
			);  

			echo '</div>';
			echo '</div>';
		}

	//------------------------------Function to save all arqspin field values.----------------------------//
		function woo_add_custom_general_fields_save($post_id) {

			$woocommerce_arqspin_spin_id = $_POST['_arqspin_spin_id'];
			if( !empty( $woocommerce_arqspin_spin_id)) {
				update_post_meta( $post_id, '_arqspin_spin_id', esc_attr( $woocommerce_arqspin_spin_id));
			}

			$woocommerce_arqspin_spin_width = $_POST['_arqspin_spin_width'];
			if( !empty( $woocommerce_arqspin_spin_width)) {
				update_post_meta( $post_id, '_arqspin_spin_width', esc_attr( $woocommerce_arqspin_spin_width));
			}

			$woocommerce_arqspin_spin_height = $_POST['_arqspin_spin_height'];
			if( !empty( $woocommerce_arqspin_spin_height)) {
				update_post_meta( $post_id, '_arqspin_spin_height', esc_attr( $woocommerce_arqspin_spin_height));
			}

			$woocommerce_arqspin_use_embed = isset( $_POST['_arqspin_use_embed'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_arqspin_use_embed', $woocommerce_arqspin_use_embed );

			$woocommerce_arqspin_auto_rotate = isset( $_POST['_arqspin_auto_rotate'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_arqspin_auto_rotate', $woocommerce_arqspin_auto_rotate );

			$woocommerce_arqspin_auto_stop = isset( $_POST['_arqspin_auto_stop'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_arqspin_auto_stop', $woocommerce_arqspin_auto_stop );

			$woocommerce_arqspin_auto_load = isset( $_POST['_arqspin_auto_load'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_arqspin_auto_load', $woocommerce_arqspin_auto_load );
		}


	//--------------------------ADDING AND EMBEDDING THE SPINS TO THE PRODUCT PAGE-------------------------//
		function remove_single_product_image() {
			$arqspin_enabled = get_post_meta( get_the_ID(), '_arqspin_use_embed', true);
				if ( "yes" == $arqspin_enabled ) { 
				$spinid = get_post_meta( get_the_ID(), '_arqspin_spin_id', true );	
				$width = get_post_meta( get_the_ID(), '_arqspin_spin_width', true );
				$height = get_post_meta( get_the_ID(), '_arqspin_spin_height', true );
				$rotate = get_post_meta( get_the_ID(), '_arqspin_auto_rotate', true );
				$stop = get_post_meta( get_the_ID(), '_arqspin_auto_stop', true );
				$load = get_post_meta( get_the_ID(), '_arqspin_auto_load', true );


				$embed = "<iframe src='https://spins0.arqspin.com/iframe.html?spin=".$spinid;
				if ( "yes" == $rotate ) {
					$embed = $embed."&is=-0.16";
				}
				if ( "no" == $stop ) {
					$embed = $embed."&ms=0.16";
				}
				if ( "no" == $load ) {
					$embed = $embed."&d=1";
				}

				$embed = $embed."' width=".$width." height=".$height." scrolling='no' frameborder='0'></iframe>";
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

				?>
				<div class="images">
				<?php

					echo $embed;
					do_action( 'woocommerce_product_thumbnails' );

				?>
				</div>
				<?php
			}
		}

		add_action( 'woocommerce_before_single_product_summary', 'remove_single_product_image', 10) ;
	}
?>