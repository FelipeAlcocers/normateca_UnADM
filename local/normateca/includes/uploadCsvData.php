<?php
$data = $_GET['parametros'];
$numInputCapture = $_GET['numInputCapture'];

$objdata = new stdClass();

$datacapture = 0;
for($i=0; $i<= sizeof($data)-1; $i++){
    $name = $data[$i]['name'];
    $objdata->$name = $data[$i]['value'];
    if($data[$i]['value'] != ''){
        $datacapture++;
    }
}
//Válida que no exista un recurso con ese nombre y perteneciente 
if($datacapture == $numInputCapture){
    $name  = $objdata->resource_tecnica;
    $language  = $objdata->resource_nombre_img;
    $consultadata = $DB->get_record('local_normateca',array('resource_tecnica'=>$name,'resource_nombre_img'=>$language));
    if($consultadata){
        echo "exist_data";
    }else{
//        print_object($objdata);
        $consulta = $DB->insert_record('local_normateca',$objdata);
        if($consulta){
            echo "success";
        }else{
            echo "error";
        }
        die();
    }
}else{
    //faltan datos por capturar
    echo "missing_data";
    die();
}