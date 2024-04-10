<?php

//function tableData()
//{
    require_once(__DIR__ . '/../../../config.php');
    global $DB;
    $consulta = $DB->get_records("local_normateca",null,'id DESC','*',1,1);
    $count = 0;
    foreach ($consulta[1] as $i) {
        $count++;
    }
    $i = 0;
    foreach ($consulta as $item) {
        $html = $html. "<tr>";
        foreach ($item as $data) {
            $html = $html . "<td>" . $data . "</td>";
//    $i++;
//    if($count == $i){
//        $i=0;
//        
//    }
        }
        $html = $html . "</tr>";
    }

    echo $html;
//}
//tableData();