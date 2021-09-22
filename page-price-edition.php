<?php
/**
 * Template Name: page-admin-asesor.html
 * The template for displaying admin-asesor.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

include_once "self/graph-raw.php";
include_once "self/graph-stuff.php";
include_once "self/schema-raw.php";


function myCss() {
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/json-editor/2.5.4/jsoneditor.js" integrity="sha512-9bJkXpGLgRZNxbRXoeGekuQB6Ea7Z0R7BrBRiCP16F8HEPHfjh4B3GjbCditEB4xeBXIKRuAC6KosK/oKDxxgQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';

}
add_action('wp_head', 'myCss');

$ccaaelegida = $_GET["ccaa"];
$provinciaelegida = $_GET["provincia"];
$municipioelegido = $_GET["municipio"];
$poblacionelegida = $_GET["poblacion"];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {

    $entityBody = file_get_contents('php://input');

    saveGraphDataById($poblacionelegida ?: $municipioelegido ?: $provinciaelegida ?: $ccaaelegida, json_decode($entityBody));

}

function selectLocationCreate($field, $values, $label = '', $fn = "editar", $selected) {
?>
    <label for="<?php echo $field; ?>"><?php echo $label ?: $field; ?></label>
    <select class="controls js-choices" onchange="<?php echo $fn ?>(event)">
        <option value="">Elige</option>
<?php
foreach ($values as $key => $value) {
?>
        <option <?php if ($selected == $value["id"]) { echo 'selected="selected"'; }?> value="<?php echo $value["id"] ?>"><?php echo $value["name"] ?></option>
<?php
}
?>
    </select>
<?php
}


get_header();
?>

<main id="primary" class="site-main" style="padding-left: 300px; padding-top: 150px;">
    <div id="editor" />
    <div>
<?php 
    $ccaas = (getCCAA());
    selectLocationCreate("ccaa", $ccaas, "CCAA", "editarCCAA", $ccaaelegida);
?>
    </div>
<?php if ($ccaaelegida) { ?>
    <div>
<?php 
    $provincias = (getPROVINCIA($ccaaelegida));
    selectLocationCreate("provincias", $provincias, "Provincia", "editarProvincia", $provinciaelegida);
?>
    </div>
<?php } ?>
<?php if ($ccaaelegida && $provinciaelegida) { ?>
    <div>
<?php 
    $municipios = (getMUNICIPIO($provinciaelegida));
    selectLocationCreate("municipios", $municipios, "Municipio", "editarMunicipio", $municipioelegido);
?>
    </div>
<?php } ?>
<?php if ($ccaaelegida && $provinciaelegida && $municipioelegido) { ?>
    <div>
<?php 
    $poblaciones = (getPOBLACION($municipioelegido));
    if (count($poblaciones) > 0) {
        selectLocationCreate("poblaciones", $poblaciones, "Población", "editarPoblacion", $poblacionelegida);
    }
?>
    </div>
<?php } ?>
<?php if ($ccaaelegida) { ?>
    <div id="editor"></div>
    <button type="button" onclick="guarda()">Guardar</button>
<?php } ?>
<style>
    #editor input {
        width: 96%;
        padding: 20px;
        display: block;
        font-size: 1.5em;
    }
</style>
<script>

const datosElegidos = <?php echo json_encode(getGraphDataById($poblacionelegida ?: $municipioelegido ?: $provinciaelegida ?: $ccaaelegida)); ?>;
const ultimoElemento = datosElegidos[datosElegidos.length - 1];
const schema = <?php echo returnFullDataOfSchema(); ?>;
const container = document.getElementById("editor")
const options = {
    startval: ultimoElemento,
    schema,
    iconlib: "fontawesome4",
    disable_collapse: true,
    disable_array_delete: true,
    disable_array_reorder: true,
    no_aditional_properties: true,
    disable_edit_json: true,
    disable_properties: true,
    form_name_root: ultimoElemento.name
}
const editor = new JSONEditor(container, options)

function editarCCAA(e) {
    var params = new URLSearchParams(location.search);
    params.set('ccaa', e.detail.value);
    params.delete('provincia');
    params.delete('municipio');
    params.delete('poblacion');
    window.location.search = params.toString();
}
function editarProvincia(e) {
    var params = new URLSearchParams(location.search);
    params.set('provincia', e.detail.value);
    params.delete('municipio');
    params.delete('poblacion');
    window.location.search = params.toString();
}
function editarMunicipio(e) {
    var params = new URLSearchParams(location.search);
    params.set('municipio', e.detail.value);
    params.delete('poblacion');
    window.location.search = params.toString();
}
function editarPoblacion(e) {
    var params = new URLSearchParams(location.search);
    params.set('poblacion', e.detail.value);
    window.location.search = params.toString();
}

function guarda() {
    console.log(editor.getValue());
    fetch('', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(editor.getValue())
    }).then(res => res.text()).then(res => console.log(res));;

}

</script>
</main><!-- #main -->

<?php
get_footer();