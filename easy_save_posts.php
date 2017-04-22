<?php

if (!defined('ABSPATH'))
    exit;
add_action('acf/save_post', 'easy_finance_acf', 100);

function easy_finance_acf($post_id)
{
    if ((!wp_is_post_revision($post_id) && 'auto-draft' != get_post_status($post_id) && 'trash' != get_post_status($post_id))) {
        if ($post_id != 'options') {
            $tt = get_post_type($post_id);
        } else {
            $screen = get_current_screen();
            $tt     = $screen->id;
        }
        $changes    = $types = array();
        $tttile     = isset($_POST['post_title']) ? $_POST['post_title'] : '';
        switch ($tt) {
            case 'easy_finances':
            $month=get_field('month');
            $year=get_field('year');
            $name=date("F Y",mktime(0,0,0,$month,1,$year));

            if ($name!=$tttile)
            {
                $changes['post_title']   = ucfirst($name);
                $changes['post_name']   = ucfirst($name);
                $types                   = array('%s','%s');
            }
            break;
            default:
                break;
        }

        if (!empty($changes) && !empty($types) && count($changes) == count($types)) {
            easy_finance_changes($post_id, $changes, $types);

        }



    }

}


/* change slug*/

function easy_finance_changes($id, $changes, $types)
{
    /*id, array('post_title'=>$title) */
    global $wpdb;
    $wpdb->update($wpdb->posts, $changes, array(
        'ID' => $id
    ), $types, array(
        '%d'
    ));
}


function easy_acf_del_acf_meta($post_id)
{
    global $wpdb;
    if ($post_id == 1) {
        $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "postmeta WHERE (meta_value LIKE %s OR meta_value='')", 'field_%'));
    } else {
        $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "postmeta WHERE (meta_value LIKE %s OR meta_value='') AND post_id=%d", 'field_%', $post_id));
    }
}






