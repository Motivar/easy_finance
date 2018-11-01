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



add_action('admin_init',function(){
if (function_exists('acf_add_local_field_group')):

    acf_add_local_field_group(array(
        'key' => 'group_58e3557f6821e',
        'title' => 'Κινήσεις',
        'fields' => array(
            array(
                'key' => 'field_58fa1ff4e6b9f',
                'label' => 'Έξοδα',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_58fa6c77e4a05',
                'label' => 'Καταγραφή',
                'name' => 'expenses',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'min' => 0,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Προσθήκη Εξόδου',
                'collapsed' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_58fa6c78e4a06',
                        'label' => 'Ημερομηνία',
                        'name' => 'date',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'd/m/Y',
                        'return_format' => 'd/m/Y',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a07',
                        'label' => 'Τύπος',
                        'name' => 'type',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'easy_finance_doc_type',
                        'field_type' => 'select',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'return_format' => 'id',
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 0,
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a0c',
                        'label' => 'Κατηγορία',
                        'name' => 'category',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'easy_finance_type',
                        'field_type' => 'select',
                        'allow_null' => 0,
                        'add_term' => 1,
                        'save_terms' => 0,
                        'load_terms' => 0,
                        'return_format' => 'id',
                        'multiple' => 0,
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a09',
                        'label' => 'Ποσό',
                        'name' => 'amount',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a0a',
                        'label' => 'ΦΠΑ',
                        'name' => 'vat',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a0b',
                        'label' => 'Τελικό',
                        'name' => 'final',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                    ),
                    array(
                        'key' => 'field_5adc9949364ba',
                        'label' => 'Κατάλυμα',
                        'name' => 'property',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'sbp_accommodation',
                        ),
                        'taxonomy' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_58fa6c78e4a0d',
                        'label' => 'Αιτιολογία',
                        'name' => 'reason',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_58fa2017e6ba0',
                'label' => 'Έσοδα',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_58e35587133a5',
                'label' => 'Καταγραφή',
                'name' => 'income',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'min' => 0,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Προσθήκη Εσόδου',
                'collapsed' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_58e3571c202b6',
                        'label' => 'Ημερομηνία',
                        'name' => 'date',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'd/m/Y',
                        'return_format' => 'd/m/Y',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_58e355a8133a7',
                        'label' => 'Τύπος',
                        'name' => 'type',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'easy_finance_doc_type',
                        'field_type' => 'select',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'return_format' => 'id',
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 0,
                    ),
                    array(
                        'key' => 'field_58fb06abc3709',
                        'label' => 'Πηγή',
                        'name' => 'source',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'easy_finance_source',
                        'field_type' => 'select',
                        'allow_null' => 0,
                        'add_term' => 1,
                        'save_terms' => 0,
                        'load_terms' => 0,
                        'return_format' => 'id',
                        'multiple' => 0,
                    ),
                    array(
                        'key' => 'field_58e355df133a8',
                        'label' => 'Ποσό',
                        'name' => 'amount',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                    ),
                    array(
                        'key' => 'field_58e355ee133a9',
                        'label' => 'ΦΠΑ',
                        'name' => 'vat',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_58e3560e133aa',
                        'label' => 'Τελικό',
                        'name' => 'final',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '€',
                    ),
                    array(
                        'key' => 'field_5adc99a9c1a46',
                        'label' => 'Κατάλυμα',
                        'name' => 'property',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'sbp_accommodation',
                        ),
                        'taxonomy' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_5adefe7a3a95d',
                        'label' => 'Αιτιολογία',
                        'name' => 'reason',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_58fb10b9051e3',
                'label' => 'Περίοδος',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_58fb10c4051e4',
                'label' => 'Μήνας',
                'name' => 'month',
                'type' => 'number',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50%',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'min' => 1,
                'max' => 12,
                'step' => 1,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_58fb110b051e5',
                'label' => 'Χρόνος',
                'name' => 'year',
                'type' => 'number',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'min' => 2017,
                'max' => '',
                'step' => 1,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'easy_finances',
                ),
                array(
                    'param' => 'current_user_role',
                    'operator' => '==',
                    'value' => 'administrator',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

endif;

},10);