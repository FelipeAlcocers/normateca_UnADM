<?php


/**
 * @package  normateca
 * @copyright 2023, Luis Felipe Alcocer  <luisfelipealcocersosa@gmail.com>
 */
 
defined('MOODLE_INTERNAL') || die();
 
$string['plugintitle'] = 'Normateca central - UnADM';
$string['pluginname'] = 'Normateca';
$string['categoryiddb'] = 'Categoría en la que se subirán los recursos';
$string['categoryiddbinfo'] = 'Los recursos se orden por categorías y cursos es por ello que la configuración debe moverse cuando se requiera que el recurso pertenezca a otra categoría';
$string['idusersallprivilegios'] = 'Id o id´s de usuarios que tiene acceso como administrador';
$string['idusersallprivilegiosinfo'] = 'Cuando se requiere que un id de usuario tenga privilegios como administrador en la carga de recursos';
$string['roleshowmdoule'] = 'Especificar el id de los roles que tendrán acceso';
$string['roleshowmdouleinfo'] = 'El o los id´s deben tener tener el siguiente formato: rol1|rol2|rol3 .
En caso que solo se un rol se deberá evitar el uso de |';
$string['aceptvisiblecourse'] = 'Muestra solo cursos visibles:';
$string['aceptvisiblecourseinfo'] = 'Para importar recursos a cursos que se encuentran ocultos, las categoría se toma en cuenta estando o no visible si se agrega el campo "Categoría en la que se subirán los recursos"';
$string['nameformupload'] = 'Importar datos';
$string['rowpreviewdata'] = 'Importar datos sin visualizar:';
$string['examplecsv'] = 'Ejemplo de archivo';
$string['examplecsv_help'] = 'Para usar el archivo de texto de ejemplo, descárguelo y ábralo con un editor de texto u hoja de cálculo. Deje la primera línea sin cambios, luego edite las siguientes líneas (registros) y agregue los datos del repositorio, agregando más líneas según sea necesario. 

Guarde el archivo como CSV y luego cárguelo. El archivo de texto de ejemplo también se puede utilizar para realizar pruebas, ya que puede obtener una vista previa de los datos de usuario y puede optar por cancelar la acción antes de crear las cuentas de usuario.';