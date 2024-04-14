<?php
class block_normateca_unadm extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_normateca_unadm');
    }
    function get_content() {
        global $CFG,$COURSE;
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '
        <style>
        .buscarpor {
          width: 10rem;
          height: 35px;
          margin-right: 1em;
          padding: 0rem;
          font-size: 1em;
        }
        .busqueda-inputs {
          border-top: none;
          border-left: none;
          border-right: none;
          background-color: white;
          outline: none;
          margin-top: 10px;
          border-bottom: solid 1.5px #8f959e;
          padding-bottom: 15px;
        }
        .buscarpalabra {
          width: 15rem;
          height: 50px;
          margin-right: 1em;
          padding: 0rem 0.5rem;
          font-size: 1em;
          margin-bottom: 20px;
        }
        .searchNotValue{
          padding: 15px;
          background: #fded99ed;
          margin: 15px;
          border-radius: 5px;
        }
        .searchNotValue-info{
          background: #fded99ed;
        
        }
        .container{
        padding: 15px;
        }
        .form-card{
          color: white;
          padding: 10px;
          margin-top: 5px;
          justify-content: center;
        }
        .extra-card-info{
            color: black;
                font-size: 0.75em;
        }
        .btn:hover, .button-form-card:hover{
            background: #97213b !important;
            color: white;
        }
        @media (max-width: 768px) {
            .container-info{
                text-align: center;
            }
        }
        
        .p-form-card{
          padding: 8px;
          margin-bottom: 2px;
        }
        .button-form-card{
        border: 1px solid white;
        }
        .color-white{
        color:white;
        border: none !important;
        }
        .img-container-form-card{
          height: auto;
          background: white;
          border-radius: 2px;
          text-align: center;
          justify-content: center;
          display: flex;
          max-width: 100px;
          margin-top: 8px;
        }
        #searchbutton, #resetElements{
            background-color: #13322b;
            border-color: #13322b;
            border-radius: 6%;
        }
        #searchbutton:active, #resetElements:active, #searchbutton:hover, #resetElements:hover{
            background-color: #9D2449;
            border-color: #9D2449;
        }
        .btn:focus, .btn.focus{
            box-shadow: none;
        }
        .searching{
          width: 100%;
          display: flex;
          justify-content: center;
          margin-top: 30px;
          height: 100px;
          align-items: center;
          font-size: 1.3em;
        }
        .not-view{
            display: none;
        }
       
        </style>
                    <div class="set-buscador">
                      <p>
                      La Normateca Interna es una herramienta de consulta simplificada que tiene como propósito el registro, difusión y actualización de las disposiciones 
                      legales y administrativas que regulan la operación y funcionamiento al interior de la UnADM.
                        </p>
                       <select name="filtro-division" id="filtro-division" class="busqueda-inputs form-select buscarpordivision">
                          <option value="0">Seleccionar división</option>
                         <!-- <option value="resource_keywords">Palabras clave</option>
                                            <option value="resource_nivel">Nivel de ejecución</option>-->
                       </select>
                       <select name="filtro-carrera" id="filtro-carrera" class="busqueda-inputs form-select buscarporcarrera">
                          <option value="0">Seleccionar carrera</option>            
                          <!--                  <option value="resource_nivel">Nivel de ejecución</option>-->
                       </select>
                       <select name="filtro-dependencia" id="filtro-dependencia" class="busqueda-inputs form-select buscarpordependencia">
                          <option value="0">Seleccionar dependencia</option>
                          <!--                  <option value="resource_nivel">Nivel de ejecución</option>-->
                       </select>
                       <select name="filtro-recurso" id="filtro-recurso" class="busqueda-inputs form-select buscarporrecurso">
                          <option value="0">Seleccionar rescursos</option>
                          <!--                  <option value="resource_nivel">Nivel de ejecución</option>-->
                       </select>
                       <!--<input class="buscarpalabra busqueda-inputs" name="filtro-avanzado-text" id="filtro-avanzado-text" placeholder="escribir una palabra o texto">-->
                       <br>
                       <br>
                       <center>
                       <button  title="Realizar busqueda" id="searchbutton" type="button" onclick="ValidateToSubmit(this)" class="btn btn-info btn-buscar button-search">
                         Buscar <i class="fa fa-search"></i>
                       </button>
                       <button  title="Borrar busqueda" id="resetElements" type="button"  onclick="ValidateToSubmit(this)" class="btn btn-danger btn-buscar button-search">
                          Borrar   <img id="icon-delete-cien-tecnicas" src="" width="18px" alt="borrar" title="Borrar busqueda">
                       </button>
</center>
                       
                    </div>
                    <div id="vista" class="container">
                    </div>
                    <div id="cargando" class="container not-view">
                       <div class="searching "><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span>Espere...</span></div>
                    </div>
                    
        <script>
        let serverurlmoodle = "'.$CFG->wwwroot.'";
        let courseid = '.$COURSE->id.';
        let SelectDivision = document.getElementById("filtro-division");
        let SelectCarrera = document.getElementById("filtro-carrera");
        let SelectDependencia = document.getElementById("filtro-dependencia");
        let SelectRecurso = document.getElementById("filtro-recurso");
        let dataContainer = document.getElementById("vista");
        document.getElementById("icon-delete-cien-tecnicas").src = serverurlmoodle+"/blocks/normateca_unadm/img/borrar.png";
        
        window.onload = ()=>{
            document.getElementById("filtro-division").value = "0";
            document.getElementById("filtro-carrera").value = "0";
            document.getElementById("filtro-dependencia").value = "0";
            document.getElementById("filtro-recurso").value = "0";
            setDivisiones();
            
        }
        SelectDivision.onchange = ()=>{
              document.getElementById("filtro-carrera").value = "0";
              id_division = document.getElementById("filtro-division").value;
              setCarreras(id_division);
        }
        SelectCarrera.onchange = ()=>{
              setDependencias();
              setRecursos();
        }
        
        function ValidateToSubmit(dato){   
            filtroAvanzadoConditionalDivision = document.getElementById("filtro-division").value;               
            filtroAvanzadoConditionalCarrera = document.getElementById("filtro-carrera").value;               
            filtroAvanzadoConditionalDependencia = document.getElementById("filtro-dependencia").value;               
            filtroAvanzadoConditionalRecurso = document.getElementById("filtro-recurso").value;               
            filtroAvanzadoSearch = document.getElementById("filtro-avanzado-text").value;    
            if(dato.id == "searchbutton") {
                       if(filtroAvanzadoConditionalDivision == "buscar_por_division" || filtroAvanzadoSearch == "" ){
                           dataContainer.innerHTML = "<p class=\'searchNotValue\'>Es necesario seleccionar un filtro y escribir por lo menos una palabra para realizar una búsqueda.</p>";
                       }else{
                           //  console.log("El filtro activo es: "+filtroAvanzadoConditional+"el texto a buscar es:"+filtroAvanzadoSearch);
                           dataContainer.innerHTML = "";
                           actualizarSeccionBusqueda(filtroAvanzadoConditional,filtroAvanzadoSearch,dataContainer);
                       }
            }else if(dato.id == "resetElements"){
                dataContainer.innerHTML = "";
                 $("#filtro-avanzado").val("buscar_por_division");
                 $("#filtro-avanzado-text").val("");
            }
        }
        function actualizarSeccionBusqueda(filtroAvanzadoConditional="",filtroAvanzadoSearch="",dataContainer){
              // Crear una nueva instancia de XMLHttpRequest
              var xhttp = new XMLHttpRequest();
              // Definir la función de devolución de llamada para manejar la respuesta del servidor
              xhttp.onreadystatechange = function() {
                  document.querySelector("#cargando").classList.remove("not-view");
                 if (this.readyState == 4 && this.status == 200) {
                     document.querySelector("#cargando").classList.add("not-view");
                     //Válida si la conexión fue exitosa
                     if(this.responseText.includes("error_connect_database100tecnicas")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>Error al realizar la búsqueda intentalo más tarde.</p>";
                     }else{
                         
                         // Actualizar el contenido de la sección con la respuesta del servidor
                         dataContainer.innerHTML =  xhttp.responseText;
                     }
                 }
              };
              // Enviar la solicitud al servidor
              xhttp.open("POST", serverurlmoodle+"/blocks/normateca_unadm/wsConection.php", true);
              var datos = "filtro=" + encodeURIComponent(filtroAvanzadoConditional)+"&busqueda=" + encodeURIComponent(filtroAvanzadoSearch)+ "&courseid="+encodeURIComponent(courseid);
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send(datos);
        }
        
        //Divisiones
        function setDivisiones(){
              // Crear una nueva instancia de XMLHttpRequest
             
              var xhttp = new XMLHttpRequest();
              // Definir la función de devolución de llamada para manejar la respuesta del servidor
              xhttp.onreadystatechange = function() {
                  document.querySelector("#cargando").classList.remove("not-view");
                 if (this.readyState == 4 && this.status == 200) {
                     document.querySelector("#cargando").classList.add("not-view");
                     //Válida si la conexión fue exitosa
                     if(this.responseText.includes("error_not_data")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>No se encontro información en divisiones.</p>";
                     }else{
                         
                         // Actualizar el contenido de la sección con la respuesta del servidor
                         SelectDivision.innerHTML =  xhttp.responseText;
                     }
                 }
              };
              // Enviar la solicitud al servidor
              xhttp.open("POST", serverurlmoodle+"/blocks/normateca_unadm/wsConection.php", true);
              var datos = "funtion=getDivisiones";
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send(datos);
        }
        
        //Carreras
        function setCarreras(id_division){
              // Crear una nueva instancia de XMLHttpRequest
             
              var xhttp = new XMLHttpRequest();
              // Definir la función de devolución de llamada para manejar la respuesta del servidor
              xhttp.onreadystatechange = function() {
                  document.querySelector("#cargando").classList.remove("not-view");
                 if (this.readyState == 4 && this.status == 200) {
                     document.querySelector("#cargando").classList.add("not-view");
                     //Válida si la conexión fue exitosa
                     if(this.responseText.includes("error_not_data")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>No se encontro información en carreras asociadas a la division.</p>";
                     }
                     if(this.responseText.includes("error_not_division")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>Faltan parametros para realizar la solicitud.</p>";
                     }
                     else{
                         
                         // Actualizar el contenido de la sección con la respuesta del servidor
                         SelectCarrera.innerHTML =  xhttp.responseText;
                     }
                 }
              };
              // Enviar la solicitud al servidor
              xhttp.open("POST", serverurlmoodle+"/blocks/normateca_unadm/wsConection.php", true);
              var datos = "funtion=getCarreras"+ "&id_division="+encodeURIComponent(id_division);
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send(datos);
        }
        
        //Dependencias
        function setDependencias(){
              // Crear una nueva instancia de XMLHttpRequest
             
              var xhttp = new XMLHttpRequest();
              // Definir la función de devolución de llamada para manejar la respuesta del servidor
              xhttp.onreadystatechange = function() {
                  document.querySelector("#cargando").classList.remove("not-view");
                 if (this.readyState == 4 && this.status == 200) {
                     document.querySelector("#cargando").classList.add("not-view");
                     //Válida si la conexión fue exitosa
                     if(this.responseText.includes("error_not_data_dependencia")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>No se encontro información en dependencia.</p>";
                     }else{
                         
                         // Actualizar el contenido de la sección con la respuesta del servidor
                         SelectDependencia.innerHTML =  xhttp.responseText;
                     }
                 }
              };
              // Enviar la solicitud al servidor
              xhttp.open("POST", serverurlmoodle+"/blocks/normateca_unadm/wsConection.php", true);
              var datos = "funtion=getDependencias";
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send(datos);
        }
        
        //Recursos
        function setRecursos(){
              // Crear una nueva instancia de XMLHttpRequest
             
              var xhttp = new XMLHttpRequest();
              // Definir la función de devolución de llamada para manejar la respuesta del servidor
              xhttp.onreadystatechange = function() {
                  document.querySelector("#cargando").classList.remove("not-view");
                 if (this.readyState == 4 && this.status == 200) {
                     document.querySelector("#cargando").classList.add("not-view");
                     //Válida si la conexión fue exitosa
                     if(this.responseText.includes("error_not_data_recursos")){
                         dataContainer.innerHTML = "<p class=\'searchNotValue\'>No se encontro información en recursos.</p>";
                     }else{
                         
                         // Actualizar el contenido de la sección con la respuesta del servidor
                         SelectRecurso.innerHTML =  xhttp.responseText;
                     }
                 }
              };
              // Enviar la solicitud al servidor
              xhttp.open("POST", serverurlmoodle+"/blocks/normateca_unadm/wsConection.php", true);
              var datos = "funtion=getRecurso";
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send(datos);
        }
        </script>
                ';
        $this->content->footer = '';

        return $this->content;
    }
    function instance_allow_config() {
        return true;
    }

    function instance_allow_multiple() {
        return true;
    }
}
