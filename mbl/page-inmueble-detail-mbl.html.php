<?php
/**
 * Template Name: page-inmuebles-mbl.html
 * The template for displaying inmuebles-mbl.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once __DIR__ . "/../self/security.php";
require_once __DIR__ . "/../self/graph-stuff.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/inmuebles-mbl-detail.css">';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="https://unpkg.com/filepond/dist/filepond.js"></script>';
    echo '<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>';
    echo '<script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>';
    
    echo '<script src="https://unpkg.com/filepond-plugin-file-rename/dist/filepond-plugin-file-rename.js"></script>';
    echo '<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>';
    echo '<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">';
    echo '<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.13.0/Sortable.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js"></script>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css">';

}
add_action('wp_head', 'myCss');



$inmueble_id = ($_GET["inmueble_id"]);
$inmueble = get_post($_GET["inmueble_id"]);


function selectPerfilCreate($field, $inmueble, $values, $label = '', $fn = "editar") {
    if (current_user_can("administrator") && get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true)) {
  
  ?>
    <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
    <select class="controls js-choices" onchange="<?php echo $fn ?>(event)" name="inmueble-<?php echo $field ?>" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true); ?>">
  <?php
  foreach ($values as $key => $value) {
  ?>
      <option <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true) == $value) echo "selected='selected'"; ?> value="<?php echo $value ?>"><?php echo $value ?></option>
  <?php
  }
  ?>
    </select>
    <div class="undoer">
      <label >
          Valor anterior
      </label>
      <label style="background-color: #fff; padding: 10px;"> 
        <?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true) ?>
  
      </label>
  
  
      <i onclick="undoSelect(event, '<?php echo $field ?>', '<?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true);?>')" class="fas fa-undo" title="Recuperar valor anterior"></i>
      <i onclick="removeUndoSelect(event, '<?php echo $field ?>')" class="fas fa-trash" title="Descartar valor anterior"></i>
    </div>
  <?php
    } else {
  ?>
    <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
    <select class="controls js-choices" onchange="<?php echo $fn ?>(event)" name="inmueble-<?php echo $field ?>" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true); ?>">
  <?php
  foreach ($values as $key => $value) {
  ?>
      <option <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true) == $value) echo "selected='selected'"; ?> value="<?php echo $value ?>"><?php echo $value ?></option>
  <?php
  }
  ?>
    </select>
  <?php
    }
  }
  
  
  function selectLocationCreate($field, $inmueble, $values, $label = '', $fn = "editar") {
    if (current_user_can("administrator") && get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true)) {
  ?>
    <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
    <select class="controls js-choices" onchange="<?php echo $fn ?>(event)" name="inmueble-<?php echo $field ?>" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true); ?>">
  <?php
  foreach ($values as $key => $value) {
  ?>
      <option <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true) == $value["id"]) echo "selected='selected'"; ?> value="<?php echo $value["id"] ?>"><?php echo $value["name"] ?></option>
  <?php
  }
  ?>
    </select>
    <div class="undoer">
      <label >
          Valor anterior
      </label>
      <label style="background-color: #fff; padding: 10px;"> 
        <?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true) ?>
  
      </label>
  
  
      <i onclick="undoSelect(event, '<?php echo $field ?>', '<?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true);?>')" class="fas fa-undo" title="Recuperar valor anterior"></i>
      <i onclick="removeUndoSelect(event, '<?php echo $field ?>')" class="fas fa-trash" title="Descartar valor anterior"></i>
    </div>
  <?php
    } else {
  ?>
    <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
    <select class="controls js-choices" onchange="<?php echo $fn ?>(event)" name="inmueble-<?php echo $field ?>" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true); ?>">
  <?php
  foreach ($values as $key => $value) {
  ?>
      <option <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true) == $value["id"]) echo "selected='selected'"; ?> value="<?php echo $value["id"] ?>"><?php echo $value["name"] ?></option>
  <?php
  }
  ?>
    </select>
  <?php
    }
  }
  
  function fieldPerfilCreate($field, $inmueble, $type, $label = '') {
    if (current_user_can("administrator") && get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true)) {
  
  ?>
  <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
  <input placeholder="<?php echo $label ?: $field; ?>" type="<?php echo $type; ?>" id="<?php echo $field; ?>" name="inmueble-<?php echo $field; ?>" onchange="editar(event)" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true);?>">
  <div class="undoer">
    <label for="old-<?php echo $field; ?>">
        Valor anterior
    </label>
    <input placeholder="<?php echo $label ?: $field; ?>" type="<?php echo $type; ?>" id="old-<?php echo $field; ?>" name="old-inmueble-<?php echo $field; ?>" onchange="editar(event)" value="<?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true);?>">
    <i onclick="undo(event, '<?php echo $field ?>', '<?php echo get_post_meta($inmueble->ID, 'old-meta-inmueble-' . $field, true);?>')" class="fas fa-undo" title="Recuperar valor anterior"></i>
    <i onclick="removeUndo(event, '<?php echo $field ?>')" class="fas fa-trash" title="Descartar valor anterior"></i>
  </div>
  <?php
    } else {
  
  ?>
  <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
  <input placeholder="<?php echo $label ?: $field; ?>" type="<?php echo $type; ?>" id="<?php echo $field; ?>" name="inmueble-<?php echo $field; ?>" onchange="editar(event)" value="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-' . $field, true);?>">
  <?php
    }
  
  }
  
get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="posicion">
            <div class="caja-volver">
                <div class="volver">
                    <a onclick="history.back()">Volver inmuebles</a>
                </div>
            </div>
        </div>
        <section class="wrapper">

            <section class="paginacion">
                <ul>
                    <li class="selected"><a onclick="menu(1)">Datos</a></li>
                    <li><a onclick="menu(2)">Detalles</a></li>
                    <li><a onclick="menu(3)">Fotos</a></li>
                </ul>
            </section>
            <section class="datos-basicos">
                <h1>Datos básicos</h1>
                <div class="boton-siguiente">
                    <input type="button" value="Siguiente" onclick="next(1)" />
                </div>
                <div class="localizacion">
                    <div class="fila">
                        <div>
<?php 
  $ccaas = (getCCAA());
  selectLocationCreate("ccaa", $inmueble, $ccaas, "CCAA", "editarLocalizacion");
?>
                        </div>
                        <div>
<?php 
  $provincias = (getPROVINCIA(get_post_meta($inmueble->ID, 'meta-inmueble-ccaa', true)));
  selectLocationCreate("provincia", $inmueble, $provincias, "Provincia", "editarLocalizacion");
?>
                        </div>
                        <div>
<?php 
  $municipios = (getMUNICIPIO(get_post_meta($inmueble->ID, 'meta-inmueble-provincia', true)));
  selectLocationCreate("municipio", $inmueble, $municipios, "Municipio", "editarLocalizacion");
?>
                        </div>
                        <div>
<?php 
  $poblaciones = (getPOBLACION(get_post_meta($inmueble->ID, 'meta-inmueble-municipio', true)));
  selectLocationCreate("poblacion", $inmueble, $poblaciones, "Población", "editarLocalizacion");
?>
                        </div>
                        <div>
<?php 
  selectPerfilCreate("tipo-de-via", $inmueble, ["Calle", "Avenida", "Via", "Paseo", "Camino", "Pasaje", "Plaza", "Poligono"], "Tipo de Via");
?>
                        </div>
                        <div class="direccion">   
<?php 
  fieldPerfilCreate("direccion", $inmueble, "text");
?>
                        </div>
                        <div>
<?php 
  fieldPerfilCreate("codigopostal", $inmueble, "text", "Código Postal");
?>
                        </div>
                        <div>
<?php 
  fieldPerfilCreate("numero", $inmueble, "text", "Número");
?>
                        </div>
                        <div>
<?php 
  fieldPerfilCreate("escalera", $inmueble, "text");
?>
                        </div>
                        <div>
<?php 
  fieldPerfilCreate("piso-planta", $inmueble, "text");
?>
                        </div>
                        <div>
<?php 
  fieldPerfilCreate("puerta", $inmueble, "text");
?>
                        </div>
                    </div>
                </div>
                <div class="boton-siguiente">
                    <input type="button" value="Siguiente" onclick="next(1)" />
                </div>
            </section>  
            <section class="detalles" style="display: none;">
                <h1>Detalles</h1>
                <div class="boton-siguiente">
                    <input type="button" value="Siguiente" onclick="next(2)" />
                </div>
                <div class="inmueble">
                    <div class="fila">
                        <div class="desplegable">
                            <div>
<?php 
  selectPerfilCreate("tipo", $inmueble, ["Piso", "Casa", "Atico", "Chalet Independiente", "Chalet Pareado", "Chalet Adosado", "Garaje", "Trastero"], "Tipo de inmueble", "editarTipoInmueble");
?>
                            </div>
                            <div>
<?php 
  selectPerfilCreate("destino", $inmueble, ["Venta", "Alquiler"], "Disponibilidad");
?>
                            </div>
                            <div>
<?php 
  selectPerfilCreate("estado", $inmueble, ["Buen estado", "A Estrenar", "A Reformar", "Reformado"], "Estado");
?>
                        </div>
                        <div>
                            <label for="valor">Valor</label>
                            <input type="text" id="valor" name="valor" placeholder="valor">
                        </div>
                        <div>
                            <label for="superficie-util">Superficie Util</label>
                            <input type="text" id="superficie-util" name="superficie-util" placeholder="superficie util">
                        </div>
                        <div>
                            <label for="superficie-construida">Superficie Construida</label>
                            <input type="text" id="superficie-construida" name="superficie-construida"
                                placeholder="superficie construida">
                        </div>
                        <!-- div class terreno solo si es un chalet de cualquiera de los 3 tipos -->
                        <div class="terreno">
                            <label for="superficie-parcela">Superficie Parcela</label>
                            <input type="text" id="superficie-parcela" name="superficie-parcela"
                                placeholder="superficie parcela">
                        </div>
                        <div>
                            <label for="habitaciones">Habitaciones</label>
                            <input type="text" id="habitaciones" name="habitaciones" placeholder="habitaciones">
                        </div>
                        <div>
                            <label for="baños">Baños</label>
                            <input type="text" id="baños" name="baños" placeholder="baños">
                        </div>
                        <div>
                            <label for="salones">Salones</label>
                            <input type="text" id="salones" name="salones" placeholder="salones">
                        </div>
                        <div>
                            <label for="terrazas">Terrazas</label>
                            <input type="text" id="terrazas" name="terrazas" placeholder="terrazas">
                        </div>
                        <div>
                            <label for="garaje">Garaje</label>
                            <input type="text" id="garaje" name="garaje" placeholder="garaje">
                        </div>
                        <div>
                            <label for="trastero">trastero</label>
                            <input type="text" id="trastero" name="trastero" placeholder="trastero">
                        </div>
                        <div class="fila descripcion">
                            <textarea>Describa su Inmueble</textarea>
                        </div>
                        <div class="check">
                            <div class="solochalet solopiso">
                                <input type="checkbox" id="cbox1" name="inmueble-garaje" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-garaje', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox1">Garaje</label>
                            </div>
                            <div class="solopiso">
                                <input type="checkbox" id="cbox2" name="inmueble-ascensor" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-ascensor', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox2">Ascensor</label>
                            </div>
                            <div class="solopiso">
                                <input type="checkbox" id="cbox3" name="inmueble-trastero" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-trastero', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox3">Trastero</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cbox4" name="inmueble-centrourbano" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-centrourbano', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox4">Centro Urbano</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cbox5" name="inmueble-comercio" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-comercio', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox5">Comercio</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cbox6" name="inmueble-farmacia" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-farmacia', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox6">Farmacia</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cbox7" name="inmueble-parques" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-parques', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox7">Parques y Jardines</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cbox8" name="inmueble-escuela" onchange="editarCheck(event)" 
                                <?php if (get_post_meta($inmueble->ID, 'meta-inmueble-escuela', true) == "on" ) { ?> checked <?php }?>>
                                <label for="cbox8">Escuelas</label>
                            </div>
                        </div>
                    </div>
                    <div class="boton-siguiente">
                        <input type="button" value="Siguiente" onclick="next(2)" />
                    </div>
                </div>
            </section>
            <section class="fotos" style="display: none;">

<hr />
<div class="bg-fotos">

<?php

$photosRaw = get_post_meta($inmueble_id, 'meta-inmueble-imagenes-metainfo', true);

$photos = json_decode(wp_unslash($photosRaw), true);
foreach ($photos as $key => $photo) {
?>

          <div class="card-ft" data-photo="<?php echo $photo['url']?>">
            <div class="iconos" style="display: none">
              <div class="icn-move">
                <i class="fas fa-grip-horizontal"></i>
              </div>
              <div class="icn-del delete-img" onclick="removeImage('<?php echo $photo['url']?>')">
                <i class="fas fa-times"></i>
              </div>
            </div>
            <div class="card-img">
              <img src="<?php echo $photo['url']?>" alt="<?php echo ($photo['name'])?>" width="100%">
            </div>
            <div class="card-text">
              <input onchange="updateImgs(event)" type="text" name="name-img" placeholder="TITULO" value="<?php echo ($photo['name'])?>">
            </div>
          </div>

<?php


}

?>


        </div>
        <input type="file" class="filepond" multiple>

        
            </section>
        </section>


    </div>
        
<script>

function next(section) {
    document.querySelector(".paginacion").querySelectorAll("li").forEach(li => li.classList.remove("selected"));
    if (section == 1) {
        document.querySelector(".datos-basicos").style.display = "none";
        document.querySelector(".detalles").style.display = "block";
        document.querySelector(".paginacion").querySelector("li:nth-child(2)").classList.add("selected");
    } else {
        document.querySelector(".datos-basicos").style.display = "none";
        document.querySelector(".detalles").style.display = "none";
        document.querySelector(".fotos").style.display = "block";
        document.querySelector(".paginacion").querySelector("li:nth-child(3)").classList.add("selected");
    }
}

function menu(section) {
    document.querySelector(".paginacion").querySelectorAll("li").forEach(li => li.classList.remove("selected"));

    if (section == 1) {
        document.querySelector(".paginacion").querySelector("li:nth-child(1)").classList.add("selected");
        document.querySelector(".datos-basicos").style.display = "block";
        document.querySelector(".detalles").style.display = "none";
        document.querySelector(".fotos").style.display = "none";
    } else if (section == 2){
        document.querySelector(".paginacion").querySelector("li:nth-child(2)").classList.add("selected");
        document.querySelector(".datos-basicos").style.display = "none";
        document.querySelector(".detalles").style.display = "block";
        document.querySelector(".fotos").style.display = "none";
    } else {
        document.querySelector(".paginacion").querySelector("li:nth-child(3)").classList.add("selected");
        document.querySelector(".datos-basicos").style.display = "none";
        document.querySelector(".detalles").style.display = "none";
        document.querySelector(".fotos").style.display = "block";

    }
}

var tipoPiso = "<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-tipo', true) ?>";
updateTipoPiso(tipoPiso);
  function updateTipoPiso(tipoPiso) {
    document.querySelectorAll('.solochalet').forEach(e => e.style.display='none');
    document.querySelectorAll('.solopiso').forEach(e => e.style.display='none');
    if (tipoPiso === "Piso" || tipoPiso === "Atico") {
      document.querySelectorAll('.solopiso').forEach(e => e.style.display='block'); 
      document.querySelector(".check").querySelectorAll('.solopiso').forEach(e => e.style.display='flex'); 
    } else if (tipoPiso === "Casa" || tipoPiso.indexOf("Chalet") === 0) {
      document.querySelectorAll('.solochalet').forEach(e => e.style.display='block'); 
      document.querySelector(".check").querySelectorAll('.solochalet').forEach(e => e.style.display='flex'); 
    }
  }
  //document.querySelectorAll('input').forEach(e => e.setAttribute("readonly", "true"));

  moment.locale("es");
  var choicesObjs = document.querySelectorAll('.js-choice, .js-choices');
  var choicesElements = {};
  for (var i = 0; i < choicesObjs.length; i++) {
    choicesElements[choicesObjs[i].name] = (new Choices(choicesObjs[i], {
      itemSelectText: 'Click para seleccionar',
      searchEnabled: false,
      shouldSort: false,
      position: 'bottom'
    }));
  }

  function removeImage(url) {
    if (confirm("¿Está seguro de querer eliminarlo?")) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "/file-upload?action=remove-photo-inmueble&inmueble_id=<?php echo $inmueble->ID ?>&photo_url=" + url);

      xhr.onload = function () {
        document.querySelector("[data-photo='" + url + "']").remove();
        updateImgs();
      }

      xhr.send();
    }
  }
  
  function editarCheck(e) {
    var input = e.currentTarget;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/inmueble-xhr?action=update_metadata&inmueble_id=<?php echo $inmueble->ID ?>");

    var formData = new FormData();

    formData.append('metaname', input.getAttribute("name"));
    formData.append('metavalue', input.checked ? "on" : "off");


    xhr.onload = function() {
        input.style.filter = "none";
        input.removeAttribute("readonly");
        
        Toastify({
            text: "Dato actualizado",
            duration: 3000,
            gravity: "bottom", // `top` or `bottom`
            position: "center", // `left`, `center` or `right`
            backgroundColor: "rgb(254, 152, 0)",
            stopOnFocus: true, // Prevents dismissing of toast on hover
            onClick: function(){} // Callback after click
        }).showToast();

    }.bind(input);
    xhr.send(formData);
    input.style.filter = "blur(1px)";
    input.setAttribute("readonly", "true");

  }

  function editarTipoInmueble(e) {
    document.querySelectorAll('.solochalet').forEach(e => e.style.display='none');
    document.querySelectorAll('.solopiso').forEach(e => e.style.display='none');
    if (e.target.value === "Piso" || e.target.value === "Atico") {
      document.querySelectorAll('.solopiso').forEach(e => e.style.display='block'); 
      document.querySelector(".check").querySelectorAll('.solopiso').forEach(e => e.style.display='flex'); 
    } else if (e.target.value === "Casa" || e.target.value.indexOf("Chalet") === 0) {
      document.querySelectorAll('.solochalet').forEach(e => e.style.display='block'); 
      document.querySelector(".check").querySelectorAll('.solochalet').forEach(e => e.style.display='flex'); 
    }

    editar(e);
  }

  function editarLocalizacion(e) {
    editar(e, function () {
      window.location.reload();
    })
  }

  function editar(e, fnCb) {
    var input = e.currentTarget;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/inmueble-xhr?action=update_metadata&inmueble_id=<?php echo $inmueble->ID ?>");

    var formData = new FormData();

    formData.append('metaname', input.getAttribute("name"));
    formData.append('metavalue', input.value);


    xhr.onload = function() {
        input.style.filter = "none";
        input.removeAttribute("readonly");
        if (fnCb) fnCb()
        Toastify({
            text: "Dato actualizado",
            duration: 3000,
            gravity: "bottom", // `top` or `bottom`
            position: "center", // `left`, `center` or `right`
            backgroundColor: "rgb(254, 152, 0)",
            stopOnFocus: true, // Prevents dismissing of toast on hover
            onClick: function(){} // Callback after click
        }).showToast();

    }.bind(input);
    xhr.send(formData);
    input.style.filter = "blur(1px)";
    input.setAttribute("readonly", "true");
  }
  

    FilePond.setOptions({    
      labelIdle: 'Arrastra y suelta tus archivos o <span class = "filepond--label-action"> Examinar <span>',
      labelInvalidField: "El campo contiene archivos inválidos",
      labelFileWaitingForSize: "Esperando tamaño",
      labelFileSizeNotAvailable: "Tamaño no disponible",
      labelFileLoading: "Cargando",
      labelFileLoadError: "Error durante la carga",
      labelFileProcessing: "Cargando",
      labelFileProcessingComplete: "Carga completa",
      labelFileProcessingAborted: "Carga cancelada",
      labelFileProcessingError: "Error durante la carga",
      labelFileProcessingRevertError: "Error durante la reversión",
      labelFileRemoveError: "Error durante la eliminación",
      labelTapToCancel: "toca para cancelar",
      labelTapToRetry: "tocar para volver a intentar",
      labelTapToUndo: "tocar para deshacer",
      labelButtonRemoveItem: "Eliminar",
      labelButtonAbortItemLoad: "Abortar",
      labelButtonRetryItemLoad: "Reintentar",
      labelButtonAbortItemProcessing: "Cancelar",
      labelButtonUndoItemProcessing: "Deshacer",
      labelButtonRetryItemProcessing: "Reintentar",
      labelButtonProcessItem: "Cargar",
      labelMaxFileSizeExceeded: "El archivo es demasiado grande",
      labelMaxFileSize: "El tamaño máximo del archivo es {filesize}",
      labelMaxTotalFileSizeExceeded: "Tamaño total máximo excedido",
      labelMaxTotalFileSize: "El tamaño total máximo del archivo es {filesize}",
      labelFileTypeNotAllowed: "Archivo de tipo no válido",
      fileValidateTypeLabelExpectedTypes: "Espera {allButLastType} o {lastType}",
      imageValidateSizeLabelFormatError: "Tipo de imagen no compatible",
      imageValidateSizeLabelImageSizeTooSmall: "La imagen es demasiado pequeña",
      imageValidateSizeLabelImageSizeTooBig: "La imagen es demasiado grande",
      imageValidateSizeLabelExpectedMinSize: "El tamaño mínimo es {minWidth} × {minHeight}",
      imageValidateSizeLabelExpectedMaxSize: "El tamaño máximo es {maxWidth} × {maxHeight}",
      imageValidateSizeLabelImageResolutionTooLow: "La resolución es demasiado baja",
      imageValidateSizeLabelImageResolutionTooHigh: "La resolución es demasiado alta",
      imageValidateSizeLabelExpectedMinResolution: "La resolución mínima es {minResolution}",
      imageValidateSizeLabelExpectedMaxResolution: "La resolución máxima es {maxResolution}",
    });
    FilePond.registerPlugin(FilePondPluginImageTransform);
    FilePond.registerPlugin(FilePondPluginImagePreview);
    FilePond.registerPlugin(FilePondPluginImageCrop);
    FilePond.setOptions({
      server: '/file-upload?action=upload-photo-inmueble&inmueble_id=<?php echo $_GET["inmueble_id"] ?>',
      allowImageCrop: true,
      imageCropAspectRatio: "16:10"
    });
    function createElementFromHTML(htmlString) {
      var div = document.createElement('div');
      div.innerHTML = htmlString.trim();

      // Change this to div.childNodes to support multiple top-level nodes
      return div.firstChild; 
    }
    var inputElement = document.querySelector('input[type="file"]');
    var pond = FilePond.create( inputElement );
    var pondevent = document.querySelector('.filepond--root');
    pond.onprocessfile = (err, file) => {
      if (!err) {
        const response = JSON.parse(file.serverId);
        var img = `<div class="card-ft" data-photo="${response.url}">
            <div class="iconos" style="display: none">
              <div class="icn-move">
                <i class="fas fa-grip-horizontal"></i>
              </div>
              <div class="icn-del delete-img" onclick="removeImage('${response.url}')">
                <i class="fas fa-times"></i>
              </div>
            </div>
            <div class="card-img">
              <img src="${response.url}" alt="${response.name}" width="100%">
            </div>
            <div class="card-text">
              <input onchange="updateImgs(event)" type="text" name="name-img" placeholder="TITULO" value="${response.name}">
            </div>
          </div>`;

        document.querySelector(".bg-fotos").appendChild(createElementFromHTML(img));
        setTimeout(() => {
          pond.removeFile(file);
        }, 200); 
      }
    }

    pond.onprocessfiles = updateImgs;

    /*-new Splide( '.splide', {
      rewind: true,
      autoplay: true,
      cover: true,
      fixedHeight: 300
    } ).mount();-*/


    function setPrecioRecomendado() {
      var newValue = prompt("Introduzca valoración");
      if (!isNaN(newValue)) {

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/inmueble-xhr?action=update_metadata&inmueble_id=<?php echo $inmueble->ID ?>");

        var formData = new FormData();

        formData.append('metaname', 'inmueble-preciorecomendado');
        formData.append('metavalue', newValue);


        xhr.onload = function() {
            window.location.reload();
            Toastify({
                text: "Dato actualizado",
                duration: 3000,
                gravity: "bottom", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                backgroundColor: "rgb(254, 152, 0)",
                stopOnFocus: true, // Prevents dismissing of toast on hover
                onClick: function(){} // Callback after click
            }).showToast();

        }
        xhr.send(formData);
      }
    }
/*
    const draggable = new Sortable.create(document.querySelector('.bg-fotos'), {
      draggable: '.card-ft',
      handle: '.icn-move',
      animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
	    easing: "cubic-bezier(1, 0, 0, 1)", // Easing for animation. Defaults to null. See https://easings.net/ for examples.
      onEnd: updateImgs
    });*/

    function updateImgs(event) {
      const wrapper = document.querySelector(".bg-fotos");
      const elements = wrapper.querySelectorAll(".card-ft");
      const elementsArray = [];
      elements.forEach((el) => {
        const element = {};
        element.name = el.querySelector("[name='name-img']").value;
        element.url = el.querySelector("img").src;

        elementsArray.push(element);
      }); 
      console.log(elementsArray);

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "/inmueble-xhr?action=actualiza-imagenes&inmueble_id=<?php echo $inmueble->ID ?>");

      var formData = new FormData();

      formData.append('metavalue', JSON.stringify(elementsArray));


      xhr.onload = function() {
          Toastify({
              text: "Dato actualizado",
              duration: 3000,
              gravity: "bottom", // `top` or `bottom`
              position: "center", // `left`, `center` or `right`
              backgroundColor: "rgb(254, 152, 0)",
              stopOnFocus: true, // Prevents dismissing of toast on hover
              onClick: function(){} // Callback after click
          }).showToast();
      }
      xhr.send(formData);
    }


    function undo(e, field, oldValue) {
      e.currentTarget.parentElement.remove();
      document.querySelector("[name='inmueble-" + field + "']").value = oldValue;
      document.querySelector("[name='inmueble-" + field + "']").dispatchEvent(new Event("change"));
    }
    function removeUndo(e, field) {
      e.currentTarget.parentElement.remove();
      document.querySelector("[name='inmueble-" + field + "']").dispatchEvent(new Event("change"));

    }

function undoSelect(e, field, oldValue) {
  e.currentTarget.parentElement.remove();
  choicesElements["inmueble-" + field].setChoiceByValue(oldValue);
  document.querySelector("[name='inmueble-" + field + "']").dispatchEvent(new Event("change"));
}
function removeUndoSelect(e, field) {
  e.currentTarget.parentElement.remove();
  document.querySelector("[name='inmueble-" + field + "']").dispatchEvent(new Event("change"));

}
    </script>
</main><!-- #main -->

<?php
get_footer();