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
    $tax_names=array();
    $sum=array();
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
                if ($aa=='type')
                {
                    $msg.='<h4>Είδος Παραστατικών</h4>';
                }
                else
                {
                    $msg.='<h4>Πηγές Χρημάτων</h4>';
                }

                $msg.='<table><tr><th>Τίτλος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';

                foreach ($b as $bb=>$bbb)
                {
                    $msg.='<tr><td>'.$tax_name[$bb].'</td><td>'.$bbb['amount'].'</td><td>'.$bbb['vat'].'</td><td>'.$bbb['final'].'</td></tr>';
                }
                $msg.='</table>';
                break;
                case 'final':
                $msg.='<h4>Συνολική Ταμειακή Κατάταση</h4>';
                $msg.='<table><tr><th>Τίτλος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                 foreach ($b as $bb=>$bbb)
                {
                    $msg.='<tr><td>'.$ex[$bb][0].'</td><td>'.$bbb['amount'].'</td><td>'.$bbb['vat'].'</td><td>'.$bbb['final'].'</td></tr>';
                }
                $msg.='</table>';
                break;
                default:
                $msg.='<h4>Συναλλαγές Μετόχων</h4>';
                $msg.='<table><tr><th>Τίτλος</th><th>Τύπος</th><th>'.$ex['amount'][0].'</th><th>'.$ex['vat'][0].'</th><th>'.$ex['final'][0].'</th></tr>';
                foreach ($b as $bb=>$bbb)
                {
                    $percent1=get_term_meta($bb,'easy_percent',true) ?: 100;
                        $percent=$percent1/100;
                    foreach ($bbb as $bbbb=>$bbbbb)
                    {

                        $vt=round($sum['type'][$bbbb]['vat']*$percent,2)- $bbbbb['vat'];
                        $at=round($sum['type'][$bbbb]['amount']*$percent,2)- $bbbbb['amount'];
                        $fl=round($sum['type'][$bbbb]['final']*$percent,2)- $bbbbb['final'];
                       $msg.='<tr><td>'.$tax_name[$bb].' ('.$percent1.'%)</td><td>'.$tax_name[$bbbb].'</td><td>'.$at.'</td><td>'.$vt.'</td><td>'.$fl.'</td></tr>';
                    }

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
                    $sum['final']['income']['vat']+=$d['vat'];
                    $sum['final']['income']['amount']+=$d['amount'];
                    $sum['final']['income']['final']+=$d['final'];
                    break;
                    default:
                    $sum['final']['final']['vat']-=$d['vat'];
                    $sum['final']['final']['amount']-=$d['amount'];
                    $sum['final']['final']['final']-=$d['final'];
                    $sum['final']['expenses']['vat']+=$d['vat'];
                    $sum['final']['expenses']['amount']+=$d['amount'];
                    $sum['final']['expenses']['final']+=$d['final'];
                    break;
                }



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

                        if (!isset($sum['participant'][$kl][$ll]))
                    {
                        $sum['participant'][$kl][$ll]=array('vat'=>0,'amount'=>0,'final'=>0);
                    }
                        switch ($a)
                        {
                            case 'income';
                            $sum['participant'][$kl][$ll]['vat']+=$ivat;
                            $sum['participant'][$kl][$ll]['amount']+=$iamount;
                            $sum['participant'][$kl][$ll]['final']+=$ifinal;
                            break;
                            default:
                            $sum['participant'][$kl][$ll]['vat']-=$ivat;
                            $sum['participant'][$kl][$ll]['amount']-=$iamount;
                            $sum['participant'][$kl][$ll]['final']-=$ifinal;
                            break;
                        }






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
                        $tax_name[$kl]='-';
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