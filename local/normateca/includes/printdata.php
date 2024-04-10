<?php
require_once(__DIR__ . '/../../../config.php');
global $DB;
$notresults =0;
//print_object($_POST);

$curso = $_POST['courseid'];
//Optener la categoría de ese curso
$consultCategory = $DB->get_record('course',array('id'=>$curso));
$idCategory = $consultCategory->category;
$idCategory = get_config("local_normateca","categoryiddb");
$filtro = $_POST['filtro'];
$busqueda = $_POST['busqueda'];
$busquedaAvanzada = $_POST['filtroavanzado'];
$busquedaAvanzadaCondicional = $_POST['filtroavanzadocondition'];
$busquedaAvzandaSearch = $_POST['filtroavanzadotext'];
$extraBusqueda = '';
if($busquedaAvanzada != ''){
    $extraBusqueda = 'AND'.' resource_'.$busquedaAvanzada.' '.$busquedaAvanzadaCondicional;
    if($busquedaAvanzadaCondicional == 'LIKE' || $busquedaAvanzadaCondicional == 'NOT LIKE' ){
        $extraBusquedaConditional= '"%'.$busquedaAvzandaSearch.'%"';
        $extraBusqueda = $extraBusqueda.' '.$extraBusquedaConditional;
    }else{
        $extraBusqueda = $extraBusqueda.'"'.$busquedaAvzandaSearch.'"';
    }
}
//echo "el valor del filtro es:".$filtro."<br>";
//echo "el valor de la busqueda es".$busqueda."<br>";
if($filtro == "todo" || $filtro == "clearSearch"){
    $filtro = '';
}
//$curso =4;
$consulta = 'SELECT * FROM mdl_local_normateca';
$consulta = $DB->get_records_sql($consulta);

//obteniendo los diferentes tipos de recursos
$consultaTipos = 'SELECT resource_2type_resource FROM mdl_local_normateca GROUP BY resource_2type_resource';
$consultaTipos = $DB->get_records_sql($consultaTipos);
$consultaTipos = array_keys($consultaTipos);

if(sizeof($consulta)==0){
    $notresults = 1;
}
//foreach ($consultaTipos as $item){
//    if($_POST[$item]==$filtro){
//        $filtro = $item;
//        $muestradata = 1;
//        break;
//    }
//}
($filtro != '')?$like=1:$like=0;
//echo $filtro;
if($busqueda != ''){
    if($like == 1) {
//        echo "busqueda con filtro";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_course = '.$curso.' AND resource_category = '.$idCategory.'  AND resource_2type_resource = "'.$filtro.'" AND (resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%")';
    }else{
//        echo "busqueda sin filtro";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_course = '.$curso.' AND resource_category = '.$idCategory.' AND (resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%")';
    }
    $consulta = $DB->get_records_sql($consulta);
    if(sizeof($consulta)==0){
        $notresults = 1;
    }
}else{
    if($filtro != ''){
//        echo "busqueda con filtro sin busqueda";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_course = '.$curso.' AND resource_category = '.$idCategory.' AND resource_2type_resource LIKE "%'.$filtro.'%" '.$extraBusqueda.'';
//        echo $consulta;
    }else{
//        echo "busqueda global busqueda";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_course = '.$curso.' AND resource_category = '.$idCategory.' AND  (resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%") '.$extraBusqueda.'';
//        echo $consulta;
    }
    $consulta = $DB->get_records_sql($consulta);
    if(sizeof($consulta)==0){
        $notresults = 1;
    }

}
$countL = 0;
$countL2 = 0;
$countLInit = 0;
function searchBar($name='',$notresult=0){
    $textL = $textL . '
                
               <div style="align-content: center; text-align: center; padding-bottom: 40px; padding-top: 40px">
                    <form id="recursos" onsubmit="return preventSubmitForm()" action="#" method="post" enctype="multipart/form-data">
                           <div style="margin-left: auto;margin-right: auto;margin-bottom: 10px;">
                                <label><input  onclick="ValidateToSubmit(this)" type="checkbox" id="todo" name="todo"><span class="label">Todos</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="recordar" name="recordar"><span class="label l1">Recordar</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox"  id="explicar" name="explicar"><span class="label l2">Explicar</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="aplicar" name="aplicar"><span class="label l3">Aplicar</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="analizar" name="analizar"><span class="label l4">Analizar</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="sintetizar" name="sintetizar"><span class="label l5">Sintetizar</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="construir" name="construir"><span class="label l6">Construir</span></label>
                           </div>
                           <div class="row col-12">
                                <div class="col-md-6">
                                <select  class="form-select  seleccionar" name="filtro-avanzado" id="filtro-avanzado">
                                <option value="">Filtrar por</option>
                                <option value="tecnica">Nombre de la técnica</option>
                                <option value="type">Resumen</option>
                                <option value="que_es">¿Qué es?</option>
                                <option value="estructura">Estructura</option>
                                <option value="utilidad">¿Cuál es su utilidad?</option>
                                <option value="construye">¿Cómo se construye?</option>
                                <option value="tomar_cuenta">Para tomar en cuenta…</option>
                                <option value="autores_dicen">Los autores dicen…</option>
                                 </select>
                            </div>
                                <div class="col-md-6">
                                 <select class="form-select  seleccionar" name="filtro-avanzado-two" id="filtro-avanzado-two">
                                <option value="">Seleccionar</option>
                                <option value="=">igual a:</option>
                                <option value="LIKE">contine:</option>
                                <option value="NOT LIKE">no contine:</option>
                                <option value="!=">no igual a:</option>
                                 </select>
                         </div>
                            </div>
                         <input class="llenar" name="filtro-avanzado-text" id="filtro-avanzado-text" placeholder="Escribe el texto que deseas filtrar..." >
                                 <button title="Eliminar filtro" id="clearSearch"  type="button" onclick="ValidateToSubmit(this)" class="button"><i class="fa fa-trash" aria-hidden="true"></i></button><br>
                         <input class="llenar" name="busqueda" id="busqueda" placeholder="Buscar" aria-label="Buscar">
                         <button title="Realizar busqueda" id="searchbutton"   type="button" onclick="ValidateToSubmit(this)" class="button"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <div class="clear-fix"></div>
               <div class="carousel-inner">
                <div class="carousel-item active">
                <div class="row row-center">
                ';
    return $textL;
}

if($notresults == 1) {
        $textL = $textL.searchBar("Recurso",$notresult);
        $textL = $textL.' <h1>Sin datos que mostrar...</h1>
              </div>';
        $muestradata=1;
}
foreach ($consulta as $item) {
        
            if($countLInit == 0) {
                if(sizeof($_POST)== 0 || $_POST['todo'] == 'on'){
                    $textL = $textL.searchBar("Recurso");
                }else{
                    $textL = $textL.searchBar($item->resource_2type_resource);
                }
                $countLInit++;
            }
        $countL2++;
        $countL++;

//    $html = $html . "<tr>";
        foreach ($item as $data) {
            $nombreCurso = $DB->get_record('course', array('id' => $item->resource_course), 'fullname');
            $nameCourse = str_replace(" ", "%20", $nombreCurso->fullname);
            $nameImage = str_replace(" ", "%20", $item->resource_tecnica);
            $nameCarpet = str_replace(" ", "%20", $item->resource_2type_resource); 
            $nameImage = str_replace("?", "",  $nameImage);
            $nameImage = str_replace("¿", "",  $nameImage);
            $nameImage = str_replace("/", "",  $nameImage);
            $nameImage = str_replace("*", "",  $nameImage);
            $nameImage = str_replace("|", "",  $nameImage);
            $nameImage = str_replace("<", "",  $nameImage);
            $nameImage = str_replace(">", "",  $nameImage);
            $nameImage = str_replace(":", "",  $nameImage);
            $nameImage = str_replace('"', "",  $nameImage);
            $urlImage = "local/normateca/img/$nameCourse/$nameCarpet/$nameImage" . ".png";
//        echo $item->resource_no_tecnica;
            $textL = $textL . '<div class="col-xs-6 col-sm-3 item-de-carousel">
                        <div class="thumb-wrapper2">
                            <div class="img-box2">
                                <a onclick="generateModal(this)"  data="' . $item->resource_no_tecnica . '" data-emb="no">
                                    <img src="' . $urlImage . '">
                                </a>
                            </div>
                        </div>
                        <div class="thumb-content">
                            <p class="project-name2 text-center">' . $item->resource_tecnica . '</p>
                        </div>
                    </div>
                    ';
            break;
//        $html = $html . "<td>" . $data . "</td>";
        }
        if ($countL == 4) {
            $countL = 0;
            $textL = $textL . '</div></div>
        <div class="carousel-item">
                <div class="row row-center">
        ';
        }
        if ($countL2 == sizeof($consulta)) {
            $textL = $textL . '
         </div>
            </div>
            </div>
        ';
            $muestradata = 1;
        }
}
?>
<style>
    .clear-fix{
        clear: both;
        float: none;
    }
    input[type=checkbox] {
        display: none;
    }
    .label {
        /*border: 1px solid #000;*/
        display: inline-block;
        padding: 12px;
        background: #545454;
        width: 7rem;
        color: white;
        margin-top: -15px;
        margin: 15px 0px;
        /* background: url("unchecked.png") no-repeat left center; */
        /* padding-left: 15px; */
    }
    .l1{
        background: #ab9ac9; 
    }
    .l2{
        background: #ecba94;
    }
    .l3{
        background: #aed094;
    }
    .l4{
        background: #ecba94;
    }
    .l5{
        background: #9cbce1;
    }
    .l6{
        background: #f2967c;
    }
    
    input[type=checkbox]:checked + .label {
        color: #fff;
        background: linear-gradient(90deg, rgba(145,32,62,1) 0%, rgba(240,52,8,1) 100%);
        border: none;
        width: 8rem;
    }
    /* Next & previous buttons */
    .prev,
    .next {
        cursor: pointer;
        position: absolute;
        top: 40%;
        width: auto;
        padding: 16px;
        margin-top: -50px;
        color: white;
        font-weight: bold;
        font-size: 20px;
        border-radius: 0 3px 3px 0;
        user-select: none;
        -webkit-user-select: none;
        background-color: #97213b;
        
    }

    /* Position the "next button" to the right */
    .next {
        right: 0;
        border-radius: 3px 0 0 3px;
    }

    /* On hover, add a black background color with a little bit see-through */
    .prev:hover,
    .next:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    .button {
        padding: 10px 15px;
        font-size: 24px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #bc955c;
        border: none;
        border-radius: 15px;
    }
.seleccionar{
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-top: 6px;
    margin-bottom: 16px;
    resize: vertical;
}
.llenar{
    width: 20%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-top: 6px;
    margin-bottom: 16px;
    resize: vertical;
}
</style>
<?php 
if($muestradata == 1){?>
    
<section class="page-section3" style="display: block;">
    <div id="myCarousel2" class="carousel slide" data-ride="carousel" data-interval="0">
        <!-- Carousel indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel2" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel2" data-slide-to="1"></li>
            <li data-target="#myCarousel2" data-slide-to="2"></li>
            <li data-target="#myCarousel2" data-slide-to="3"></li>
            <li data-target="#myCarousel2" data-slide-to="4"></li>
        </ol>
        <div id="myCarousel2" class="carousel slide" data-ride="carousel" data-interval="0">
            <!-- Carousel indicators -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel2" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel2" data-slide-to="1" class=""></li>
                <li data-target="#myCarousel2" data-slide-to="2"></li>
                <li data-target="#myCarousel2" data-slide-to="3"></li>
                <li data-target="#myCarousel2" data-slide-to="4"></li>
            </ol>

            <!-- Wrapper for carousel items -->
            <?php
            echo $textL;
            ?>
            <!-- Carousel controls -->
            <a class="prev" href="#myCarousel2" data-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="next" href="#myCarousel2" data-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
</section>

    <?php    
}

?>
                
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
<script src="assets/js/scripts.js?=1"></script>
<script src="assets/js/startbootstrap.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<link rel="stylesheet" type="text/css" href="local/normateca/src/js/alertifyjs/css/alertify.min.css">
<link rel="stylesheet" type="text/css" href="local/normateca/src/js/alertifyjs/css/themes/default.min.css">

<?php
//}