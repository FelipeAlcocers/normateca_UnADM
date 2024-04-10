<?php

/**
 * @package  normateca
 * @copyright 2023, Luis Felipe Alcocer  <luisfelipealcocersosa@gmail.com>
 */

require_once(__DIR__.'/../../config.php');
global $CFG,$OUTPUT;
$PAGE->set_url(new moodle_url('/local/normateca/editar.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Editor de repositorio de recursos');
//CUANDO EL USUARIO ESTA LOGEADO.
$url = $CFG->wwwroot.'/index.php';
require_login();
$course =enrol_get_all_users_courses($USER->id,true);
//categoria para subir los recursos
$idCategory = get_config("local_normateca","categoryiddb");
//Roles que pueden acceder al módulo
$rolesAcept = get_config("local_normateca","roleshowmdoule");
if($idCategory == ''){
    print_error("El campo de id´s de roles se encuentra vacío, revisar la configuración del pluggin.");
}
//válida que el id de la category este configurado
if($idCategory != ''){
    $consultacategoria = $DB->get_record('course_categories',array('id'=>$idCategory));
    if(!$consultacategoria){
        print_error("El id de la categoría configurado no es valido, ingresa un id que exista, revisar la configuración del pluggin.");
    }
}

$rolesAcept = explode('|',$rolesAcept);

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

$idrecurso = optional_param('id',0,PARAM_INT);
if($idrecurso == 0){
    print_error('Se requiere del parámetro id para editar un recurso.');
}else{
    $validateID = $DB->get_record('local_normateca',array('id'=>$idrecurso));
    if(!$validateID){
        print_error('El recurso que intentas modificar no existe.');
    }
}
if($validateID){
//    print_object($validateID);
    $url = $validateID->resource_no_tecnica;
    $titulo = $validateID->resource_tecnica;
    $autor = $validateID->resource_que_es;
    $tema = $validateID->resource_estructura;
    $palabrasclaves = $validateID->resource_keywords;
    $descipcion = $validateID->resource_utilidad;
    $editor = $validateID->resource_como_construye;
    $colaborador = $validateID->resource_tomar_cuenta;
    $fecha = $validateID->resource_autores_dicen;
    $tipo = $validateID->resource_resumen;
    $formato =$validateID->resource_nivel;
//    (strstr($formato,'web'))?$formato = str_replace(' ','_',$formato):$formato =$validateID->resource_nivel;
    $tipodeRecursoBusqueda = $validateID->resource_type_resource;
    $fuente = $validateID->resource_referencias;
    $comocitar = $validateID->resource_comocitar;
    $lenguaje = $validateID->resource_nombre_img;
//    $nombrePDF = $validateID->resource_nombre_pdf;
    $nombrePDF = $validateID->resource_nombre_pdf;
//    $nombrePdfBn = $validateID->resource_nombre_pdf_bn;
    $nombrePdfBn = explode('.pdf',$nombrePDF);
    $nombrePdfBn  = $nombrePdfBn[0].'_bn.pdf';
    $categoria = $validateID->resource_category;
    $curso = $validateID->resource_course;
}

$iduserallPrivilegios = get_config("local_normateca","idusersallprivilegios");
$iduserallPrivilegios = explode('|',$iduserallPrivilegios);

foreach ($iduserallPrivilegios as $iduser){
    if($iduser == $USER->id){
        $iduserallPrivilegios = $USER->id;
        $noUserAdmins =2;
        break;
    }
}

if($noUserAdmins == 2 || is_siteadmin()){
    $isadmin = 1 ;
}
if(!$isadmin){
    $destination = 'index.php';
    $message = "No tienes acceso para visualizar este contenido.";
    redirect($destination, $message, 15, \core\output\notification::NOTIFY_ERROR);
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
    <div class="container col-md-10" >
        <div class="col-auto">
            <a href="index.php" class='btn btn-secondary btn-sm'>Regresar</a>
        </div>
        <form id="uploadForm" class="form-resource" method="GET" onsubmit="return alerType('error')" enctype="multipart/form-data">
            <div style="text-align: center;font-size: 25px;">Editor de técnicas</div>
            <p style="padding-bottom: 15px;padding-top: 15px;">Editando la técnica: <b><?= $titulo?></b> </p>
            <p>Los campos marcados con <span class="text-red">*</span> son obligatorios.</p>
            <div class="form-group">
                <div name="lenguaje" class="info-text view-of">
                    La lengua en la que está escrito el recurso. Si es más de una, poner todas las lenguas separadas por comas.
                </div>
                <label for="">Nombre de la imagen:<span class="text-red">*</span></label>
                <input value="<?= $lenguaje ?>"  type="text" class="form-control" name="resource_nombre_img" id="resource_nombre_img">
            </div>
            <p><b>Imagen</b>:</p>
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input accept="image/svg+xml" class="custom-file-input" type="file" d="imageFile" name="imageFile">
                    <label class="custom-file-label" for="inputGroupFile02">Seleccionar imagen</label>
                </div>
            </div>
            <!--            <input type="file" accept="image/svg+xml" id="imageFile" name="imageFile"><br>-->
            <!--            PDF color -->
            <div class="form-group">
                <br>
                <label for="">Nombre del PDF:<span class="text-red">*</span></label>
                <input value="<?= $nombrePDF ?>"  type="text" class="form-control" name="resource_nombre_pdf" id="resource_nombre_pdf">
            </div>
            <!--            PDF blanco y negro -->
            <div class="form-group">
                <label for="">Nombre del PDF blanco y negro:<span class="text-red">*</span></label>
                <input readonly value="<?= $nombrePdfBn ?>"  type="text" class="form-control" name="resource_nombre_pdf_bn" id="resource_nombre_pdf_bn">
            </div>
            <p style="margin-bottom: 10px" for="">Adjuntar el o los PDF</p>
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input accept="application/pdf" class="custom-file-input" type="file" name="pdfs[]" multiple="multiple">
                    <label class="custom-file-label" for="inputGroupFile02">Seleccionar PDF</label>
                </div>
            </div>
            <!--            Id-->
            <div style="display:none" class="form-group"><br>
                <label for="">Id:<span class="text-red">*</span></label>
                <input value="<?= $idrecurso ?>" readonly type="text" class="form-control" name="id_resource" id="id_resource">
            </div>


            <div class="form-group">
                <label for="">Número de técnica:<span class="text-red">*</span></label>
                <input value="<?= $url ?>" type="text" class="form-control" name="resource_no_tecnica" id="resource_no_tecnica">
            </div>
            <div class="form-group">
                <label for="">Nivel:<span class="text-red">*</span></label>
                <select  class="form-control" name="resource_nivel" id="resource_nivel">
                    <option value=0>Selecciona un formato</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div class="form-group">
                <div name="title" class="info-text view-of">
                    Nombre formal por el que se conoce el recurso.
                </div>
                <label for="">Nombre de la técnica:<span class="text-red">*</span></label>&nbsp;<i id="title" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <input value="<?= $titulo ?>" type="text" class="form-control" name="resource_tecnica" id="resource_tecnica">
            </div>
            <div class="form-group">
                <label for="">Resumen:<span class="text-red">*</span></label>&nbsp;<i id="tipo" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_resumen" id="resource_resumen"><?= $tipo ?></textarea>
            </div>
            <div class="form-group">
                <div name="autores" class="info-text view-of">
                    Creadores intelectuales de la obra (persona, organización o servicio).
                </div>
                <label for="">¿Qué es?:<span class="text-red">*</span></label>&nbsp;<i id="autores" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_que_es" id="resource_que_es"><?= $autor ?></textarea>
            </div>
            <div class="form-group">
                <div name="tema" class="info-text view-of">
                    De qué se trata el recurso. Puedes guiarte con el siguiente Tesauro: https://vocabularyserver.com/tee/es/index.php
                </div>
                <label for="">¿Cuál es su estructura?:<span class="text-red">*</span></label>&nbsp;<i id="tema" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_estructura" id="resource_estructura"><?= $tema ?></textarea>
            </div>
            <div class="form-group">
                <div name="descripcion" class="info-text view-of">
                    Resumen conciso. Aproximadamente 50 palabras o 160 caracteres.
                </div>
                <label for="">¿Cuál es su utilidad?:<span class="text-red">*</span></label>&nbsp;<i id="descripcion" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_utilidad" id="resource_utilidad"><?= $descipcion?></textarea>
            </div>
            <div class="form-group">
                <div name="editor" class="info-text view-of">
                    Entidad responsable de que se encuentre disponible el recurso.
                </div>
                <label for="">¿Cómo se construye?:<span class="text-red">*</span></label>&nbsp;<i id="como_construye" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_como_construye" id="resource_como_construye"><?= $editor ?></textarea>
            </div>
            <div class="form-group">
                <div name="colaborador" class="info-text view-of">
                    Pueden ser, por ejemplo, compiladores.
                </div>
                <label for="">Para tomar en cuenta…:<span class="text-red">*</span></label>&nbsp;<i id="colaborador" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea class="form-control" name="resource_tomar_cuenta" id="resource_tomar_cuenta"><?= $colaborador ?></textarea>
            </div>
            <div class="form-group">
                <div name="fecha" class="info-text view-of">
                    Año de publicación, Fecha de actualización, etc. Formato: AÑO/MES/DÍA
                </div>
                <label for="">Los autores dicen…:<span class="text-red">*</span></label>&nbsp;<i id="fecha" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_autores_dicen" id="resource_autores_dicen"><?= $fecha ?></textarea>
            </div>
            <div class="form-group">
                <div name="fuente" class="info-text view-of">
                    Si se hace referencia a un capítulo de una obra, se debe poner la fuente de la que se extrae. Ref. Bibliográfica.
                </div>
                <label for="">Referencias:<span class="text-red">*</span></label>&nbsp;<i id="fuente" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea class="form-control" name="resource_referencias" id="resource_referencias"><?= $fuente ?></textarea>
            </div>
            <div class="form-group">
                <div name="fuente" class="info-text view-of">
                    Si se hace referencia a un capítulo de una obra, se debe poner la fuente de la que se extrae. Ref. Bibliográfica.
                </div>
                <label for="">¿Como citar?:<span class="text-red">*</span></label>&nbsp;<i id="fuente" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea class="form-control" name="resource_comocitar" id="resource_comocitar"><?= $comocitar ?></textarea>
            </div>
            <div class="form-group">
                <div name="palabras_clave" class="info-text view-of">
                    Describe de forma corta contenidos y temas. Se escriben separadas por comas.
                </div>
                <label for="">Palabras clave:<span class="text-red">*</span></label>&nbsp;<i id="palabras_clave" onmouseover="viewContent(this)"  class="fa fa-info color-blue info-icon"></i>
                <textarea  class="form-control" name="resource_keywords" id="resource_keywords"><?= $palabrasclaves ?></textarea>
            </div>
            <div class="form-group">
                <label for="">Tipo de Sección(Buscador):<span class="text-red">*</span></label>
                <select  class="form-control" name="resource_type_resource" id="resource_type_resource">
                    <option value=0>Selecciona la categorización</option>
                    <option value="recordar">Recordar</option>
                    <option value="explicar">Explicar</option>
                    <option value="aplicar">Aplicar</option>
                    <option value="analizar">Analizar</option>
                    <option value="sintetizar">Sintetizar</option>
                    <option value="construir">Construir</option>
                </select>
            </div>
            <!-- <div class="form-group">
                 <label for="">Categoría Moodle:<span class="text-red">*</span></label>
                 <select  class="form-control" name="resource_category" id="resource_category"></select>
             </div>
             <div class="form-group">
                 <label for="">Curso al que pertenece el recurso</label>
                 <select  class="form-control" name="resource_course" id="resource_course"></select>
             </div>-->
            <div class="form-group">
                <input type="hidden"  class="form-control" name="numInputs" id="numInputs">
            </div>
            <input type="hidden" value="1"  class="form-control" name="edit" id="edit">


            <div class="form-group">
                <button onclick="alerType()" type="button" class="btn btn-primary btn-sm" name="btnSubmit" id="btnSubmit">Actualizar información</button>
                <a class="btn btn-secondary btn-sm" href="busqueda.php">Cancelar</a>
            </div>
        </form>
        <div id="preview"></div>
    </div>

    <script>
        function actualizarSeccion() {
            // Crear una nueva instancia de XMLHttpRequest
            var xhttp = new XMLHttpRequest();
            // Definir la función de devolución de llamada para manejar la respuesta del servidor
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Actualizar el contenido de la sección con la respuesta del servidor
                    document.getElementById("mi-seccion").innerHTML = this.responseText;
                }
            };
            // Enviar la solicitud al servidor
            xhttp.open("GET", "includes/getData.php", true);
            xhttp.send(datos);
        }
    </script>



    <script src="src/js/alertifyjs/alertify.js"></script>
    <script>
        $(document).ready(function () {
            // $.get("includes/dispach.php", {funcion: "selectCategories"}, function (data) {
            //     $("#resource_category").html(data);
            // });
            /*$.get("includes/paises.php", {}, function (data) {
                $("#resource_coverage").html(data);
            });*/
            document.getElementById('resource_nivel').value = '<?php echo $formato; ?>';
            document.getElementById('resource_type_resource').value = '<?php echo $tipodeRecursoBusqueda; ?>';
            document.getElementById('resource_nombre_img').value = '<?php echo $lenguaje; ?>';
            //setTimeout( function (){
            //    document.getElementById('resource_category').value = '<?php //echo $categoria ; ?>//';
            //},2000);
            //setTimeout( function (){
            //    document.getElementById('resource_course').value = '<?php //echo $curso ; ?>//';
            //},5000);

            // alert($iduser);
            //setTimeout(()=>{
            //    id_category = $('#resource_category').val();
            //    iduser = '<?//= $iduser ?>//';
            //    isadmin = '<?//= $isadmin ?>//';
            //
            //    $.get("includes/dispach.php", {funcion: "selectCourses", id_category : id_category, iduser: iduser, isadmin: isadmin  }, function (data) {
            //        $("#resource_course").html(data);
            //    });
            //},2000)

            $("#resource_nombre_pdf").change(function () {
                console.log('valor cambio');
                $('#resource_nombre_pdf_bn').val($("#resource_nombre_pdf").val());
            });
            //$("#resource_category").change(function () {
            //    id_category = $('#resource_category').val();
            //    iduser = '<?//= $iduser ?>//';
            //    isadmin = '<?//= $isadmin ?>//';
            //    alert(isadmin);
            //    $.get("includes/dispach.php", {funcion: "selectCourses", id_category : id_category, iduser: iduser, isadmin: isadmin }, function (data) {
            //        $("#resource_course").html(data);
            //    });
            //});
            // actualizarSeccion();
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
                    // $('#preview').html(data);
                    if(data == 'error'){
                        alertify.error('Hubo un problema al actualizar el registro.');
                    }
                    if(data == 'success'){
                        alertify.success('Registro actualizado con éxito.');
                        setTimeout(()=>{
                            window.location.href = 'busqueda.php'
                        },2000)
                    }
                    if(data == 'missing_data'){
                        alertify.warning('Falta información para realizar el registro.');
                    }
                    if(data == 'exist_data'){
                        alertify.notify('El registro ya existe.');
                    }
                    if(data == 'success_but_not_upload_image'){
                        alertify.notify('Registro actualizado con éxito. Pero la imagen no se cargo correctamente');
                    }
                    if(data == 'error_name_img'){
                        alertify.notify('La imagen que estas intentando cargar debe tener el mismo nombre que el campo "Nombre de la imagen"');
                    }
                    if(data == 'error_name_pdf'){
                        alertify.notify('El PDF que estas intentando cargar debe tener el nombre del campo "Nombre del PDF" o "Nombre del PDF blanco y negro"');
                    }
                    if(data == 'error_upload_pdf'){
                        alertify.notify('Registro actualizado con éxito. Pero el PDF no se cargo correctamente');
                    }
                    if(data == 'success_but_upload_image_exist'){
                        alertify.success('Registro actualizado con éxito.');
                        setTimeout(()=>{
                            window.location.href = 'busqueda.php'
                        },2000)
                    }
                    if(data == 'image_not_correct_name'){
                        alertify.warning('El nombre de la imagen debe ser el mismo que el título');
                    }
                }
            });

        }
        function viewContent(dato){
            /* document.getElementsByName(dato.id)[0].classList.remove('view-of');
             setTimeout(()=>{
                 document.getElementsByName(dato.id)[0].classList.add('view-of');
             },1500);*/
        }

    </script>
    <!--    <script src="interfaz.js"></script>-->
    <!--    <script src="logica.js"></script>-->
    <!--    <script src="https://cdn.ckeditor.com/ckeditor5/18.0.0/classic/ckeditor.js"></script>-->
    <script src="src/js/ckeditor.js"></script>

    <!--    <script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/decoupled-document/ckeditor.js"></script>-->
    <!--    <script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/superbuild/ckeditor.js"></script>-->
    <style>
        .ck-sticky-panel__content_sticky{
            margin-top: 50px !important;
        }
    </style>
    <script>

        CKEDITOR.ClassicEditor.create(document.getElementById("resource_resumen"), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.getElementById('resource_resumen').value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_que_es' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_que_es' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_estructura' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_estructura' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_utilidad' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_utilidad' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_como_construye' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_como_construye' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_tomar_cuenta' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_tomar_cuenta' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_autores_dicen' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_autores_dicen' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_referencias' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_referencias' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );
        CKEDITOR.ClassicEditor.create( document.querySelector( '#resource_comocitar' ), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Welcome to CKEditor 5!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents'
            ]
        }).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.querySelector( '#resource_comocitar' ).value = datosd;

            } )
        }).catch( error => {
            console.error( error );
        } );

        /*ClassicEditor.create( document.getElementById( 'resource_resumen' )
        ).then(editor =>{
            // Disable the plugin so that no toolbars are visible.

            editor.model.document.on( 'change:data', () => {
                console.log( 'The data has changed!' );
                const datosd= editor.getData();
                console.log( datosd );
                document.getElementById('resource_resumen').value = datosd;

            } )    
        }).catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_que_es' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_que_es').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_estructura' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_estructura').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_utilidad' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_utilidad').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_como_construye' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_como_construye').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_tomar_cuenta' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_tomar_cuenta').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_autores_dicen' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_autores_dicen').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#resource_referencias' ) )
            .then(editor =>{
                editor.model.document.on( 'change:data', () => {
                    console.log( 'The data has changed!' );
                    const datosd= editor.getData();
                    console.log( datosd );
                    document.querySelector('#resource_referencias').value = datosd;

                } )
            })
            .catch( error => {
                console.error( error );
            } );*/
    </script>
    </body>
    </html>
<?php
echo $OUTPUT->footer();