<?php
require_once(__DIR__.'/../../../config.php');
$data = $_POST;
/*print_object($data);
die();*/
//Valida el  de la imagen

$objdata = new stdClass();
$namesdata = array_keys($data);
$datacapture = 0;
$numInputCapture = $data['numInputs']-1;
for($i=0; $i<= sizeof($data)-1; $i++){
    $name = $namesdata[$i];
    if($name != 'numInputs'){
        if($name != 'edit'){
            if($data[$namesdata[$i]] != '') {
//                echo $name . "<br>";
                $objdata->$name = $data[$namesdata[$i]];
                $datacapture++;
            }
        }
    }
}
/*echo $datacapture;
echo $numInputCapture;
print_object($objdata);
die();*/

//COmprueba la carpeta de la imagen

if($objdata->resource_type_resource == ''){
    echo "missing_data";
    die();
}
//$courseData = get_course($objdata->resource_course);
//$coursename = $courseData->fullname;
$tiperesource = $objdata->resource_type_resource;
$carpeta = $CFG->dirroot."/local/normateca/docs";
$permisos = 0777;

// Verifica si la carpeta del curso y si no la crea
if (!is_dir($carpeta)) {
    // Crea la carpeta
    mkdir($carpeta);
    chmod($carpeta, $permisos);
//    echo "La carpeta del curso se le han dado todos los permisos."."<br>";
    $carpeta = $CFG->dirroot."/local/normateca/docs";
    $permisos = 0777;

// Verifica si la carpeta ya existe
    if (!is_dir($carpeta)) {
        // Crea la carpeta
        mkdir($carpeta);
        chmod($carpeta, $permisos);
//        echo "La carpeta ".$tiperesource."se ha creado correctamente y se le han dado todos los permisos.";
    } else {
//        echo "La carpeta".$tiperesource." ya existe.";
        //Solo toma la ruta donde guardara la imagen
    }

} else {
//    echo "La carpeta del curso  ya existe."."<br>";
    //si la carpeta existe valida que la carpeta con el tipo de recurso exista
    $carpeta = $CFG->dirroot."/local/normateca/docs";
    $permisos = 0777;

// Verifica si la carpeta ya existe
    if (!is_dir($carpeta)) {
        // Crea la carpeta
        mkdir($carpeta);
        chmod($carpeta, $permisos);
//        echo "La carpeta ".$tiperesource."se ha creado correctamente y se le han dado todos los permisos.";
    } else {
//        echo "La carpeta".$tiperesource." ya existe.";
        //Solo toma la ruta donde guardara la imagen
    }
}
$carpeta = $carpeta.'/';
//echo "La carpeta es ".$carpeta;
//die();
//Válida que no exista un recurso con ese nombre y perteneciente 
if($datacapture == $numInputCapture){
    $idtecnica = $objdata->id_resource;
    $name  = $objdata->resource_tecnica;
    $language  = $objdata->resource_nombre_img;
    $consultadata = $DB->get_record('local_normateca',array('id'=>$idtecnica));
    if($consultadata){
        if($data['edit'] == 1){
            $itemUpdate = new stdClass();
            $itemUpdate->id = $data['id_resource'];
            $itemUpdate->resource_no_tecnica = $data['resource_no_tecnica'];
            $itemUpdate->resource_tecnica = $data['resource_tecnica'];
            $itemUpdate->resource_que_es = $data['resource_que_es'];
            $itemUpdate->resource_estructura = $data['resource_estructura'];
            $itemUpdate->resource_keywords = $data['resource_keywords'];
            $itemUpdate->resource_utilidad = $data['resource_utilidad'];
            $itemUpdate->resource_como_construye = $data['resource_como_construye'];
            $itemUpdate->resource_tomar_cuenta = $data['resource_tomar_cuenta'];
            $itemUpdate->resource_autores_dicen = $data['resource_autores_dicen'];
            $itemUpdate->resource_resumen = $data['resource_resumen'];
            $itemUpdate->resource_nivel = $data['resource_nivel'];
            $itemUpdate->resource_type_resource = $data['resource_type_resource'];
            $itemUpdate->resource_referencias = $data['resource_referencias'];
            $itemUpdate->resource_comocitar = $data['resource_comocitar'];
            $itemUpdate->resource_nombre_img = $data['resource_nombre_img'];
            $itemUpdate->resource_nombre_pdf = $data['resource_nombre_pdf'];

//            $itemUpdate->resource_category = $data['resource_category'];
//            $itemUpdate->resource_course = $data['resource_course'];
           /* print_object($itemUpdate);
            die();*/
            $consultadata = $DB->update_record('local_normateca',$itemUpdate);
           
            if($consultadata){
//                echo "success";
//                echo "Se validará la image a subir";
                //Actualiza la el nombre de la imagen en caso de contener caracteres especiales
                if(isset($_FILES['imageFile'])) {
                    $nameImage = $_FILES['imageFile']['name'];
//                    $Arrayname = explode('.svg',$nameImage);
//                    $nameImage = $Arrayname[0];
                    $nameImages = $data['resource_nombre_img'];
                    $nameImages = str_replace("?", "",  $nameImage);
                    $nameImages = str_replace("¿", "",  $nameImage);
                    $nameImages = str_replace("/", "",  $nameImage);
                    $nameImages = str_replace("*", "",  $nameImage);
                    $nameImages = str_replace("|", "",  $nameImage);
                    $nameImages = str_replace("<", "",  $nameImage);
                    $nameImages = str_replace(">", "",  $nameImage);
                    $nameImages = str_replace(":", "",  $nameImage);
                    $nameImages = str_replace('"', "",  $nameImage);

                    if($nameImage != $nameImages){
                        echo "image_not_correct_name";
                        die();
                    }
                }
                //Validando y subiendo archivos PDFS
                if (isset($_FILES['pdfs'])) {
                    $contador =0;
                    $totalFiles = count($_FILES['pdfs']['name']); // Obtiene el total de archivos cargados
                    $ImageNotUpload = [];
//                    echo "Revisando PDF";
                    // Recorre todos los archivos cargados
                    for ($i = 0; $i < $totalFiles; $i++) {
                        $nombreArchivo = $_FILES['pdfs']['name'][$i]; // Obtiene el nombre del archivo
                        $rutaArchivo = $_FILES['pdfs']['tmp_name'][$i]; // Obtiene la ruta temporal del archivo
//                        echo "El número de archivos que hay ".$totalFiles."<br>";
//                        echo "El nombre del archivo es: ".$nombreArchivo."<br>";
//                        echo "La ruta de archivo es: ".$rutaArchivo."<br>";
                        // Puedes realizar aquí el procesamiento de las imágenes, como moverlas a una carpeta de destino
                        // Por ejemplo, para mover las imágenes a una carpeta llamada "imagenes" en el directorio actual:
                        if($nombreArchivo != ''){
                            if($nombreArchivo == $objdata->resource_nombre_pdf){
//                                echo "Nombre de pdf de correcto".$objdata->resource_nombre_pdf."<br>";
                                $correctname =1;
                            }else if($nombreArchivo == $objdata->resource_nombre_pdf_bn){
                                $correctname =1;
//                                echo "Nombre pdf_Blanco y negro".$objdata->resource_nombre_pdf_bn."<br>";
                            }else{
//                                echo "El nombre del archivo es diferente";
                                echo "error_name_pdf";
                                die();
                            }
                        }
                        if($correctname == 1) {
                            $rutaDestino = $carpeta . $nombreArchivo;
                            $uploadImage = move_uploaded_file($rutaArchivo, $rutaDestino);
                            if ($uploadImage) {
                                $contador++;
                            } else {
                                array_push($nombreArchivo, $ImageNotUpload);
                            }
                        }
                    }
                    if($contador == $totalFiles ){
                        //Bien correcto
                        $uploadOk = 1;
                    }else{
                        if($contador == 0){
                            $uploadOk = 1;
                        }else{
                            echo "error_upload_pdf";
                            die();
                            //Muestra los archivos que no se subieron
                        }
                    }

                }
                //Validando y subiendo imagen
//                echo "Antes de validar imagen";
                if(isset($_FILES['imageFile'])){
                    $target_dir = $carpeta;
                    $nombreImagenASubir = $_FILES['imageFile']['name']; //Para el nombre que tiene la imagen que se intentará subir
                    $target_file = $target_dir . basename($_FILES['imageFile']['name']); // Ruta completa del archivo de destino
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                    
                    if($nombreImagenASubir != '' && $nombreImagenASubir != $objdata->resource_nombre_img){
//                        echo $nombreImagenASubir;
                        echo "error_name_img";
                        die();
                    }
                    // Verificar si el archivo es una imagen real
                    /*$check = getimagesize($_FILES['imageFile']['tmp_name']);
                    if($check !== false) {
                        $uploadOk = 1;
                    } else {
        echo "El archivo no es una imagen.";
                        $uploadOk = 0;
                    }*/
                    //Validando la imagen SVG
                    $xml = simplexml_load_file($_FILES['imageFile']['tmp_name']);
                    if ($xml !== false && $xml->getName() === 'svg') {
                        // El archivo es un SVG válido
                        $uploadOk = 1;
                        // Realiza acciones adicionales si es necesario
                    } else {
                        $uploadOk = 0;
                        // El archivo no es un SVG válido
                    }

                    // Verificar si el archivo ya existe
                    if (file_exists($target_file)) {
//                        echo "El archivo ya exite";
                        echo "success_but_upload_image_exist";
                        unlink( $target_file);
                        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $target_file)) {
//                            echo "La imagen " . basename($_FILES['imageFile']['name']) . " se ha subido correctamente.";
//                            echo "success";
                        }
                        $uploadOk = 0;
                        die();
                    }

                    // Verificar el tamaño del archivo
                    /*if ($_FILES['imageFile']['size'] > 800000) {
                        echo "imagen grande";
                        echo "success_but_not_upload_image";
                        die();
                        $uploadOk = 0;
                    }*/

                    // Permitir sólo ciertos formatos de imagen
                   /* $allowedFormats = array('jpg', 'jpeg', 'png', 'gif','svg');
                    if(!in_array($imageFileType, $allowedFormats)) {
//        echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                        $uploadOk = 0;
                    }*/

                    // Verificar si $uploadOk está en 0 por algún error
                    if ($uploadOk == 0) {
                        die();
                        // Si todo está bien, intentar subir el archivo
                    } else {
                        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $target_file)) {
//            echo "La imagen ". basename($_FILES['imageFile']['name']). " se ha subido correctamente.";
                            echo "success";
                        } else {
                            echo "success_but_not_upload_image";

                        }
                    }
                }

            }else{
                echo "error";

            }
        }else{
            echo "exist_data";

        }
    }
    else{
        if(isset($_FILES['imageFile'])) {
            $nameImage = $_FILES['imageFile']['name'];
            $Arrayname = explode('.png',$nameImage);
            $nameImage = $Arrayname[0];
            $nameImages = $data['resource_tecnica'];
            $nameImages = str_replace("?", "",  $nameImage);
            $nameImages = str_replace("¿", "",  $nameImage);
            $nameImages = str_replace("/", "",  $nameImage);
            $nameImages = str_replace("*", "",  $nameImage);
            $nameImages = str_replace("|", "",  $nameImage);
            $nameImages = str_replace("<", "",  $nameImage);
            $nameImages = str_replace(">", "",  $nameImage);
            $nameImages = str_replace(":", "",  $nameImage);
            $nameImages = str_replace('"', "",  $nameImage);
          
            if($nameImage != $nameImages){
                echo "image_not_correct_name";
                die();
            }
        }
//        print_object($objdata);
        $consulta = $DB->insert_record('local_normateca',$objdata);
        if($consulta){
            //SI SEINSERTO BIEN SUBE LA IMAGEN
            if(isset($_FILES['imageFile'])){
                $target_dir = $carpeta;
                $target_file = $target_dir . basename($_FILES['imageFile']['name']); // Ruta completa del archivo de destino
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Verificar si el archivo es una imagen real
                $check = getimagesize($_FILES['imageFile']['tmp_name']);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
//        echo "El archivo no es una imagen.";
                    $uploadOk = 0;
                }

                // Verificar si el archivo ya existe
                if (file_exists($target_file)) {
                    echo "success_but_upload_image_exist";
                    $uploadOk = 0;
                    die();
                }

                // Verificar el tamaño del archivo
                /*if ($_FILES['imageFile']['size'] > 800000) {
                    echo "imagen grande";
                    echo "success_but_not_upload_image";
                    die();
                    $uploadOk = 0;
                }*/

                // Permitir sólo ciertos formatos de imagen
                $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
                if(!in_array($imageFileType, $allowedFormats)) {
//        echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                    $uploadOk = 0;
                }

                // Verificar si $uploadOk está en 0 por algún error
                if ($uploadOk == 0) {
                    die();
                    // Si todo está bien, intentar subir el archivo
                } else {
                    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $target_file)) {
//            echo "La imagen ". basename($_FILES['imageFile']['name']). " se ha subido correctamente.";
                        echo "success";
                    } else {
//            echo "Error al subir la imagen.";
                        echo "success_but_not_upload_image";

                    }
                }
            }

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




