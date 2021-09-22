<?php
/**
 * Template Name: page-inicio.html
 * The template for displaying inicio.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";

require_once 'self/mobile-detect.php';
$detect = new Mobile_Detect();
if ($detect->isMobile()) {
  require_once 'mbl/page-index-mobile.html.php';

} else {


function myCss() {
    if (current_user_can('administrator')){
        echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/inicio-admin.css?cb=' . generate_random_string() . '">';
    } else {
        echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/inicio.css?cb=' . generate_random_string() . '">';
    }
    
}
add_action('wp_head', 'myCss');
if ( ! function_exists( 'getAllUsersForAdmin' ) ) require_once( get_template_directory() . '/self/users-stuff.php' );


get_header();
?>

<main id="primary" class="site-main">
    <?php
    if (!current_user_can('administrator')){
      $unread_msgs = 0;
      foreach (get_user_meta(get_current_user_id(), 'meta-messages-chat') as $chat_str) {
        $chat = json_decode(wp_unslash($chat_str), true);
        if (!$chat['readed'] && $chat["user"] == "admin") {
          $unread_msgs++;
        }
      }
      function get_own_documentos_solicitados() {
        $arr = array();
        foreach (get_user_meta(get_current_user_id(), 'meta-documento-solicitado-al-cliente') as $meta) {
            $arr[] = json_decode(wp_unslash($meta), true);
        }
        return $arr;
      }
      function get_own_citas() {
        return get_user_meta(get_current_user_id(), 'meta-citas-usuario');
      }
      
      $pending_documents = 0;
      $array_documentos = get_own_documentos_solicitados();
      foreach ($array_documentos as $i => $documento) {
        if (wp_unslash($documento["status"]) != 'fichero-anadido') {
          $pending_documents++;
        }
      }

      $pending_citas = 0;
      $array_citas = get_own_citas();

      foreach ($array_citas as $i => $cita) {
        $cita = json_decode(wp_unslash($cita), true);
        if (wp_unslash($cita["status"]) != 'aceptada-cliente' && wp_unslash($cita["status"]) != 'rechazada-cliente' && wp_unslash($cita["status"]) != 'realizada' && wp_unslash($cita["status"]) != 'descartada') {
          $pending_citas++;
        }
      }

      $ofertas_recibidas = 0;
      $ofertas = get_own_ofertas_recibidas(wp_get_current_user());
      foreach ($ofertas as $user => $ofertas_arr) {
        $ofertas_recibidas += count($ofertas_arr);
      }

?>
    <div class="main">
        <div class="main-container">
        <div class="texto-cabecera">
          <h2>Resumen de Actuación</h2>
          </div>
          <hr>
                <div class="estadisticas">
                    <div class="visualizaciones">
                        <button onclick="location.href='/ofertas-recibidas'">
                        <a href="#">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>lupa.png" width="100%">
                        <h2>Ofertas Recibidas</h2>
                        <p><span id="ofertas_recibidas"><?php echo $ofertas_recibidas ?></span> Ofertas</p>
                        </a>
                        </button>
                    </div>
                    <div class="contacto-email">
                        <button onclick="location.href='/mensajes'">
                        <a href="/mensajes">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>email2.png" width="100%">
                        <h2>Mensajes sin Leer</h2>
                        <p><span id="unread_msgs"><?php echo $unread_msgs ?></span> Mensajes Sin Leer</p>
                        </a>
                        </button>
                    </div>
                    <div class="calendario">
                        <button onclick="location.href='/citas'">
                        <a href="/citas">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>schedule.png" width="100%">
                        <h2>Citas por Confirmar</h2>
                        <p><span id="pending_citas"><?php echo $pending_citas ?></span> Citas sin Confirmar</p>
                        </a>
                        </button>
                    </div>
                    <div class="citas">
                        <button onclick="location.href='/mis-documentos'">
                        <a href="/mis-documentos">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png" width="100%">
                        <h2>Doc Requeridos</h2>
                        <p><span id="pending_documents"><?php echo $pending_documents ?></span> Doc Requeridos</p>
                        </a>
                        </button>
                    </div>
                </div>
            </div>

        <div class="main-container-sub">
            <h2>Evolución de la Vivienda</h2>

            <hr>
            <div class="estadisticas-sub">
                <div class="precio-medio">
                    <p>Precio Medio de Venta en la Zona</p>
                    <canvas id="main-graph"></canvas>
                </div>
                       <div class="main-up-inmuebles">
<?php 
    $inmuebles_of_user = getInmueblesOfUser(wp_get_current_user());
    $inmuebles_loc_ids = [];
    foreach($inmuebles_of_user as $inmueble) {
      $inmuebles_loc_ids[] = [
        "ccaa" => get_post_meta($inmueble->ID, 'meta-inmueble-ccaa', true),
        "provincia" => get_post_meta($inmueble->ID, 'meta-inmueble-provincia', true),
        "municipio" => get_post_meta($inmueble->ID, 'meta-inmueble-municipio', true),
        "poblacion" => get_post_meta($inmueble->ID, 'meta-inmueble-poblacion', true),
        "precioestimado" => get_post_meta($inmueble->ID, 'meta-inmueble-precioestimado', true),
        "preciorecomendado" => get_post_meta($inmueble->ID, 'meta-inmueble-preciorecomendado', true),
        "metros" => get_post_meta($inmueble->ID, 'meta-inmueble-m2construidos', true),
      ];

?>
                            <div class="card-wrapper">
                             <button>
                                <a href="/perfil-inmueble?inmueble_id=<?php echo $inmueble->ID ?>">
                                <img src="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-foto-principal', true); ?>" alt="Avatar" style="width:100%">
                                <div class="box-text">
                                  <h3><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-destino', true); ?></h3>
                                  <h4><b><?php echo number_format(get_post_meta($inmueble->ID, 'meta-inmueble-precioestimado', true), 2, ",", "."); ?> €</b></h4>
                                  <p><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-descripcion', true); ?></p>
                                </div>
                               </a>
                              </button>
                            </div>
<?php
    }

?>
                     </div>
              </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    var inmueblesLocIds = <?php echo json_encode($inmuebles_loc_ids); ?>;
    const requests = inmueblesLocIds.map(inmuebleData => {
      const mostSpecific = inmuebleData.poblacion || inmuebleData.municipio || inmuebleData.provincia || inmuebleData.ccaa;

      return fetch("/inmueble-xhr?action=get_graph&id=" + mostSpecific).then(res => res.json())

    });
    var colors = ["#007bff","#6610f2", "#6f42c1","#e83e8c","#dc3545","#fd7e14"," #ffc107"," #28a745","#20c997", "#17a2b8","#fff","#6c757d","#343a40"," #007bff","#6c757d", "#343a40","#007bff","#6c757d","#28a745","#17a2b8","#dc3545"," #f8f9fa"," #343a40"];

    let counter = 0;
    Promise.all(requests).then(async res => {
      const datasets = [];
      res.forEach(r => {
        r.forEach(ds => {
          const name = ds.name + ' (' + ds.level + ')';
          if (datasets.some(d => d.label ===  name)) return;
          datasets.push({
            label: name,
            backgroundColor: colors[(counter) % colors.length],
            borderColor: colors[(counter) % colors.length],
            data: ds.graph.data.map(d => d.y),
            pointRadius: 1
          });
          counter++;
        });
      })
      let lengthColumn = res[0][0].graph.data.length;

      const precios = inmueblesLocIds.map((inmuebleData, idx) => {
        const datasetEstimados = {
          label: 'Precio estimado inm.' + (idx + 1),
          backgroundColor: colors[(counter) % colors.length],
          borderColor: colors[(counter) % colors.length],
          data: new Array(lengthColumn).fill(inmuebleData.precioestimado / inmuebleData.metros),
          pointRadius: 1
        }
        datasets.push(datasetEstimados);
        counter++;

        if (inmuebleData.preciorecomendado) {

          const datasetrecomendado = {
            label: 'Precio recomendado inm.' + (idx + 1),
            backgroundColor: colors[(counter) % colors.length],
            borderColor: colors[(counter) % colors.length],
            data: new Array(lengthColumn).fill(inmuebleData.preciorecomendado / inmuebleData.metros),
            pointRadius: 1
          }
          datasets.push(datasetrecomendado);
          counter++
        }

      });

      const data = {
        labels: res[0][0].graph.data.map(el => el.x),
        datasets
      };
      var formatter = new Intl.NumberFormat('de-DE', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
      });

      const config = {
        type: 'line',
        data,
        options: {
          plugins: {
            legend: {
              display: true,
              align: 'start',
              labels: {
                boxWidth: 10
              }
            },
            tooltip: {
              intersect: false,
              interaction: {
                mode: 'x'
              },
              callbacks: {
                label: function(data) {
                  //var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';
                  //var label = data.labels[tooltipItem.index];
                  return data.dataset.label + ": " + formatter.format(data.raw) + "€/m2";
                }
              }
            }
          }
        }
      };
      var myChart = new Chart(
        document.getElementById("main-graph"),
        config
      );
    });
    
    </script>

<?php
    } else {
      if (get_current_user_id() === 1) {
        $users_of_admin = get_users(array(
          "role" => "subscriber"
        ));
      } else {
        $users_of_admin = get_users(array(
          'meta_key' => 'meta-gestor-asignado',
          'meta_value' => get_current_user_id()
        ));
      }
      $unread_msgs = 0;
      $pending_documents = 0;
      $review_documents = 0;
      $pending_citas = 0;
      foreach ($users_of_admin as $user_of_admin) {
        foreach (get_user_meta($user_of_admin->ID, 'meta-messages-chat') as $chat_str) {
          $chat = json_decode(wp_unslash($chat_str), true);
          if (!$chat['readed'] && $chat["user"] == "user") {
            $unread_msgs++;
          }
        }

        foreach (get_user_meta($user_of_admin->ID, 'meta-documento-solicitado-al-cliente') as $meta) {
          $documento = json_decode(wp_unslash($meta), true);

          if (wp_unslash($documento["status"]) != 'fichero-anadido') {
            $pending_documents++;
          } else {
            if (!($documento["revisado"]) ) {

              $review_documents++;
            }
          }
        }

        foreach (get_user_meta($user_of_admin->ID, 'meta-citas-usuario') as $meta) {
          $cita = json_decode(wp_unslash($meta), true);
          if (strtotime(wp_unslash($cita["fin"])) < time()) {
            if (wp_unslash($cita["status"]) == 'creada' || wp_unslash($cita["status"]) == 'fecha-cambiada') {
              $pending_citas++;
            }
          }
        }
      }
?>
   <div class="main">
   <div class="main-container">
   <div class="texto-cabecera">
          <h2>Resumen de Actuación</h2>
          </div>
          <hr>
        <div class="general">
          <div class="doc-pendientes">
            <button onclick="location.href='/admin-doc'">
              <a href="/admin-doc">
              <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>docs.png" width="100%">
              <h2>Doc Pendientes</h2>
              <p><span id="pending_documents"><?php echo $pending_documents ?></span> Documentos</p>
              </a>
            </button>
          </div>
          <div class="doc-revisar">
            <button onclick="location.href='/admin-doc'">
              <a href="/admin-doc">
              <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>/docs.png" width="100%">
              <h2>Doc Revisar</h2>
              <p><span id="review_documents"><?php echo $review_documents ?></span> Documentos</p>
              </a>
            </button>
          </div>
          <div class="chat-pendientes">
            <button onclick="location.href='/mensajes'">
              <a href="/mensajes">
              <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>/email2.png" width="100%">
              <h2>Chat Pendientes</h2>
              <p><span id="unread_msgs"><?php echo $unread_msgs ?></span> Mensajes sin leer</p>
              </a>
            </button>
          </div>
          <div class="citas">
            <button onclick="location.href='/citas'">
              <a href="/citas">
              <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>/cita.png" width="100%">
              <h2>Citas Sin Actualizar</h2>
              <p><span id="pending_citas"><?php echo $pending_citas ?></span> Citas pendientes</p>
              </a>
            </button>
          </div>
        </div>
      </div>
    </div>

        <?php 
        }
        ?>
</main><!-- #main -->

<script>
  function update() {
    fetch('/usuarios-xhr?action=inicio_data').then(res => res.json()).then(res => {
      Object.keys(res).map(id => {
        document.querySelector("#" + id).innerHTML = res[id];

      });

      if (res.unread_msgs > 0) {
        document.querySelector('.mensajes-wrapper').classList.add('unread');
      } else {
        document.querySelector('.mensajes-wrapper').classList.remove('unread');

      }

      setTimeout(() => {
        update();
      }, 10000);
    });
  }
  update();
</script>

<?php
get_footer();
}
