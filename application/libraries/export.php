<?php if(!defined('BASEPATH'))exit ('No direct script access allowed');
class Export{
    function to_excel($array,$filename){
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename='.$filename.'.xls');
        $h=array();
        foreach ($array->result_array() as $row) {
            foreach ($row as $key=>$val) {
                if(!in_array($key,$h)){
                    $h[]=$key;
                }
            }
            
        }
        echo '<table><tr>';
        foreach ($h as $key) {
          $key= ucwords($key);  
          echo '<th>'.$key.'</th>';
        }
        echo '</tr>';
        foreach ($array->result_array() as $row) {
            echo '<tr>';
            foreach ($row as $val) 
                $this->writeRow($val); 
            
           
        } 
        echo '</tr>';
            echo '</table>';
    }
    function writeRow($val){
        echo '<td>'.  utf8_decode($val).'</td>';
    }
}


