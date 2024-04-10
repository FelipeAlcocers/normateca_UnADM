<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parametro1 = $_POST["parametro1"];
    $parametro2 = $_POST["parametro2"];
    $respuesta = "El valor del primer parámetro es " . $parametro1 . " y el valor del segundo parámetro es " . $parametro2;
    echo $respuesta;
}
?>
