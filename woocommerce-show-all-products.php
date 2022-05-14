<?php
/*
Plugin Name: Woocommerce Show All Products
Plugin URI: https://www.a2area.it/woocommerce-show-all-products/
Description: Use a shortcode to display all Woocommerce products as a list
Version: 1.0
Author: Alessandro Alessio
Author URI: https://www.a2area.it
License: GPLv2 or later
Text Domain: woocommerce-show-all-products
*/

function woo_show_all_products_check_woo_is_active(){
    echo '<div class="notice notice-warning is-dismissible">
        <p>'.__('Woocommerce not active: For right functions of Woocommerce Show All Products you need to activate Woocommerce', 'woocommerce-show-all-products').'</p>
    </div>';
}
if ( ! class_exists( 'WooCommerce', false ) ) { add_action('admin_notices', 'woo_show_all_products_check_woo_is_active'); }



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

        if ( $query->have_posts() ) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'woocommerce-show-all-products') ?></th>
                        <th><?php _e('Product Name', 'woocommerce-show-all-products') ?></th>
                        <th><?php _e('Q.ty', 'woocommerce-show-all-products') ?></th>
                        <th><?php _e('Price', 'woocommerce-show-all-products') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $ID = get_the_ID();
                        $product = wc_get_product( $ID );
                        $children = $product->get_children();
                        if ( count($children)>0 ) {
                            foreach ($children as $key => $ID_children) {
                                $children = wc_get_product( $ID_children );
                                ?>
                                <tr>
                                    <td><?=$ID_children ?></td>
                                    <td><?=$children->get_name() ?></td>
                                    <td>
                                        <?php 
                                        $stock_quantity = $children->get_stock_quantity();
                                        echo ( is_int($stock_quantity) || $stock_quantity!=null ) ? $stock_quantity : '0' ;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $price = $children->get_price();
                                        echo ( is_numeric($price) ) ? $price.' '.get_woocommerce_currency_symbol() : '0 '.get_woocommerce_currency_symbol();
                                        ?>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td><?=$ID ?></td>
                                <td><?=$product->get_name() ?></td>
                                <td>
                                    <?php 
                                    $stock_quantity = $product->get_stock_quantity();
                                    echo ( is_int($stock_quantity) || $stock_quantity!=null ) ? $stock_quantity : '0' ;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $price = $product->get_price();
                                    echo ( is_numeric($price) ) ? $price.' '.get_woocommerce_currency_symbol() : '0 '.get_woocommerce_currency_symbol();
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }
        wp_reset_postdata();
    }
}

?>