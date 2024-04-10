<?php
require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseData = get_course($_POST['resource_course']);
    $coursename = $courseData->fullname;
    $tiperesource = $_POST['resource_resumen'];

    $carpeta = $CFG->dirroot . "/local/normateca/img/$coursename";
    $permisos = 0777;

// Verifica si la carpeta del curso y si no la crea
    if (!is_dir($carpeta)) {
        // Crea la carpeta
        mkdir($carpeta);
        chmod($carpeta, $permisos);
//    echo "La carpeta del curso se le han dado todos los permisos."."<br>";
        $carpeta = $CFG->dirroot . "/local/normateca/img/$coursename/$tiperesource";
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
        $carpeta = $CFG->dirroot . "/local/normateca/img/$coursename/$tiperesource";
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
    $carpeta = $carpeta . '/';
    if (isset($_FILES['imagenes'])) {
        $totalFiles = count($_FILES['imagenes']['name']); // Obtiene el total de archivos cargados
        $ImageNotUpload = [];
        // Recorre todos los archivos cargados
        for ($i = 0; $i < $totalFiles; $i++) {
            $nombreArchivo = $_FILES['imagenes']['name'][$i]; // Obtiene el nombre del archivo
            $rutaArchivo = $_FILES['imagenes']['tmp_name'][$i]; // Obtiene la ruta temporal del archivo

            // Puedes realizar aquí el procesamiento de las imágenes, como moverlas a una carpeta de destino
            // Por ejemplo, para mover las imágenes a una carpeta llamada "imagenes" en el directorio actual:
            $rutaDestino = $carpeta . $nombreArchivo;
            $uploadImage = move_uploaded_file($rutaArchivo, $rutaDestino);
            if($uploadImage){
                $contador++;
            }else{
                array_push($nombreArchivo,$ImageNotUpload);
            }
        }
        if($contador == $totalFiles ){
            //Bien correcto
            $destination = 'index.php';
            $message = "La imagen se cargo correctamente.";
            redirect($destination, $message, 15, \core\output\notification::NOTIFY_SUCCESS);
        }else{
            echo "Error";
            //Muestra los archivos que no se subieron
        }
    }
}
?>

