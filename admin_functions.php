<?php
if (!defined('ABSPATH')) exit;


add_action('admin_head', 'easy_finance_custom_css');

function easy_finance_custom_css() {

    $classes=array('#easy_finance_participantsdiv','#easy_finance_doc_typediv','#easy_finance_typediv','#easy_finance_taxdiv','#easy_finance_servicediv','#easy_finance_sourcediv');

    $screen = get_current_screen();
    if ($screen->post_type=='easy_finances')
    {

    $final=implode(',',$classes);
    echo '<style>'.$final.'{display: none !important;visibility:hidden !important;opacity:0 !important;pointer-events:none !important;}

    #analysis table
    {
      table-layout:fixed !important;
      width:100%;
          }
          #analysis td
          {
            padding:1%;
            text-align:center;
          }
          input#title
          {

          }

    </style>';
    echo '<script>jQuery(\''.$final.'\').remove(); </script>';
  }
}

/*


 $option_page = acf_add_options_page(array(
    'page_title'  => 'Έξοδα',
    'menu_title'  => 'Έξοδα',
    'menu_slug'   => 'wibee-expenses',
    'capability'  => 'read',
    'redirect'  => false
  ));
*/





add_action( 'easy_finance_participants_add_form_fields', 'easy_finance_field', 10, 2 );
  /*this is for amenities*/
function easy_finance_field($taxonomy) {
$msg='<div class="form-field term-group form-required"><label for="easy_percent">Percent</label><input type="number" min="0" max="100"aria-required="true" class="postform" name="easy_percent" />';
$msg.='</div>';
$msg.='<div class="form-field term-group form-required"><label for="easy_percent_holder">Money Holder</label><input type="checkbox" value="1" aria-required="true" class="postform" name="easy_percent_holder" />';
$msg.='</div>';
echo $msg;
}

add_action( 'easy_finance_participants_edit_form_fields', 'easy_finance_field2', 10, 2 );

function easy_finance_field2( $term, $taxonomy ){
$val = get_term_meta( $term->term_id, 'easy_percent', true ) ?: 0;
echo '<tr class="form-field term-group-wrap form-required"><th scope="row"><label for="easy_percent">Percent</label></th><td><input type="number" min="0" aria-required="true" class="postform" name="easy_percent" value="'.$val.'"/></td></tr>';
$val2 = get_term_meta( $term->term_id, 'easy_percent_holder', true ) ?: '';
$ext='';
if ($val2!='')
{
$ext=' checked="true"';
}
echo '<tr class="form-field term-group-wrap form-required"><th scope="row"><label for="easy_percent_holder">Money Holder</label></th><td><input type="checkbox" aria-required="true" class="postform" name="easy_percent_holder" value="1" '.$ext.'/></td></tr>';
}



add_action( 'easy_finance_doc_type_add_form_fields', 'easy_finance_field3', 10, 2 );
  /*this is for amenities*/
function easy_finance_field3($taxonomy) {
$msg='<div class="form-field term-group form-required"><label for="easy_percent">Tax Removal</label><input type="number" min="0" max="100"aria-required="true" class="postform" name="easy_percent" />';
$msg.='</div>';
echo $msg;
}

add_action( 'easy_finance_doc_type_edit_form_fields', 'easy_finance_field4', 10, 2 );

function easy_finance_field4( $term, $taxonomy ){
$val = get_term_meta( $term->term_id, 'easy_percent', true ) ?: 0;
echo '<tr class="form-field term-group-wrap form-required"><th scope="row"><label for="easy_percent">Tax Removal</label></th><td><input type="number" min="0" aria-required="true" class="postform" name="easy_percent" value="'.$val.'"/></td></tr>';
}












add_action('create_term','easy_finance_custom_tax_functions_update',10,3);
add_action('edit_term', 'easy_finance_custom_tax_functions_update',10,3);
//add_action('delete_term', 'custom_functions_delete');

function easy_finance_custom_tax_functions_update($term_id)
{

if (isset($_POST['taxonomy']))
  {
  switch ($_POST['taxonomy']) {
    case 'easy_finance_participants':
      $priority=isset($_POST['easy_percent']) ? $_POST['easy_percent'] : 0;
      update_term_meta( $term_id, 'easy_percent',$priority);
      $holder=isset($_POST['easy_percent_holder']) ? $_POST['easy_percent_holder'] : '';
      update_term_meta( $term_id, 'easy_percent_holder',$holder);
      break;
   case 'easy_finance_doc_type':
      $priority=isset($_POST['easy_percent']) ? $_POST['easy_percent'] : 0;
      update_term_meta( $term_id, 'easy_percent',$priority);
      break;
    default:
      break;
  }

  }
}

update_message_field('field_59061e8a89120', '<b>My message</b>');

function update_message_field($field_key='', $message='')
{
  global $wpdb;

  $table = $wpdb->prefix.'postmeta';
  $field = $wpdb->get_results("SELECT * FROM $table WHERE meta_key = '$field_key'");
  if($field)
  {
    $meta = unserialize($field[0]->meta_value);
    $meta['message'] = $message;
    $wpdb->update(
      $table,
      array(
        'meta_value'=>serialize($meta)
      ),
      array('meta_key'=>$field_key),
      array('%s')
    );
  }
}



