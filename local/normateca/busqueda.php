<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Buscar datos en tiempo real con PHP, MySQL y AJAX">
    <meta name="author" content="Marco Robles">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busqueda</title>

    <!-- Bootstrap core CSS -->
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">-->
    <script src="src/js/jquery/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="src/css/style.css?v=1">
    <link rel="stylesheet" type="text/css" href="src/js/alertifyjs/css/alertify.min.css">
    <link rel="stylesheet" type="text/css" href="src/js/alertifyjs/css/themes/default.min.css">

</head>
<?php
require_once(__DIR__.'/../../config.php');

echo $OUTPUT->header();

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

?>
<body>
    <main>
        <div class="container py-4 text-center">
            <h2>Técnicas</h2>

            <div class="row">
                <div class="col-md-2">
                    <div class="col-auto">
                        <a href="index.php" class='btn btn-secondary btn-sm'>Regresar</a>
                    </div>
                </div>
                <div class="col-md-1">
                    <label for="num_registros" class="col-form-label">Mostrar: </label>
                </div>

                <div class="col-md-1">
                    <select name="num_registros" id="num_registros" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="num_registros" class="col-form-label">registros </label>
                </div>

                <div class="col-md-2">
                    <label for="campo" class="col-form-label">Buscar: </label>
                </div>
                <div class="col-md-5">
                    <input type="text" name="campo" id="campo" class="form-control">
                </div>
            </div>

            <div class="row py-4">
                <div class="col">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th class="sort asc">Número de técnica</th>
                            <th class="sort asc">Nombre de técnica</th>
                            <th class="sort asc">Nivel</th>
                            <th class="sort asc">Categorización</th>
                            <th class="sort asc">Palabras claves</th>
                            <th></th>
                            <th></th>
                        </thead>

                        <!-- El id del cuerpo de la tabla. -->
                        <tbody id="content">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label id="lbl-total"></label>
                </div>

                <div class="col-6" id="nav-paginacion"></div>

                <input type="hidden" id="pagina" value="1">
                <input type="hidden" id="orderCol" value="0">
                <input type="hidden" id="orderType" value="asc">
            </div>
        </div>
    </main>
    <script src="src/js/alertifyjs/alertify.js"></script>

    <script>
        /* Llamando a la función getData() */
        getData()

        /* Escuchar un evento keyup en el campo de entrada y luego llamar a la función getData. */
        document.getElementById("campo").addEventListener("keyup", function() {
            getData()
        }, false)
        document.getElementById("num_registros").addEventListener("change", function() {
            getData()
        }, false)


        /* Peticion AJAX */
        function getData() {
            let input = document.getElementById("campo").value
            let num_registros = document.getElementById("num_registros").value
            let content = document.getElementById("content")
            let pagina = document.getElementById("pagina").value
            let orderCol = document.getElementById("orderCol").value
            let orderType = document.getElementById("orderType").value

            if (pagina == null) {
                pagina = 1
            }

            let url = "load.php"
            let formaData = new FormData()
            formaData.append('campo', input)
            formaData.append('registros', num_registros)
            formaData.append('pagina', pagina)
            formaData.append('orderCol', orderCol)
            formaData.append('orderType', orderType)

            fetch(url, {
                    method: "POST",
                    body: formaData
                }).then(response => response.json())
                .then(data => {
                    content.innerHTML = data.data
                    document.getElementById("lbl-total").innerHTML = 'Mostrando ' + data.totalFiltro +
                        ' de ' + data.totalRegistros + ' registros'
                    document.getElementById("nav-paginacion").innerHTML = data.paginacion
                }).catch(err => console.log(err))
        }

        function nextPage(pagina){
            document.getElementById('pagina').value = pagina
            getData()
        }

        let columns = document.getElementsByClassName("sort")
        let tamanio = columns.length
        for(let i = 0; i < tamanio; i++){
            columns[i].addEventListener("click", ordenar)
        }

        function ordenar(e){
            let elemento = e.target

            document.getElementById('orderCol').value = elemento.cellIndex

            if(elemento.classList.contains("asc")){
                document.getElementById("orderType").value = "asc"
                elemento.classList.remove("asc")
                elemento.classList.add("desc")
            } else {
                document.getElementById("orderType").value = "desc"
                elemento.classList.remove("desc")
                elemento.classList.add("asc")
            }

            getData()
        }
        function deleteItem(idDeleteItem){
            alertify.confirm('Confirmar', '¿Eliminar elemento?', function(){
                    $.get("includes/dispach.php", {funcion: "deleteItem",idDeleteItem:idDeleteItem}, function (data) {
                        if(data == 'error'){
                            alertify.error('Hubo un problema al eliminar el recurso.');



                        }
                        if(data == 'delete_success'){
                            alertify.success('Registro eliminado con éxito.');
                            getData()

                        }
                    });
                }
                , function(){ });
            
        }

    </script>

    <!-- Bootstrap core JS -->
<!--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>-->

</body>

</html>
<?php
echo $OUTPUT->footer();
