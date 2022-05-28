<?php
/*
Plugin Name: AA Woo Show All Products
Plugin URI: https://www.a2area.it/woocommerce-show-all-products/
Description: Use a shortcode to display all Woocommerce products as a list
Version: 1.0
Author: Alessandro Alessio
Author URI: https://www.a2area.it
License: GPLv2 or later
Text Domain: aa-woo-show-all-products
*/

function woocommerce_show_all_products_check_woocommerce_is_active(){
    echo '<div class="notice notice-warning is-dismissible">
        <p>'.__('Woocommerce not active: For right functions of Woocommerce Show All Products you need to activate Woocommerce', 'aa-woo-show-all-products').'</p>
    </div>';
}

add_action( 'init', 'woo_show_all_products_init_check' );
function woo_show_all_products_init_check(){
    if ( ! class_exists( 'WooCommerce', false ) ) { add_action('admin_notices', 'woocommerce_show_all_products_check_woocommerce_is_active'); }
}

add_shortcode("woo_show_all_products", "woocommerce_show_all_products");

if ( !function_exists('woocommerce_show_all_products') ) {
    function woocommerce_show_all_products($atts){

        extract(
            shortcode_atts(array(
                'show_title' => true,
                'show_qty' => true,
                'show_price' => true,
            ), $atts)
        );

        // General Loop
        $args = array(
            'post_type'              => 'product',
            'post_status'            => array( 'publish' ),
            'posts_per_page'         => '-1',
            'order' => 'ASC',
            'order_by' => 'title'

        );
        $query = new WP_Query( $args );

        $html = '';
        if ( $query->have_posts() ) { 
            $html .= '<table class="table">
                <thead>
                    <tr>
                        <th>'.__('ID', 'aa-woo-show-all-products').'</th>
                        <th>'.__('Product Name', 'aa-woo-show-all-products').'</th>
                        <th>'.__('Q.ty', 'aa-woo-show-all-products').'</th>
                        <th>'.__('Price', 'aa-woo-show-all-products').'</th>
                    </tr>
                </thead>
                <tbody>';
                
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $ID = get_the_ID();
                        $product = wc_get_product( $ID );
                        $children = $product->get_children();
                        if ( count($children)>0 ) {
                            foreach ($children as $key => $ID_children) {
                                $children = wc_get_product( $ID_children );
                                $html .= '<tr>
                                    <td>'.$ID_children.'</td>
                                    <td>'.$children->get_name().'</td>
                                    <td>'; 
                                        $stock_quantity = $children->get_stock_quantity();
                                        $html .= ( is_int($stock_quantity) || $stock_quantity!=null ) ? $stock_quantity : '0' ;
                                    $html .= '</td>
                                    <td>';
                                        $price = $children->get_price();
                                        $html .= ( is_numeric($price) ) ? $price.' '.get_woocommerce_currency_symbol() : '0 '.get_woocommerce_currency_symbol();
                                    $html .= '</td>
                                </tr>';
                            }
                        } else {
                            $html .= '<tr>
                                <td>'.$ID.'</td>
                                <td>'.$product->get_name().'</td>
                                <td>';
                                    $stock_quantity = $product->get_stock_quantity();
                                    $html .= ( is_int($stock_quantity) || $stock_quantity!=null ) ? $stock_quantity : '0' ;
                                $html .= '</td>
                                <td>';
                                    $price = $product->get_price();
                                    $html .= ( is_numeric($price) ) ? $price.' '.get_woocommerce_currency_symbol() : '0 '.get_woocommerce_currency_symbol();
                                $html .= '</td>
                            </tr>';
                        }
                    }
                $html .= '</tbody>
            </table>';
        }
        wp_reset_postdata();
        
        return $html;
    }
}

?>