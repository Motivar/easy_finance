<?php
/*
Plugin Name: Easy Finance
Plugin URI: https://www.motivar.io
Description: For cooperations
Version: 0.0.2
Author: Anastasiou K., Giannopoulos N.
Author URI: https://motivar.io
Text Domain:       github-updater
GitHub Plugin URI: https://github.com/Motivar/easy_finance
GitHub Branch:     master
*/

if (!defined('WPINC')) {
    die;
}


/*register post type*/
function easy_finance_custom_posts($post_type)
{
    $all = array(
        array(
            'post' => 'easy_finances',
            'sn' => 'Παραστατικό',
            'pl' => 'Παραστατικά',
            'args' => array(
                'title'
            ),
            'chk' => false,
            'mnp' => 3,
            'icn' => '',
            'slug' => get_option('easy_finances') ?: 'easy-finance',
            'en_slg' => 1
        )
    );
    if ($post_type == 'all') {
        $msg = $all;
    } else {
        foreach ($all as $k) {
            $posttype = $k['post'];
            if ($posttype == $post_type) {
                $msg = $k;
            }
        }
    }
    return $msg;
}

add_action('init', 'easy_finance_my_cpts');

function easy_finance_my_cpts()
{
    $current_user = wp_get_current_user();
    $user_roles   = $current_user->roles;
    if (in_array('administrator', $user_roles) || in_array('editor', $user_roles)) {

        $names = easy_finance_custom_posts('all');
        foreach ($names as $n) {
            $chk          = $n['chk'];
            $hierarchical = false;
            $labels = $args = array();
            $labels = array(
                'name' => $n['pl'],
                'singular_name' => $n['sn'],
                'menu_name' => '' . $n['pl'],
                'add_new' => 'New ' . $n['sn'],
                'add_new_item' => 'New ' . $n['sn'],
                'edit' => 'Edit',
                'edit_item' => 'Edit ' . $n['sn'],
                'new_item' => 'New ' . $n['sn'],
                'view' => 'View ' . $n['sn'],
                'view_item' => 'View ' . $n['sn'],
                'search_items' => 'Search ' . $n['sn'],
                'not_found' => 'No ' . $n['pl'],
                'not_found_in_trash' => 'No trushed ' . $n['pl'],
                'parent' => 'Parent ' . $n['sn']
            );
            $args   = array(
                'labels' => $labels,
                'description' => 'My Simple Bookings post type for ' . $n['pl'],
                'public' => $n['chk'],
                'show_ui' => true,
                'has_archive' => $n['chk'],
                'show_in_menu' => true,
                'exclude_from_search' => $n['chk'],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'hierarchical' => $hierarchical,
                'rewrite' => array(
                    'slug' => $n['post'],
                    'with_front' => true
                ),
                'query_var' => true,
                'supports' => $n['args']
            );

            if (!empty($n['slug'])) {
                $args['rewrite']['slug'] = $n['slug'];
            }

            if (!empty($n['mnp'])) {
                $args['menu_position'] = $n['mnp'];
            }

            if (!empty($n['icn'])) {
                $args['menu_icon'] = $n['icn'];
            }
            register_post_type($n['post'], $args);

            if (isset($n['en_slg']) && $n['en_slg'] == 1) {
                add_action('load-options-permalink.php', function($views) use ($n)
                {
                    if (isset($_POST[$n['post'] . '_slug'])) {
                        update_option($n['post'] . '_slug', sanitize_title_with_dashes($_POST[$n['post'] . '_slug']));
                    }

                    add_settings_field($n['post'] . '_slug', __($n['pl'] . ' Slug'), function($views) use ($n)
                    {
                        $value = get_option($n['post'] . '_slug');
                        echo '<input type="text" value="' . esc_attr($value) . '" name="' . $n['post'] . '_slug' . '" id="' . $n['post'] . '_slug' . '" class="regular-text" placeholder="' . $n['slug'] . '"/>';

                    }, 'permalink', 'optional');
                });

            }


        }
    }
}

add_action( 'init', 'easy_finance_register_my_taxes' );
function easy_finance_register_my_taxes() {
$actions=array(array(__('Μέτοχοι', 'easy-finance'),__('Μέτοχος', 'easy-finance'),'easy_finance_participants',array('easy_finances')),array(__('Έγγραφα', 'easy-finance'),__('Έγγραφο', 'easy-finance'),'easy_finance_doc_type',array('easy_finances')),array(__('Κατηγορίες', 'easy-finance'),__('Κατηγορία', 'easy-finance'),'easy_finance_type',array('easy_finances')),array(__('Φόροι', 'easy-finance'),__('Φόρος', 'easy-finance'),'easy_finance_tax',array('easy_finances')),array(__('Υπηρεσίες', 'easy-finance'),__('Υπηρεσία', 'easy-finance'),'easy_finance_service',array('easy_finances')),array(__('Πηγές', 'easy-finance'),__('Πηγή', 'easy-finance'),'easy_finance_source',array('easy_finances')));

foreach ($actions as $i)
{
$labels=$args=array();
$labels = array( 'name' => $i[0], 'label' => $i[0], 'all_items' =>  __('All', 'easy-finance').' '.$i[0], 'edit_item' =>  __('Edit', 'easy-finance').' '.$i[1], 'update_item' =>  __('Update', 'easy-finance').' '.$i[1], 'add_new_item' => __('New', 'easy-finance').' '.$i[1], 'new_item_name' => __('New', 'easy-finance').' '.$i[1], 'parent_item' => $i[1].' '.__('Parent', 'easy-finance'), 'parent_item_colon' => $i[1].' '.__('Parent :)', 'easy-finance'), 'search_items' => __('Search', 'easy-finance').' '.$i[0], 'popular_items' => __('Popular', 'easy-finance').' '.$i[0], 'separate_items_with_commas' => __('Split', 'easy-finance').' '.$i[0].' '.__('with comma', 'easy-finance'), 'add_or_remove_items' => __('Insert / Delete', 'easy-finance').' '.$i[1], 'choose_from_most_used' => __('Select', 'easy-finance').' '.$i[0]);
$args = array( 'labels' => $labels, 'hierarchical' => true, 'label' => $i[2], 'show_ui' => true, 'query_var' => true, 'rewrite' => array( 'slug' => $i[2] ), 'show_admin_column' => false);
register_taxonomy( $i[2], $i[3], $args );
}

}


if (is_admin())
{
require_once('admin_functions.php');
require_once('easy_edit_posts.php');
require_once('easy_save_posts.php');
require_once('easy_custom_parameters.php');
}




