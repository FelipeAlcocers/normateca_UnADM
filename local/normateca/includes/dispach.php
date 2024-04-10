<?php
require_once ( '../lib.php');

if(isset($_GET['funcion']) && !empty($_GET['funcion'])) {
    $funcion = $_GET['funcion'];
    $idcategory = $_GET['id_category'];
    $iduser = $_GET['iduser'];
    $idItem = $_GET['idDeleteItem'];
    $isadmin = $_GET['isadmin'];
    
    //En función del parámetro que nos llegue ejecutamos una función u otra
    switch($funcion) {
        case 'selectCategories':
            selectCategories();
            break;
        case 'selectCourses':
            selectCourses($idcategory,$iduser,$isadmin);
            break;
        case 'deleteItem':
            $registro = array('id' => $idItem);
            $delete = $DB->delete_records('local_normateca',$registro);
            if($delete){
                echo "delete_success";
            }else{
                echo "error";
            }
            break;
    }
}