<?php

/**
 * @package  normateca
 * @copyright 2023, Luis Felipe Alcocer  <luisfelipealcocersosa@gmail.com>
 */

require_once(__DIR__.'/../../config.php');
global $CFG,$OUTPUT;
$PAGE->set_url(new moodle_url('/local/normateca/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Repositorio de recursos');
//CUANDO EL USUARIO ESTA LOGEADO.
$url = $CFG->wwwroot.'/index.php';
require_login();
$course =enrol_get_all_users_courses($USER->id,true);
//categoria para subir los recursos
$idCategory = get_config("local_normateca","categoryiddb");
//Roles que pueden acceder al módulo
$rolesAcept = get_config("local_normateca","roleshowmdoule");
if($idCategory == ''){
    print_error("campo de id´s de roles se encuentra vacío, revisar la configuración del pluggin.");
}
//válida que el id de la category este configurado
if($idCategory != ''){
    $consultacategoria = $DB->get_record('course_categories',array('id'=>$idCategory));
    if(!$consultacategoria){
        print_error("El id de la categoría configurado no es valido, ingresa un id que exista, revisar la configuración del pluggin.");
    }
}
$iduserallPrivilegios = get_config("local_normateca","idusersallprivilegios");
$noUserAdmins =0;
if($iduserallPrivilegios!= ''){
    $noUserAdmins = 1;
}
$iduserallPrivilegios = explode('|',$iduserallPrivilegios);

foreach ($iduserallPrivilegios as $iduser){
    if($iduser == $USER->id){
        $iduserallPrivilegios = $USER->id;
        $noUserAdmins =2;
        break;
    }
}

$rolesAcept = explode('|',$rolesAcept);
// Comprueba si el usuario actual tiene el rol de estudiante
foreach ($course as $item){
    $viewContentCourse = 0;
//    $modinfo = get_fast_modinfo($item->id); //cuando se requiere tomar las instancias específicas de ese curso
    if(get_config("local_normateca","aceptvisiblecourse") == 0){
        if($item->visible == get_config("local_normateca","aceptvisiblecourse")) {
            $cContext = context_course::instance($item->id); // global $COURSE
            $currenRole = current(get_user_roles($cContext, $USER->id));
            foreach ($rolesAcept as $roleid) {
                if ($currenRole->roleid == $roleid) {
                    $viewContentCourse = 1;
                    $courseid = $item->id;
                    break;
                }
            }
            if ($viewContentCourse) {
                break;
            }
        }
    }else{
        if($item->visible == get_config("local_normateca","aceptvisiblecourse")) {
            $cContext = context_course::instance($item->id); // global $COURSE
            $currenRole = current(get_user_roles($cContext, $USER->id));
            foreach ($rolesAcept as $roleid) {
                if ($currenRole->roleid == $roleid) {
                    $viewContentCourse = 1;
                    $courseid = $item->id;
                    break;
                }
            }
            if ($viewContentCourse) {
                break;
            }
        }
    }
    
}
//Válida que solo sean los roles que están en la configuración
if(!$viewContentCourse AND !is_siteadmin()){
    print_error("No tienes acceso para visualizar este contenido.");
}
$iduser = $USER->id;
$isadmin = 0;

if($noUserAdmins == 2 || is_siteadmin()){
    $isadmin = 1 ;
}
echo $OUTPUT->header();

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">-->
        <script src="src/js/jquery/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="src/css/style.css?v=1">
        <link rel="stylesheet" type="text/css" href="src/js/alertifyjs/css/alertify.min.css">
        <link rel="stylesheet" type="text/css" href="src/js/alertifyjs/css/themes/default.min.css">
    </head>
    <body>
    <style>
        .info-text{
            color:white;
            text-align: center;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: -4%;
            margin-left: 9rem ;
            background-color: #2B333F;
        }
        .view-of{
            display: none;
        }
        .view-on{
            display: block;
        }
        .info-icon{
            background-color: antiquewhite;
            padding: 6px;
            border-radius: 50%;
        }
    </style>
    <script>
        function activaMenu(){
            document.getElementById('agregartecnica').style.display = 'block';
        }
    </script>
    <div class="container col-md-6" >
        <form id="uploadForm" class="form-resource" method="GET" onsubmit="return alerType('error')" enctype="multipart/form-data">
            <h1>Técnicas</h1>
            <p>Selecciona una opción antes de continuar:</p>
            <div style="text-align: initial ">
                <!--<a href="importar.php">Importación masiva</a><br>
                <a href="cargarImagenes.php">Importación masiva de imágenes</a><br>-->
                <?php 
                 if($isadmin){
                    echo '<a href="busqueda.php">Editar técnica existente</a><br>';
//                    echo '<a style="cursor: not-allowed" >Agregar nueva técnica</a>';
                 }
                ?>
            </div><br>
            <div style="display: none" id="agregartecnica">
                <p>Imagen: <span class="text-red">*</span></p>
                <div class="custom-file">
                    <input class="custom-file-input" type="file" accept="image/png" id="imageFile" name="imageFile">
                    <label class="custom-file-label" for="inputGroupFile01">Cargar imagen</label>
                </div>
                <br>
                <br>
                <div class="form-group">
                    <!--<div name="lenguaje" class="info-text view-of">
                        La lengua en la que está escrito el recurso. Si es más de una, poner todas las lenguas separadas por comas.
                    </div>-->
                    <label for="">Nombre de la imagen:<span class="text-red">*</span></label>&nbsp;<i id="lenguaje" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <input  class="form-control" name="resource_nombre_img" id="resource_nombre_img">
                </div>
                <div class="form-group">
                    <label for="">Número de técnica:<span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="resource_no_tecnica" id="resource_no_tecnica">
                </div>
                <div class="form-group">
                    <label for="">Nivel:<span class="text-red">*</span></label>
                    <select  class="form-control" name="resource_nivel" id="resource_nivel">
                        <option value=0>Selecciona un nivel</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </div>
                <div class="form-group">
                    <!-- <div name="title" class="info-text view-of">
                         Nombre formal por el que se conoce el recurso.
                     </div>-->
                    <label for="">Nombre de la técnica:<span class="text-red">*</span></label>&nbsp;<i id="title" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <input type="text" class="form-control" name="resource_tecnica" id="resource_tecnica">
                </div>
                <div class="form-group">
                    <!--<div name="tipo" class="info-text view-of">
                        Escoger el que mejor se adecue al recurso en cuestión.
                    </div>-->
                    <label for="">Resumen:<span class="text-red">*</span></label>&nbsp;<i id="tipo" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <!--                <textarea class="form-control" name="resource_resumen" id="resource_resumen"></textarea>-->
                    <textarea type="text" class="form-control" name="resource_resumen" id="resource_resumen"></textarea>
                </div>
                <div class="form-group">
                    <!--<div name="autores" class="info-text view-of">
                        Creadores intelectuales de la obra (persona, organización o servicio).
                    </div>-->
                    <label for="">¿Qué es?:<span class="text-red">*</span></label>&nbsp;<i id="autores" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_que_es" id="resource_que_es"></textarea>
                    <!--                <textarea class="form-control" name="resource_que_es" id="resource_que_es"></textarea>-->
                </div>
                <div class="form-group">
                    <!--<div name="tema" class="info-text view-of">
                        De qué se trata el recurso. Puedes guiarte con el siguiente Tesauro: https://vocabularyserver.com/tee/es/index.php
                    </div>-->
                    <label for="">Estructura:<span class="text-red">*</span></label>&nbsp;<i id="tema" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_estructura" id="resource_estructura"></textarea>
                    <!--                <textarea class="form-control" name="resource_estructura" id="resource_estructura"></textarea>-->
                </div>
                <div class="form-group">
                    <!--<div name="descripcion" class="info-text view-of">
                        Resumen conciso. Aproximadamente 50 palabras o 160 caracteres.
                    </div>-->
                    <label for="">¿Cuál es su utilidad?:<span class="text-red">*</span></label>&nbsp;<i id="descripcion" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_utilidad" id="resource_utilidad"></textarea>
                    <!--                <textarea class="form-control" name="resource_utilidad" id="resource_utilidad"></textarea>-->
                </div>
                <div class="form-group">
                    <!-- <div name="editor" class="info-text view-of">
                         Entidad responsable de que se encuentre disponible el recurso.
                     </div>-->
                    <label for="">¿Cómo se construye?:<span class="text-red">*</span></label>&nbsp;<i id="editor" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_como_construye" id="resource_como_construye"></textarea>
                    <!--                <textarea class="form-control" name="resource_como_construye" id="resource_como_construye"></textarea>-->
                </div>
                <div class="form-group">
                    <!--<div name="colaborador" class="info-text view-of">
                        Pueden ser, por ejemplo, compiladores.
                    </div>-->
                    <label for="">Para tomar en cuenta…:<span class="text-red">*</span></label>&nbsp;<i id="colaborador" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_tomar_cuenta" id="resource_tomar_cuenta"></textarea>
                    <!--                <textarea class="form-control" name="resource_tomar_cuenta" id="resource_tomar_cuenta"></textarea>-->
                </div>
                <div class="form-group">
                    <!--<div name="fecha" class="info-text view-of">
                        Año de publicación, Fecha de actualización, etc. Formato: AÑO/MES/DÍA
                    </div>-->
                    <label for="">Los autores dicen…:<span class="text-red">*</span></label>&nbsp;<i id="fecha" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_autores_dicen" id="resource_autores_dicen"></textarea>
                    <!--                <textarea class="form-control" name="resource_autores_dicen" id="resource_autores_dicen"></textarea>-->
                </div>
                <div class="form-group">
                    <!--<div name="fuente" class="info-text view-of">
                        Si se hace referencia a un capítulo de una obra, se debe poner la fuente de la que se extrae. Ref. Bibliográfica.
                    </div>-->
                    <label for="">Referencias:<span class="text-red">*</span></label>&nbsp;<i id="fuente" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_referencias" id="resource_referencias"></textarea>
                    <!--                <textarea class="form-control" name="resource_referencias" id="resource_referencias"></textarea>-->
                </div>
                <div class="form-group">
                    <div name="palabras_clave" class="info-text view-of">
                        Describe de forma corta contenidos y temas. Se escriben separadas por comas.
                    </div>
                    <label for="">Palabras clave:<span class="text-red">*</span></label>&nbsp;<i id="palabras_clave" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                    <textarea  class="form-control" name="resource_keywords" id="resource_keywords"></textarea>
                    <!--                <textarea class="form-control" name="resource_keywords" id="resource_keywords"></textarea>-->
                </div>
                <div class="form-group">
                    <label for="">Tipo de Sección(Buscador):<span class="text-red">*</span></label>
                    <select  class="form-control" name="resource_2type_resource" id="resource_2type_resource">
                        <option value=0>Selecciona la categorización</option>
                        <option value="recordar">Recordar</option>
                        <option value="explicar">Explicar</option>
                        <option value="aplicar">Aplicar</option>
                        <option value="analizar">Analizar</option>
                        <option value="sintetizar">Sintetizar</option>
                        <option value="construir">Construir</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Categoría Moodle:<span class="text-red">*</span></label>
                    <select  class="form-control" name="resource_category" id="resource_category"></select>
                </div>
                <div class="form-group">
                    <label for="">Curso al que pertenece el recurso</label>
                    <select  class="form-control" name="resource_course" id="resource_course"></select>
                </div>
                <div class="form-group">
                    <input type="hidden"  class="form-control" name="numInputs" id="numInputs">
                </div>
                <div class="form-group">
                    <button onclick="alerType()" type="button" class="btn btn-primary btn-sm" name="btnSubmit" id="btnSubmit">Enviar</button>
                </div>
            </div>
            
        </form>
        <div id="preview"></div>
    </div>
    <script>
        function actualizarSeccion() {
            // Crear una nueva instancia de XMLHttpRequest
            // var xhttp = new XMLHttpRequest();
            // // Definir la función de devolución de llamada para manejar la respuesta del servidor
            // xhttp.onreadystatechange = function() {
            //     if (this.readyState == 4 && this.status == 200) {
            //         // Actualizar el contenido de la sección con la respuesta del servidor
            //         document.getElementById("mi-seccion").innerHTML = this.responseText;
            //     }
            // };
            // // Enviar la solicitud al servidor
            // xhttp.open("GET", "includes/getData.php", true);
            // xhttp.send();
        }
    </script>


<!--    <div class="container table-responsive">-->
<!--        <table class="table table-hover" id="tablaPelicula">-->
<!--            <thead>-->
<!--            <tr>-->
<!--                <th>Id</th>-->
<!--                <th>Nombre de imagen</th>-->
<!--                <th>Numero de técnica</th>-->
<!--                <th>Nivel</th>-->
<!--                <th>Nombre de la técnica</th>-->
<!--                <th>Resumen</th>-->
<!--                <th>¿Qué es?</th>-->
<!--                <th>Estructura</th>-->
<!--                <th>¿Cuál es su utilidad?</th>-->
<!--                <th>¿Cómo se construye?</th>-->
<!--                <th>Para tomar en cuenta…</th>-->
<!--                <th>Los autores dicen…</th>-->
<!--                <th>Referencias</th>-->
<!--                <th>Palabras clave</th>-->
<!--                <th>Tipo de Sección(Buscador)</th>-->
<!--                <th>Categoría</th>-->
<!--                <th>Curso</th>-->
<!--            </thead>-->
<!--            <tbody id="mi-seccion">-->
<!--            </tbody>-->
<!--        </table>-->
<!---->
<!--    </div>-->
    <script src="src/js/alertifyjs/alertify.js"></script>
    <script>
        $(document).ready(function () {
            $.get("includes/dispach.php", {funcion: "selectCategories"}, function (data) {
                $("#resource_category").html(data);
            });
            /*$.get("includes/paises.php", {}, function (data) {
                $("#resource_coverage").html(data);
            });*/
            $("#resource_category").change(function () {
                id_category = $('#resource_category').val();
                iduser = '<?= $iduser ?>';
                isadmin = '<?= $isadmin ?>';
                // alert(isadmin);
                $.get("includes/dispach.php", {funcion: "selectCourses", id_category : id_category, iduser: iduser, isadmin: isadmin }, function (data) {
                    $("#resource_course").html(data);
                });
            });
            actualizarSeccion();
        });
        function alerType(){
            datos =  document.querySelectorAll('.form-resource > div > .form-control')
            parametros = [];
            numInputCapture = datos.length;
            document.getElementById("numInputs").value = numInputCapture;
            /*for(var i = 0; i <= datos.length-1;  i++){
                datos[i].setAttribute('required',true);
                parametros.push({name: datos[i].name, value: datos[i].value})
            }*/
            var formData = new FormData($('#uploadForm')[0]);
            $.ajax({
                url: 'includes/uploadData.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data){
                    // Manejar la respuesta del servidor
                    $('#preview').html(data);
                    if(data == 'error'){
                        alertify.error('Hubo un problema al realizar el registro.');
                    }
                    if(data == 'success'){
                        alertify.success('Registro realizado con éxito.');
                        for(var i = 0; i <= datos.length-1;  i++){
                            datos[i].value = "";
                        }
                        actualizarSeccion();
                    }
                    if(data == 'missing_data'){
                        alertify.warning('Falta información para realizar el registro.');
                    }
                    if(data == 'exist_data'){
                        alertify.notify('El registro ya existe.');
                    }
                    if(data == 'success_but_not_upload_image'){
                        alertify.notify('Registro realizado con éxito. Pero la imagen no se cargo correctamente');
                    } 
                    if(data == 'success_but_upload_image_exist'){
                        alertify.notify('Registro realizado con éxito. Pero la imagen ya existe');
                    }
                    if(data == 'image_not_correct_name'){
                        alertify.warning('El nombre de la imagen debe ser el mismo que el título');
                    }
                }
            });

        }
        function viewContent(dato){
            document.getElementsByName(dato.id)[0].classList.remove('view-of');
            setTimeout(()=>{
                document.getElementsByName(dato.id)[0].classList.add('view-of');
            },1500);
        }

    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/18.0.0/classic/ckeditor.js"></script>
    <!--    <script src="interfaz.js"></script>-->
    <!--    <script src="logica.js"></script>-->
    <!--<script>
        ClassicEditor
            .create( document.getElementById( 'resource_resumen' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_que_es' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_estructura' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_utilidad' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_como_construye' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_tomar_cuenta' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_autores_dicen' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_referencias' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>-->
    </body>
    </html>
<?php
echo $OUTPUT->footer();