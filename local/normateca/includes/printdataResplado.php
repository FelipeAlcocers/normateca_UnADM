<?php
//function caroucelCVL($curoso=2){
require_once(__DIR__ . '/../../../config.php');
global $DB;
$filtro = '';
$notresults =0;

$busqueda = $_POST['busqueda'];
$curso =4;
print_object($_POST);
print_object($_GET);
$consulta = 'SELECT * FROM mdl_local_normateca';
$consulta = $DB->get_records_sql($consulta);

//obteniendo los diferentes tipos de recursos
$consultaTipos = 'SELECT resource_2type_resource FROM mdl_local_normateca GROUP BY resource_2type_resource';
$consultaTipos = $DB->get_records_sql($consultaTipos);
$consultaTipos = array_keys($consultaTipos);

if(sizeof($consulta)==0){
    $notresults = 1;
}
foreach ($consultaTipos as $item){
    if($_POST[$item]=='on'){
        $filtro = $item;
        $muestradata = 1;
        break;
    }
}
($filtro != '')?$like=1:$like=0;
echo $filtro;
if($busqueda != ''){
    if($like == 1) {
        echo "busqueda con filtro";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_2type_resource = "'.$filtro.'" AND (resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%")';
    }else{
        echo "busqueda sin filtro";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%"';
    }
    $consulta = $DB->get_records_sql($consulta);
    if(sizeof($consulta)==0){
        $notresults = 1;
    }
}else{
    if($like == 1){
        echo "busqueda con filtro sin busqueda";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_2type_resource LIKE "%'.$filtro.'%"';
    }else{
        echo "busqueda global busqueda";
        $consulta = 'SELECT * FROM mdl_local_normateca WHERE resource_course = '.$curso.' AND (resource_tecnica LIKE "%'.$busqueda.'%" OR resource_keywords LIKE "%'.$busqueda.'%")';
//        $consulta = 'SELECT * FROM mdl_local_normateca';
    }
    $consulta = $DB->get_records_sql($consulta);
    if(sizeof($consulta)==0){
        $notresults = 1;
    }

}
$countL = 0;
$countL2 = 0;
$countLInit = 0;

function searchBar($name,$notresult=0){
    $textL = $textL . '
                <div class="text-center" xmlns="http://www.w3.org/1999/html">
                    <h5>' .strtoupper($name)."S".'</h5>
                </div>
               <div style="align-content: center; text-align: center; padding-bottom: 40px; padding-top: 40px">
                    <form id="recursos" action="printdata.php" method="post" enctype="multipart/form-data">
                           <div style="margin-left: auto;margin-right: auto;margin-bottom: 10px;width: 500px;">
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="todo" name="todo"><span class="label">Todos</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="libro" name="libro"><span class="label">Libros</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox"  id="video" name="video"><span class="label">Videos</span></label>
                                <label><input onclick="ValidateToSubmit(this)" type="checkbox" id="biblioteca" name="biblioteca"><span class="label">Bibliotecas</span></label>
                           </div>
                         <input name="busqueda" id="busqueda" placeholder="Buscar" aria-label="Buscar">
                         <button style="margin-top: -3px; " type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button><br>
                    </form>
                </div>
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
                    </div>';
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
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Recursos - Club Virual De Lenguas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css?version=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../assets/css/styles.css?v=1" rel="stylesheet"/>
</head>
<style>
    input[type=checkbox] {
        display: none;
    }
    .label {
        border: 1px solid #000;
        display: inline-block;
        padding: 3px;
        /* background: url("unchecked.png") no-repeat left center; */
        /* padding-left: 15px; */
    }
    input[type=checkbox]:checked + .label {
        background: #f00;
        color: #fff;
        /* background-image: url("checked.png"); */
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
            <a class="carousel-control-prev" href="#myCarousel2" data-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="carousel-control-next" href="#myCarousel2" data-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
</section>

    <?php    
}
if($filtro!=''){
    echo "<script>
                    var inputChecks = document.getElementById('recursos').querySelectorAll('input[type=checkbox]');
                for(var i=0; i<inputChecks.length; i++){
                    inputChecks[i].checked = false;
                }
                document.getElementById('$filtro').checked = true;
                dato.parentElement.parentElement.parentElement.submit()
                </script>";
}    
?>
                
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
<script src="assets/js/scripts.js?=1"></script>
<!--<script src="../assets/js/linkindex.js?version=1"></script>-->
<!--<script src="assets/js/linkexterno.js"></script>-->
<script src="assets/js/startbootstrap.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!--<script src="../assets/js/recursos.js?version=1.0"></script>-->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<link rel="stylesheet" type="text/css" href="local/normateca/src/js/alertifyjs/css/alertify.min.css">
<link rel="stylesheet" type="text/css" href="local/normateca/src/js/alertifyjs/css/themes/default.min.css">
<script src="local/normateca/src/js/alertifyjs/alertify.js"></script>

<script>
    alertify.YoutubeDialog || alertify.dialog('YoutubeDialog',function(){
        var iframe;
        var tipo;
        var enlaceExterno;
        return {
            // dialog constructor function, this will be called when the user calls alertify.YoutubeDialog(videoId)
            main:function(typeframe,urlId){
                //set the typeframe setting and return current instance for chaining.
                return this.set({
                    'typeframe': typeframe,
                    'url': urlId
                });
            },
            // we only want to override two options (padding and overflow).
            setup:function(){
                return {
                    options:{
                        //disable both padding and overflow control.
                        padding : !1,
                        overflow: !1,
                        basic:true,
                    }
                };
            },
            // This will be called once the DOM is ready and will never be invoked again.
            // Here we create the iframe to embed the video.
            build:function(){
                // create the iframe element
                iframe = document.createElement('iframe');
                enlaceExterno = document.createElement('a');
                iframe.frameBorder = "no";
                iframe.width = "100%";
                iframe.height = "100%";
                enlaceExterno.textContent = "Abrir en otra ventana";
                enlaceExterno.target = "_blank";
                enlaceExterno.style.margin = "40%";
                enlaceExterno.style.backgroundColor = "#413e46";
                enlaceExterno.style.padding = "0.7%";
                enlaceExterno.style.color = "white";
                enlaceExterno.style.borderRadius = "10px";
                enlaceExterno.style.textDecoration = "none";
                // add it to the dialog
                this.elements.content.appendChild(iframe);
                this.elements.footer.appendChild(enlaceExterno);
                this.elements.footer.style.visibility = "visible";

                //give the dialog initial height (half the screen height).
                this.elements.body.style.minHeight = screen.height * .5 + 'px';
            },
            // dialog custom settings
            settings:{
                typeframe:undefined,
                url:undefined
            },
            // listen and respond to changes in dialog settings.
            settingUpdated:function(key, oldValue, newValue){
                switch(key){
                    case 'typeframe':
                        console.log(newValue);
                        if(newValue == 'video'){
                            tipo = 'video';
                        }
                        break;
                    case 'url':
                        if(tipo == 'video'){
                            //Valida si es video de youtube
                            if(newValue.includes('youtube')){
                                const urlvideo = newValue;
                                const index = urlvideo.indexOf('?');

                                const urlPart1 = urlvideo.slice(0, index);
                                const urlPart2 = urlvideo.slice(index + 1);

                                const params = {};
                                urlPart2.split('&').forEach(param => {
                                    const keyValue = param.split('=');
                                    params[keyValue[0]] = keyValue[1];
                                });
                                //
                                newValue = params.v;
                                iframe.src = "https://www.youtube.com/embed/" + newValue + "?enablejsapi=1";
                                enlaceExterno.href = urlvideo;
                            }else{
                                iframe.src = newValue+"?enablejsapi=1";
                                enlaceExterno.href = newValue;
                            }
                        }else{
                            iframe.src = newValue+"?enablejsapi=1";
                            enlaceExterno.href = newValue;
                        }
                        break;

                }
            },
            // listen to internal dialog events.
            hooks:{
                // triggered when the dialog is closed, this is seperate from user defined onclose
                onclose: function(){
                    iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}','*');
                },
                // triggered when a dialog option gets update.
                // warning! this will not be triggered for settings updates.
                onupdate: function(option,oldValue, newValue){
                    switch(option){
                        case 'resizable':
                            if(newValue){
                                this.elements.content.removeAttribute('style');
                                iframe && iframe.removeAttribute('style');
                            }else{
                                this.elements.content.style.minHeight = 'inherit';
                                iframe && (iframe.style.minHeight = 'inherit');
                            }
                            break;
                    }
                }
            }
        };
    });
</script>
<script>
    function ValidateToSubmit(dato){
        var inputChecks = document.getElementById('recursos').querySelectorAll('input[type=checkbox]');
        for(var i=0; i<inputChecks.length; i++){
            inputChecks[i].checked = false;
        }
        document.getElementById(dato.id).checked = true;
        dato.parentElement.parentElement.parentElement.submit()
    }
    function generateModal(data){
        alertify.YoutubeDialog('video',data.attributes.data.nodeValue).set({frameless:false});

        console.log(data.attributes.data.nodeValue);
    }
   

</script>
<?php
echo "Sin texto";