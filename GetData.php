<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetData
 *
 * @author david
 */
function getData($url){
    ini_set('memory_limit', '-1');
    $file = fopen($url,"r");
    $index = 0;
    $table = array();
    while(! feof($file))
      {
          $table[$index] = fgetcsv($file);
          $index++;
      }

    fclose($file);
    return $table;
}
function googleTable($data,$var){
    echo "var ".$var." = google.visualization.arrayToDataTable([";
    $type = array();
    for ($k=0; $k<count($data[0]); $k++){
        if (is_numeric($data[1][$k])){
            array_push($type,"numeric");
        } else {
            array_push($type,"string");
        }
    }
    echo "[";
    for ($jj=0; $jj<count($data[0]); $jj++){
        echo "'".$data[0][$jj]."'";
        if ($jj<count($data[0])-1){
            echo ",";
        } else {
            echo "],";
        }
    }
    for ($i=1; $i< count($data); $i++){
        echo "[";
        for ($j=0; $j<count($data[0]); $j++){
            if ($type[$j] == "string"){
                echo "'";
            }
            if ($type[$j] == "numeric" && $data[$i][$j] == ''){
                echo "0";
            } else {
                echo $data[$i][$j];
            }
            if ($type[$j] == "string"){
                echo "'";
            }
            if ($j<count($data[0])-1){
                echo ",";
            } else {
                echo "]";
            }
        }
        if ($i<count($data)-1){
            echo ",\n";
        } else {
            echo "]);\n";
        }
    }
}

?>
