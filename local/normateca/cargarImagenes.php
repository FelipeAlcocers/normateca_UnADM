<?php
require_once(__DIR__.'/../../config.php');
$iduser = $USER->id;
$PAGE->set_url(new moodle_url('/local/normateca/cargarImagenes.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Carga Masiva de Imágenes');
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
    redirect($destination);
}
echo $OUTPUT->header();
?>
<style>
    .custom-file-label::after {
        background-color: #8f959e !important;
        color: white !important;
    }
</style>
<div class="container col-md-6" >
    <h1>Cargar Imagenes para Recursos</h1>
    <form method="post" action="uploadImages.php" enctype="multipart/form-data">
        <hr>
        <p>Selecciona la categoría, curso y tipo de recurso, posteriormente carga el lote de imagenes.</p>
        <div class="form-group">
            <label for="">Categoría Moodle:<span class="text-red">*</span></label>
            <select  class="form-control" name="resource_category" id="resource_category"></select>
        </div>
        <div class="form-group">
            <label for="">Curso al que pertenece el recurso</label>
            <select  class="form-control" name="resource_course" id="resource_course"></select>
        </div> 
        <div class="form-group">
            <label for="">Tipo de recursos</label>
            <select  class="form-control" name="resource_resumen" id="resource_resumen">
                <option selected="selected" value="0">Seleccionar</option>
                <option value="biblioteca">Biblioteca</option>
                <option value="libro">Libro</option>
                <option value="video">Video</option>
            </select>
        </div>
        <p style="margin-bottom: 10px" for="">Lote de Imágenes</p>
        <div class="input-group mb-3">
            <div class="custom-file">
                <input accept="image/png" class="custom-file-input" type="file" name="imagenes[]" multiple="multiple">
                <label class="custom-file-label" for="inputGroupFile02">Seleccionar imágenes</label>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" value="Subir Imágenes" >
        <a href="index.php" class="btn btn-secondary" >Cancelar</a>
    </form>
</div>
    <script src="src/js/jquery/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        $.get("includes/dispach.php", {funcion: "selectCategories"}, function (data) {
            $("#resource_category").html(data);
        });
        $("#resource_category").change(function () {
            id_category = $('#resource_category').val();
            iduser = '<?= $iduser ?>';
            isadmin = '<?= $isadmin ?>';
            $.get("includes/dispach.php", {funcion: "selectCourses", id_category : id_category, iduser: iduser, isadmin: isadmin }, function (data) {
                $("#resource_course").html(data);
            });
        });
    });
</script>
<?php
echo $OUTPUT->footer();