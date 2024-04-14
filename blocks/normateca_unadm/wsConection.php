<?php
require('../../config.php');
require_once('classes/event/consult_normateca_unadm.php');

$mysqli = new mysqli('localhost' , 'mdl_user' , 'elearning', 'normateca_unadm');
$serverData = "";
if ($mysqli->connect_errno) {
    echo "Falló la conexión: error_connect_normatecaunadm" . $mysqli->connect_error;
    exit();
}

$mysqli->set_charset('utf8');

//Divisiones
if(isset( $_POST['funtion']) && $_POST['funtion'] == 'getDivisiones' ){
    $filtro='activo=1';
    $ordenamiento='nombre_largo';
    $consulta = "SELECT * FROM cat_divisiones WHERE $filtro ORDER BY $ordenamiento ASC ";
    $result = $mysqli->query($consulta);
    $dataSend = '<option value="0">Seleccionar división</option>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataSend.='<option value="'.$row['id_division'].'">'.$row['nombre_largo'].'</option>';
        }
    }else{
        $dataSend='error_not_data';
    }
    echo $dataSend;
    die();
}

//Carreras
if(isset( $_POST['funtion']) && $_POST['funtion'] == 'getCarreras' ){
if(isset( $_POST['id_division']) && $_POST['id_division'] > 0 ){
    $filtro='estatus=1 and id_division='.$_POST['id_division'];
    $ordenamiento='nombre_largo';
    $consulta = "SELECT * FROM cat_carreras WHERE $filtro ORDER BY $ordenamiento ASC ";
    $result = $mysqli->query($consulta);
    $dataSend = '<option value="0">Seleccionar carrera</option>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataSend.='<option value="'.$row['id_carrera'].'">'.$row['nombre_largo'].'</option>';
        }
    }else{
        $dataSend='error_not_data';
    }
    echo $dataSend;
}else{
    $dataSend='error_not_division';
}
    die();
}

//Dependendias
if(isset( $_POST['funtion']) && $_POST['funtion'] == 'getDependencias' ){
    $filtro='estatus=1';
    $ordenamiento='nombre_largo';
    $consulta = "SELECT * FROM cat_dependencias WHERE $filtro ORDER BY $ordenamiento ASC ";
    $result = $mysqli->query($consulta);
    $dataSend = '<option value="0">Seleccionar división</option>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataSend.='<option value="'.$row['id_dependencia'].'">'.$row['nombre_largo'].'</option>';
        }
    }else{
        $dataSend='error_not_data_dependencia';
    }
    echo $dataSend;
    die();
}

//Recursos
if(isset( $_POST['funtion']) && $_POST['funtion'] == 'getRecurso' ){
    $filtro='estatus=1';
    $ordenamiento='nombre';
    $consulta = "SELECT * FROM cat_recurso WHERE $filtro ORDER BY $ordenamiento ASC ";
    $result = $mysqli->query($consulta);
    $dataSend = '<option value="0">Seleccionar recurso</option>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataSend.='<option value="'.$row['id_recurso'].'">'.$row['nombre'].'</option>';
        }
    }else{
        $dataSend='error_not_data_recurso';
    }
    echo $dataSend;
    die();
}

//Variables
$colorsNivel = [
    '1'=>'#7351a1',
    '2'=>'#ee7b45',
    '3'=>'#4ead49',
    '4'=>'#dbb11e',
    '5'=>'#4579ae',
    '6'=>'#e01f8f',
];
$urlPdfGenerator = "https://100tecnicasdidacticas.unadmexico.mx/generate_get_PDF.php?numeroDeTecnica=";
$urlImage = "https://100tecnicasdidacticas.unadmexico.mx/local/resource_repository/docs/";
$columtiperesource= "resource_type_resource";
$columnumtecnica= "resource_no_tecnica";

if(isset( $_POST['filtro']) && isset( $_POST['busqueda'])) {
    $filtro = $_POST['filtro'];
    $busqueda = $_POST['busqueda'];

    if ($filtro != '' && $busqueda != '') {
//    echo "Busqueda solo por busqueda avanzada";
        if($filtro == $columtiperesource ){
            //Para buscar tanto por nombre o número de nivel
            $consulta = "SELECT * FROM mdl_local_resource_repository WHERE $filtro LIKE '%$busqueda%' OR resource_nivel LIKE '%$busqueda%'   ORDER BY $filtro ASC ";
        }else if( $filtro == $columnumtecnica ){
            //Para buscar cuando es por número de técnica
            $consulta = "SELECT * FROM mdl_local_resource_repository WHERE $filtro = $busqueda ORDER BY $filtro ASC ";
        }
        else{
            $consulta = "SELECT * FROM mdl_local_resource_repository WHERE $filtro LIKE '%$busqueda%' ORDER BY $filtro ASC ";
        }
        $result = $mysqli->query($consulta);
        $dataSend = '';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $imageTecnica = $row['resource_nombre_img'];
                $nameTecnica = $row['resource_tecnica'];
                $numTecnica = $row['resource_no_tecnica'];
                $nivelTecnica = $row['resource_nivel'];
                $colorTecnica = $colorsNivel[$nivelTecnica];
                $dataSend = $dataSend. "<div class='form-card row' style='background: ".$colorTecnica."'>
                    <div class='col-md-8 container-info'>
                    <p class='p-form-card'>".$nameTecnica."</p>
                    <input type='hidden' name='numeroDeTecnica' id='numeroDeTecnica' value='".$numTecnica."'>
                    <button class='button-form-card' style='background: ".$colorTecnica."' >
                        <a href='".$urlPdfGenerator.$numTecnica."' target='_blank' class ='btn color-white'>
                            <i class='fa fa-download' aria-hidden='true'></i>
                            <span>Ver PDF</span>
                        </a>
                    </button>
                    </div>
                    <div class='col-md-4 img-container-form-card'>
                        <p class='extra-card-info'>".$numTecnica."</p>
                        <img src='".$urlImage.$imageTecnica."?v=1.0'>
                        <p class='extra-card-info'>".$nivelTecnica."</p>
                    </div>
                    </div>";
            }
            //Creación de log de bitácoras
            if($filtro != '' && $busqueda != '' ){
                global $USER;
                $filtros = ['resource_tecnica'=>'Nombre de técnica','resource_type_resource'=>'Nivel taxonómico básico','resource_no_tecnica'=>'Número de técnica','resource_keywords'=>'Palabras clave'];
                $couserid = $_POST['courseid'];
                $event =  \block_normateca_unadm\event\consult_normateca_unadm::create(array(
                'context' => context_course::instance($couserid),
                'other' => array('filtro'=>$filtros[$filtro],'busqueda'=>$busqueda,
                ),
                'userid'  => $USER->id,
            ));
            $event->trigger();
            }
            echo $dataSend;
        } else {
            printNotData();
        }
    }
}else{
    printNotData();
}
function printNotData(){
    echo '<div class="wrapper centrar-contenido">';
    echo '<p class="searchNotValue">No se han encontrado resultados para tu búsqueda.</p>';
    echo '</div>';
}
