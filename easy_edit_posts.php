<?php
if (!defined('ABSPATH'))
    exit;


add_action('add_meta_boxes', 'easy_finance_metas');
function easy_finance_metas()
{

    add_meta_box('analysis', // $id
        'Συνολική Ανάλυση', // $title
        'easy_finance_analysis', // $callback
        array(
        'easy_finances'
    ), // $page
        'normal', // $context
        'low'); // $priority

}



function easy_finance_analysis($post)
{
    /* all information regarding payment*/
    $msg = '';
    $arrs=array('income'=>'Έσοδα','expenses'=>'Έξοδα','sum'=>'Σύνολο');
    $ex=array('participant'=>array('Μέτοχος','easy_finance_participants'),'type'=>array('Τύπος','easy_finance_doc_type'),'category'=>array('Κατηγορία','easy_finance_type'),'vat'=>array('ΦΠΑ'),'amount'=>array('Ποσό'),'final'=>array('Τελικό'),'service'=>array('Υπηρεσία','easy_finance_service'),'sum'=>'Σύνολο','source'=>array('Πηγή','easy_finance_source'),'income'=>array('Έσοδα'),'expenses'=>array('Έξοδα'));
    $count=array();
    $msg2='';
    $tax_names=$percentt=array();
    $sum=$last_sum=array();
    $parts = get_terms( array('taxonomy' => 'easy_finance_participants',
        'hide_empty' => false,
) );
    if (!empty($parts))
    {
    foreach ($parts as $p)
    {
        if (!isset($percentt[$p->term_id]))
            {
                $percentt[$p->term_id][0]=get_term_meta($p->term_id,'easy_percent',true) ?: 100;
                $percentt[$p->term_id][1]=get_term_meta($p->term_id,'easy_percent_holder',true) ?: 0;
            }
        if (!isset($tax_name[$p->term_id]))
                {
                $tax_name[$p->term_id]=$p->name;
                }
    }
    }
    $taken=array();
    $movements=get_field('movements', $post->ID);
    if (!empty($movements))
    {
        foreach ($movements as $m)
        {
            if (!$taken[$m['participant']])
                {
                $taken[$m['participant']][0]=0;
                $taken[$m['participant']][1]=$percentt[$m['participant']][1];
                }
            $taken[$m['participant']][0]+=absint($m['amount']);
        }
    }
    foreach ($arrs as $a=>$v)
    {

        switch ($a)
        {
        case 'sum':
        $msg.='<h2>'.$v.'</h2>';

        foreach ($sum as $aa=>$b)
        {
             switch ($aa)
            {
                case 'type':
                case 'source':
                $dd=0;
                if ($aa=='type')
                {
                    $msg.='<h4>Είδος Παραστατικών</h4>';
                    $dd=1;
                }
                else
                {
                    $msg.='<h4>Πηγές Χρημάτων</h4>';
                }

                $msg.='<table><tr><th>Τίτλος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                foreach ($b as $bb=>$bbb)
                {
                    $msg.='<tr><td><strong>'.$tax_name[$bb].'</strong></td><td>'.$bbb['amount'].'</td><td>'.$bbb['vat'].'</td><td>'.$bbb['final'].'</td></tr>';
                    if ($dd!=0)
                    {
                    foreach ($parts as $p)
                    {
                        $percent1=$percentt[$p->term_id][0];
                        $percent=$percent1/100;
                        $namp=round($sum['final']['income'][$aa][$bb]['amount']*$percent,2);
                        $nvtp=round($sum['final']['income'][$aa][$bb]['vat']*$percent,2);
                        $nflp=round($sum['final']['income'][$aa][$bb]['final']*$percent,2);

                        $namm=round($sum['final']['expenses'][$aa][$bb]['amount']*$percent,2);
                        $nvtm=round($sum['final']['expenses'][$aa][$bb]['vat']*$percent,2);
                        $nflm=round($sum['final']['expenses'][$aa][$bb]['final']*$percent,2);
                        $msg.='<tr><td colspan="4">'.$tax_name[$p->term_id].' ('.$percent1.'%) <small>Έσοδα/Έξοδα</small></td></tr>';
                         $msg.='<tr><td><strong>Ποσοστό</strong></td><td>'.$namp.' / '.$namm.'</td><td>'.$nvtp.' / '.$nvtm.'</td><td>'.$nflp.' / '.$nflm.'</td></tr>';
                        $msg.='<tr><td><strong>Κινήσεις</strong></td><td>'.$sum['final']['income']['participant'][$p->term_id][$bb]['amount'].' / '.$sum['final']['expenses']['participant'][$p->term_id][$bb]['amount'].'</td><td>'.$sum['final']['income']['participant'][$p->term_id][$bb]['vat'].' / '.$sum['final']['expenses']['participant'][$p->term_id][$bb]['vat'].'</td><td>'.$sum['final']['income']['participant'][$p->term_id][$bb]['final'].' / '.$sum['final']['expenses']['participant'][$p->term_id][$bb]['final'].'</td></tr>';


                        $fnamp=$namp-$sum['final']['income']['participant'][$p->term_id][$bb]['amount'];
                        $fnvtm=$nvtm-$sum['final']['income']['participant'][$p->term_id][$bb]['vat'];
                        $fnflp=$nflp-$sum['final']['income']['participant'][$p->term_id][$bb]['final'];

                        $fnamm=$namm-$sum['final']['expenses']['participant'][$p->term_id][$bb]['amount'];
                        $fnvtm=$nvtm-$sum['final']['expenses']['participant'][$p->term_id][$bb]['vat'];
                        $fnflm=$nflm-$sum['final']['expenses']['participant'][$p->term_id][$bb]['final'];
                        $msg.='<tr><td><strong>Τελικά</strong></td><td>'.$fnamp.' / '.$fnamm.'</td><td>'.$fnvtp.' / '.$fnvtm.'</td><td>'.$fnflp.' / '.$fnflm.'</td></tr>';



                        if (!isset($last_sum[$p->term_id]))
                        {
                            $last_sum[$p->term_id]['am']=$last_sum[$p->term_id]['vt']=$last_sum[$p->term_id]['fl']=0;
                        }

                        $last_sum[$p->term_id]['am']+=$fnamp-$fnamm;
                        $last_sum[$p->term_id]['vt']+=$fnvtp-$fnvtm;
                        $last_sum[$p->term_id]['fl']+=$fnflp-$fnflm;
                    }
                    $msg.='<tr><td colspan="4"><strong>-------------------</strong></td></tr>';
                  }

                }
                if ($dd==1)
                {
                $msg.='<tr><td colspan="4"><strong>Ισοσκελισμός Ταμείου - Συναλλαγές Μετόχων</strong></td></tr>';
                foreach ($last_sum as $vp=>$p)
                    {
                        $nameeee=$tax_name[$vp];
                        $fnk=$p['fl'];
                        if (isset($taken[$vp]))
                        {

                            if ($taken[$vp][1]==0)
                                {
                                $fnk-=$taken[$vp][0];
                                $fnk.=' <small>('.$p['fl'].' - '.$taken[$vp][0].')</small>';
                                }
                            else
                            {
                                $nameeee.=' <small>(ταμείας)</small>';
                            }
                            $nameeee.=' - Εκταμιεύσεις: <strong>'.$taken[$vp][0].'</strong>';

                        }
                       $msg.='<tr><td>'.$nameeee.'</td><td>'.$p['am'].'</td><td>'.$p['vt'].'</td><td>'.$fnk.'</td></tr>';
                    }

                }

                $msg.='</table>';
                break;
                case 'final':
                $msg.='<h4>Συνολική Ταμειακή Κατάταση</h4>';
                $msg.='<table><tr><th>Τίτλος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                foreach ($b as $bb=>$bbb)
                {
                    $msg.='<tr><td><strong>'.$ex[$bb][0].'</strong></td><td>'.$bbb['amount'].'</td><td>'.$bbb['vat'].'</td><td>'.$bbb['final'].'</td></tr>';

                    update_post_meta($post->ID,$bb.'e',$bbb['final']);
                    /*foreach ($parts as $p)
                    {
                        $percent1=get_term_meta($p->term_id,'easy_percent',true) ?: 100;
                        $percent=$percent1/100;
                        $cam=round($bbb['amount']*$percent,1);
                        $cvat=round($bbb['vat']*$percent,2);
                        $cfinal=round($bbb['final']*$percent,2);
                        $msg.='<tr><td>'.$tax_name[$p->term_id].'</td><td>'.$cam.'</td><td>'.$cvat.'</td><td>'.$cfinal.'</td></tr>';

                    }*/
                }

                $msg.='</table>';
                break;
            }
        }
        $msg.='<hr/><br>';
        break;
        default:
        $msg2.='<h2>'.$v.'</h2>';
        $data=get_field($a, $post->ID);
        if (!empty($data))
        {
          $vat=$amount=$final=0;
            switch ($a)
            {
                case 'expenses':
                $akl='category';
                break;
                default:
                $akl='service';
                break;

            }
            foreach ($data as $d)
            {
            $count[$a][$akl][$d[$akl]][]=$count[$a]['source'][$d['source']][]=$count[$a]['type'][$d['type']][]=$count[$a]['participant'][$d['participant']][$d['type']][]=array('amount'=>$d['amount'],'vat'=>$d['vat'],'final'=>$d['final']);
            if (empty($sum['final']))
            {
                $sum['final']['final']=array('vat'=>0,'amount'=>0,'final'=>0);
                $sum['final']['income']=array('vat'=>0,'amount'=>0,'final'=>0);
                $sum['final']['expenses']=array('vat'=>0,'amount'=>0,'final'=>0);
            }

             switch ($a)
                {
                    case 'income';
                    $sum['final']['final']['vat']+=$d['vat'];
                    $sum['final']['final']['amount']+=$d['amount'];
                    $sum['final']['final']['final']+=$d['final'];
                    break;
                    default:
                    $sum['final']['final']['vat']-=$d['vat'];
                    $sum['final']['final']['amount']-=$d['amount'];
                    $sum['final']['final']['final']-=$d['final'];

                    break;
                }

                    $sum['final'][$a]['vat']+=$d['vat'];
                    $sum['final'][$a]['amount']+=$d['amount'];
                    $sum['final'][$a]['final']+=$d['final'];
            $vat+=$d['vat'];
            $amount+=$d['amount'];
            $final+=$d['final'];
            }
            foreach ($count[$a] as $dd=>$vv)
            {
                $msg2.='<h4>'.$ex[$dd][0].'</h4><table>';

                switch ($dd)
                {
                    case 'participant':
                    $msg2.='<tr><th>Τίτλος</th><th>Τύπος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                    foreach ($vv as $kl=>$lk)
                    {

                       if (!isset($tax_name[$kl]))
                            {
                            $term=get_term( $kl, $ex[$dd][1]);
                            $tax_name[$kl]=$term->name;
                            }
                        $name=$tax_name[$kl];
                        $kd=0;
                        foreach ($lk as $ll=>$vs)
                        {
                            if (!isset($tax_name[$ll]))
                            {
                            $term=get_term( $ll, $ex['type'][1]);
                            $tax_name[$ll]=$term->name;
                            }
                            $first='';
                            $iamount=$ivat=$ifinal=0;
                            foreach ($vs as $o=>$p)
                            {
                            $iamount+=$p['amount'];
                            $ivat+=$p['vat'];
                            $ifinal+=$p['final'];
                            }
                        if ($kd==0)
                        {
                            $first='<td rowspan="'.count($lk).'">'.$name.'</td>';
                        }
                        $msg2.='<tr>'.$first.'<td>'.$tax_name[$ll].'</td><td>'.$iamount.'</td><td>'.$ivat.'</td><td>'.$ifinal.'</td></tr>';

                        $kd++;

                        if (!isset($sum[$a]['participant'][$kl][$ll]))
                            {
                            $sum['final'][$a][$aa]['participant'][$kl][$ll]=array('vat'=>0,'amount'=>0,'final'=>0);
                            }
                            $sum['final'][$a]['participant'][$kl][$ll]['vat']+=$ivat;
                            $sum['final'][$a]['participant'][$kl][$ll]['amount']+=$iamount;
                            $sum['final'][$a]['participant'][$kl][$ll]['final']+=$ifinal;
                        }
                    }

                    break;
                    default:
                    $msg2.='<tr><th>Τίτλος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                foreach ($vv as $kl=>$lk)
                {
                   $ivat=$iamount=$ifinal=0;
                    foreach ($lk as $ll)
                    {
                        $iamount+=$ll['amount'];
                        $ivat+=$ll['vat'];
                        $ifinal+=$ll['final'];
                    }
                    if (!isset($tax_name[$kl]))
                    {
                        if ($kl==0)
                        {
                        $tax_name[$kl]=' / ';
                        }
                        else
                        {
                        $term=get_term( $kl, $ex[$dd][1]);
                        $tax_name[$kl]=$term->name;
                        }

                    }

                   $msg2.='<tr><td>'.$tax_name[$kl].'</td><td>'.$iamount.'</td><td>'.$ivat.'</td><td>'.$ifinal.'</td></tr>';
                    if ($dd=='type' || $dd=='source')
                    {
                    if (!isset($sum[$dd][$kl]))
                    {
                        $sum[$dd][$kl]=array('vat'=>0,'amount'=>0,'final'=>0);
                        $sum['final'][$a][$dd][$kl]=array('vat'=>0,'amount'=>0,'final'=>0);
                    }
                        switch ($a)
                        {
                            case 'income';
                            $sum[$dd][$kl]['vat']+=$ivat;
                            $sum[$dd][$kl]['amount']+=$iamount;
                            $sum[$dd][$kl]['final']+=$ifinal;
                            break;
                            default:
                            $sum[$dd][$kl]['vat']-=$ivat;
                            $sum[$dd][$kl]['amount']-=$iamount;
                            $sum[$dd][$kl]['final']-=$ifinal;
                            break;
                        }
                        $sum['final'][$a][$dd][$kl]['vat']+=$ivat;
                        $sum['final'][$a][$dd][$kl]['amount']+=$iamount;
                        $sum['final'][$a][$dd][$kl]['final']+=$ifinal;
                    }

                }
                    break;
                }



                $msg2.='</table>';
            }
        }
        else
        {
        $msg2.='Δεν υπάρχουν Δεδομένα!';
        }
            break;
        }

        $msg2.='<hr/><br>';
    }



    echo $msg.$msg2;
}






function register_custom_column2()
{
    $columns_array = array(
        array(
            'easy_finances',
            /*columns to insert*/
            array(
                array(
                    'incomee',
                    'Έσοδα',
                    0,
                    0
                ),
                array(
                    'expensese',
                    'Έξοδα',
                    0,
                    0
                ),
                array(
                    'finale',
                    'Τελικό Ποσό',
                    0,
                    0
                )
            ),
            /*columns to delete*/
            array(
                'date',
                'tags',
                'comments'
            )
        )
    );


    return $columns_array;
}

/*bulk import data for admin columns
SOSSOSSOS NEVER use '/' inside names
*/
$columns_array = register_custom_column2();
if (!empty($columns_array)) {
    foreach ($columns_array as $post_array) {
        $sortables = array();
        add_action('manage_edit-' . $post_array[0] . '_columns', function($columns) use ($post_array)
        {
            /*global actions*/
            /*insert columns*/
            if (!empty($post_array[1])) {
                foreach ($post_array[1] as $s) {
                    $columns[$s[0]] = $s[1];
                    if ($s[2] == 1) {
                        add_action('manage_edit-' . $post_array[0] . '_sortable_columns', function($sortable) use ($s)
                        {
                            $sortable[$s[0]] = $s[0];
                            return $sortable;
                        });
                    }
                }
            }
            /*empty columns*/
            if (!empty($post_array[2])) {
                foreach ($post_array[2] as $s) {
                    unset($columns[$s]);
                }
            }
            return $columns;
        });
    }
    add_action('pre_get_posts', 'custom_sorting2');
    add_action('manage_posts_custom_column', 'manage_posts_function2', 10, 2);
}







function manage_posts_function2($column_name, $post_id)
{
    $msg = '';
    switch ($column_name) {
        case 'incomee':
        case 'expensese':
        case 'finale':
            $msg = get_post_meta($post_id, $column_name, true) ?: 0;
            $msg.=' €';
            break;
        default:
            break;
    }
    echo $msg;
}





function custom_sorting2($query)
{
    global $pagenow;
    $meta_query = $tax_query = array();

    if (!is_admin())
        return;
    $orderby       = $query->get('orderby');
    $columns_array = register_custom_columns();
    if (!empty($columns_array)) {
        foreach ($columns_array as $post_array) {
            foreach ($post_array[1] as $s) {
                if ($s[0] == $orderby) {
                    $query->set('meta_key', $orderby);
                    $type = 'meta_value';
                    switch ($s[3]) {
                        case 1:
                            $type = 'meta_value_num';
                            break;

                        default:
                            break;
                    }
                    $query->set('orderby', $type);
                }
            }
        }

    }
}










