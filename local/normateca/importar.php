<?php
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/csvlib.class.php');

$previewrows = optional_param('previewrows', 10, PARAM_INT);

echo $OUTPUT->header();

if (empty($iid)) {

    $mform1 = new admin_export_repository_form();
    if ($formdata = $mform1->get_data()) {
//        print_object($formdata);
        $iid = csv_import_reader::get_new_iid('uploaddatarepository');
        $cir = new csv_import_reader($iid, 'uploaddatarepository');

        $content = $mform1->get_file_content('userfile');
//        print_object($formdata);
        $ArrayDelimiter = array(
            'comma' => ",",
            'colon' => ":",
            'semicolon' => ";",
        );
        $delimitador = '';
        $keys = array_keys($ArrayDelimiter);
        for ($i=0; $i<= sizeof($keys)-1; $i++){
            $key =$keys[$i];
           if($formdata->delimiter_name == $key){
               $delimitador =  $ArrayDelimiter[$keys[$i]];
           }
        }

        if($delimitador == ''){
            print_error("Error al obtener el delimitador verifique la configuración del separador");
        }
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
//        print_object($readcount);
        $csvloaderror = $cir->get_error();
//        print_object($csvloaderror);
        $contenidoCsv = $content;
        unset($content);

        if (!is_null($csvloaderror)) {
            print_error("No hay suficientes columnas; por favor, verifique la configuración del separador");
        }
//     Continue to form2.

    } else {
//    echo $OUTPUT->header();

//    echo $OUTPUT->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');
        echo '
    <h2>Cargar datos<a class="btn btn-link p-0" role="button" data-container="body" data-toggle="popover" data-placement="right" data-content="<div class=&quot;no-overflow&quot;><p>Las datos pueden ser cargados de manera masiva usando un archivo de texto csv, el formato del archivo debe ser el siguiente:</p>

<ul>
<li>Cada línea del archivo contiene un registro</li>
<li>Cada registro es una serie de datos separados por el separador seleccionado</li>
<li>El primer registro contiene una lista de nombres los cuales son identificadores para poder agregar los datos en su posición correcta.</li>
<li>Los nombres de los campos obligatorios son resource_no_tecnica, resource_tecnica, resource_que_es, resource_estructura, resource_keywords, resource_utilidad, resource_como_construye, resource_tomar_cuenta, resource_autores_dicen, resource_resumen, resource_nivel, resource_2type_resource, resource_referencias, resource_nombre_img, resource_category, resource_course</li>
</ul>
</div> <div class=&quot;helpdoclink&quot;>Para más información descarga el ejemplo.</div>" data-html="true" tabindex="0" data-trigger="focus">
            <i class="icon fa fa-question-circle text-info fa-fw " title="Help with Upload users" role="img" aria-label="Help with Upload users"></i>
        </a>
    </h2>';
        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
}
else {
    $cir = new csv_import_reader($iid, 'uploaddatarepository');
}
$columnasHeader = $cir->get_columns(); //obteniendo las columnas
//print_object($columnasHeader);
$numHeader = count($columnasHeader)-1;
//echo $numHeader;
$ArrayData = explode($delimitador,$contenidoCsv);
    $i = 0;
    $array = array();
    $array2 = $array;
    foreach ($ArrayData as $item) {
        if ($i == $numHeader) {
            array_push($array2, $array);
            $array = array();
            $i = 0;
        }
        $i++;
        array_push($array, $item);
    }

//print_object($ArrayData);
if($formdata->showdata == 0) {

    echo "<p>Verifica la información y da clic en Importar Datos</p>";

    echo "<table class='table table-striped'><tr>";
    foreach ($columnasHeader as $header) {
        echo "<th>$header</th>";
    }
    echo "</tr>";
//Itera a través de las filas del array y realiza la acción deseada para cada fila
    $i = 0;
    foreach ($array2 as $row) {
        echo "<tr>";
        $i++;
        foreach ($row as $item) {
            if ($i > 1) {
                echo "<td>" . $item . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}else{
    $j=0;
    $datos = array();
    $arreglo = [];
    for($i=0; $i< sizeof($array2); $i++){
        foreach ($array2[$i] as $item){
            if($i >= 1){
                $arreglo[$array2[0][$j]] = $item;
                //para obtener el url a validar
                if($array2[0][$j] == 'resource_tecnica'){
                    $title = $item;
                }
                //para obtener el id del curso y categoría
                if($array2[0][$j] == 'resource_category'){
                    $category = $item;
                }
                if($array2[0][$j] == 'resource_course'){
                    $course = $item;
                }
                $j++;
            }
        }
        $j=0;
        //comprueba si los elementos de ese arreglo ya estan registrados
        $consulta = $DB->get_record('local_normateca',array('resource_tecnica'=> "$title",'resource_category'=>$category,'resource_course'=>$course));
        $noinserta =0;
//        print_object($consulta);
        if($consulta){
            $noinserta = 1;
        }
        //Solo agregara los que no se encuentren en la bd
        if($i >= 1 && $noinserta == 0) {
            array_push($datos, $arreglo);
            $j = 0;
        }
    }
    if(sizeof($datos) == 0){
        $message = "Los datos que intentas exportar ya se encuentra registrados, revisar el documento.";
        redirect('importar.php', $message, 15, \core\output\notification::NOTIFY_WARNING);
    }else{
        $insercion = $DB->insert_records('local_normateca',$datos);
        $message = "Datos importados con éxito.";
        redirect('index.php', $message, 15, \core\output\notification::NOTIFY_SUCCESS);
    }
}
?>




