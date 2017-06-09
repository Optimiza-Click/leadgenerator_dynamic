<?php
/*
Plugin Name: LeadGenetor Dynamic
Plugin URI: http://#
Description: Generador de datos
Author: Departamento de Desarrollo
Author URI: http://#
Version: 1.0
Copyright: 2016 - 2017
*/


require_once dirname( __FILE__ ) . '/includes/producto/single_landing.php';
require_once dirname( __FILE__ ) . '/includes/promociones/single_product.php';

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'LeadGenerator_WP' ) ) {
class LeadGenerator_WP extends Landing_WP{

    public function __construct() {
        //get all values in init
        add_action('wp_head', array($this, 'init'));
        add_action('init', array($this, 'promo_taxonomy'));
       

        //print the shortcodes
        add_shortcode( 'get_meta_ubication', array($this, 'get_meta_ubication'));
        add_shortcode( 'get_ubication_loc', array($this, 'get_ubication_loc'));
        add_shortcode( 'get_meta_values', array($this, 'get_meta_values'));
        add_shortcode( 'get_map_and_values', array($this, 'get_map_and_values'));
        add_shortcode( 'display_weight', array($this, 'display_weight'));
        add_shortcode( 'title', array($this, 'title'));
        add_shortcode( 'subtitle', array($this, 'subtitle'));
    
        //rewrite data in form with store id
        add_action( 'init', array($this, 'rewrite_form'));

       add_filter( 'wpcf7_form_tag', array($this,'city_locale'), 10, 2);  
    }

    public $slug;
    public $category;
    public $meta;
    public $category_simple;
    public $store_id;
    public $id;
    public $store_posts;
    public $posts;
    public $column;
    public $cp_id;
    public $cp;
    public $load_template;
    public $plugin_name = 'leadgenerator_dynamic';

//initizalizate
    public function init() {
        global $post;
        $this->id = get_the_ID();
        $this->meta = get_post_meta($post->ID, '',false);

        $this->category_simple = get_category( get_query_var( 'cat' ) );
        $this->slug = get_the_category();
        $this->category = get_category_by_slug($this->slug[0]->slug); 

        $this->store_id = $_GET['store_id'];
        $this->cp_id = $_GET['cp'];

        $this->store_posts = get_posts(array(
            'numberposts'	=> -1,
            'post_type'		=> 'post',
            'meta_key'		=> 'store_id',
            'meta_value'	=> $this->store_id
         ));

        $this->code = get_posts(array(
            'numberposts'	=> -1,
            'post_type'		=> 'post',
            'meta_key'		=> 'cp',
            'meta_value'	=> $this->cp_id
        ));

        $copy = $this->get_type_of_post();

            switch($copy):
                case 'single':
                    $category = $this->check_category(get_the_category());
                    $defaults = array(
                        'numberposts' => -1,
                        'category' => $category->term_id,
                        'order' => 'ASC'
                    );

                    $this->posts = $this->check_tag($defaults);
                break;

                case 'category':
                    $defaults = array(
                        'numberposts' => -1,
                        'category' => $this->category_simple->term_id,
                        'order' => 'ASC'
                    );

                    $this->posts = $this->check_tag($defaults);
                break;

                case 'store_id':
                ?>
                <script>
                    jQuery( document ).ready(function() {
                        jQuery('select option').attr('selected','selected');
                    });
                </script>
                <?php
                    $this->posts = $this->store_posts;
                break;

                case 'cp_id':
                    $this->posts = $this->code;
                break;

                case 'other':
                     $defaults = array(
                        'numberposts' => -1,
                        'category' => 0, 'orderby' => 'date',
                        'order' => 'ASC'
                    );
                    $this->posts = $this->check_tag($defaults);
                break;
            endswitch;

        if(count($this->posts) > 4) {
            $this->column = '3';
        } else {
            $this->column = '6';
        }
        wp_enqueue_script( 'calculator', plugins_url( 'js/calculator.js', __FILE__ ) );
    }

    public function get_type_of_post() {
        global $post;

        $args = array(
            'single' => is_single(),
            'category' => is_category(),
            'store_id' => isset($_GET['store_id']),
            'cp_id' => isset($_GET['cp_id']),
            'other');

        foreach($args as $arr => $key):
            if($key == TRUE) {
                return $arr;
            }
        endforeach;
    }

    public function rewrite_form() { 
        if(isset($_GET['direccion_form'])) {

             $post = get_posts(array(
                'numberposts'	=> -1,
                'post_type'		=> 'post',
                'meta_key'		=> 'direc',
                'meta_value'	=> $_GET['direccion_form']
            ));
            
            $args = array(
                'store_id' => get_post_meta($post[0]->ID, 'store_id', true),
                'nutricionista' => get_post_meta($post[0]->ID, 'nutricionista', true)
            );

            echo json_encode($args);

            die();
        }
    }
        
    public function limpiar($s){
	$s = ereg_replace("[áàâãª]","a",$s);
	$s = ereg_replace("[ÁÀÂÃ]","A",$s);
	$s = ereg_replace("[éèê]","e",$s);
	$s = ereg_replace("[ÉÈÊ]","E",$s);
	$s = ereg_replace("[íìî]","i",$s);
	$s = ereg_replace("[ÍÌÎ]","I",$s);
	$s = ereg_replace("[óòôõº]","o",$s);
	$s = ereg_replace("[ÓÒÔÕ]","O",$s);
	$s = ereg_replace("[úùû]","u",$s);
	$s = ereg_replace("[ÚÙÛ]","U",$s);
	$s = str_replace(" ","-",$s);
	$s = str_replace("ñ","n",$s);
	$s = str_replace("Ñ","N",$s);

	return $s;
    }   

    public function load_template($s) {
        $template = get_post($s);
		$content = $template->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]>', $content);
		echo $content;
    }

    public function check_form_category($category) {
        if ($category[0]->category_parent > 0){
           return $category[1];
        } else {
            return $category[0];
        }
    }

    function city_locale ( $tag, $unused ) {  
    if ( $tag['name'] != 'plugin-list' )  
        return $tag;  
   
    $plugins = $this->posts;
 
  
    if ( ! $plugins )  
        return $tag;  
  
    foreach($plugins as $plugin):
            $direction = get_post_meta($plugin->ID, 'direc',true);  
            $mail = get_post_meta($plugin->ID, 'email',true);  
            
            if($mail != '') {
                $tag['raw_values'][] = $mail; 
                $tag['values'][] = $mail;
            } else {
                $tag['raw_values'][] = 'patrocinios@naturhouse.com'; 
                $tag['values'][] = 'patrocinios@naturhouse.com';
            }
            $tag['labels'][] = $plugin->post_title . ', ' . substr($direction,0,60);
    endforeach;
  
    return $tag;  
    }   

    public function promo_taxonomy() {    
            $labels = array(
                'name' => translate( 'Promociones', 'promo-leadgenerator' ),
                'singular_name' => translate( 'Paginas de promociones', 'promo-leadgenerator' ),
                'add_new' =>  translate( 'Añadir Pagina Promo', 'promo-leadgenerator' ),
                'add_new_item' => translate( 'Añadir nueva Promo', 'promo-leadgenerator' ),
                'edit_item' => translate( 'Editar Promos', 'promo-leadgenerator' ),
                'new_item' => translate( 'Añadir Promo', 'promo-leadgenerator' ),
                'view_item' => translate( 'Ver Promo', 'promo-leadgenerator' ),
                'search_items' => translate( 'Buscar Promo', 'promo-leadgenerator' ),
                'not_found' =>  translate( 'No hay promos', 'promo-leadgenerator' ),
                'not_found_in_trash' => translate( 'No hay promos en la papelera', 'promo-leadgenerator' ),
                'parent_item_colon' => ''
            );
            $args = array( 'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'hierarchical' => false,
                'menu_position' => 10,
                'menu_icon' => 'dashicons-megaphone',
                'supports' => array( 'title', 'editor', 'revisions')
            );

        register_post_type( 'promo-leadgenerator', $args );
    }


        public function register_category_cupon_promo() {
        $labels = array(
            'name'                => _x( 'Cupones', 'promo-leadgenerator-categor' ),
            'singular_name'       => _x( 'Añadir Cupon', 'promo-leadgenerator-categor' ),
            'search_items'        => __( 'Buscar Cupones' ),
            'all_items'           => __( 'Todas los Cupones' ),
            'parent_item'         => __( 'Categorias unidas' ),
            'parent_item_colon'   => __( 'Categorias emparejadas:' ),
            'edit_item'           => __( 'Editar categoria' ), 
            'update_item'         => __( 'Actualizar Categoria' ),
            'add_new_item'        => __( 'Añadir nueva categoria' ),
            'new_item_name'       => __( 'Añadir nombre de categoria' ),
            'menu_name'           => __( 'Cupones' )
        );    
        $args = array(
            'hierarchical'        => true,
            'labels'              => $labels,
            'show_ui'             => true,
            'show_admin_column'   => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'promo_categories' )
        );
        
        register_taxonomy( 'promo_cupon', array( 'promo-leadgenerator' ), $args );
    }
    

    public function meta_ubi($meta) {
         return '"' . $meta['store_id'][0] . '",' . $meta['lat'][0] . ',' . $meta['long'][0];
    }

    public function check_category($category) {
        if ($category[0]->category_parent > 0){
           return $category[0];
        } else {
            return $category[1];
        }
    }



    public function category_cheker_reverse($category) {
         if ($category[0]->category_parent > 0){
            return $category[1];
        } else {
            return $category[0];
        }
    }
//get posts by store_id
    public function get_page_store() {
        if(isset($this->store_id)) {

        $post = get_posts(array(
            'meta_key'   => 'store_id',
            'meta_value' => $this->store_id
        ));

           wp_redirect( esc_url( add_query_arg( 'single', 'true', get_permalink($post[0]->ID) ) ) );      
         exit;
        }
    }

//get the title
    public function title() {
                
        if(is_category()) {

        echo '<h1>' . get_bloginfo() . ' <span>' . $this->category_simple->name . '</span></h1>'; 
        } else {
            $meta_single = get_post_meta($this->store_posts[0]->ID, '',false);
            if(isset($this->store_id) OR isset($this->cp_id)) {    
                 $category = get_the_category($post[0]->ID);
                 echo '<h1>' . get_bloginfo() . ' <span>' . $meta_single['direc'][0] . '</span></h1>';

                } else {
                    $meta = get_post_meta($this->id, '',false);
                    echo '<h1>' . get_bloginfo() . ' <span>' . get_the_title() . '</span></h1>';
                } 
            }
        }


    public function subtitle() {
        $nutri_name = get_post_meta($this->store_posts[0]->ID, 'nutricionista',true);
        if(isset($this->store_id) OR isset($this->cp_id)){
            echo '<div class="text_with_image_background claim">En este centro te espera:<br />' . $nutri_name . '</div>';
            echo '<div class="texto_contact">';
            echo '<h4>Te ayudará a</h4>';
            echo '<h3>alcanzar<br>tus objetivos</h3>';    
            echo '</div>';        
        } else {
            echo '<div class="text_with_image_background claim">Selecciona tu centro Naturhouse más cercano</div>';
            echo '<div class="texto_contact">';
            echo '<h4>Y alcanza tus objetivos</h4>';
            echo '<h3>con la ayuda de<br />tu nutricionista</h3>';    
            echo '</div>'; 
        }
    }


    public function get_map_and_values() { 
        if(is_single()) {
         $category = $this->check_category(get_the_category());
            $defaults = array(
                'numberposts' => -1,
                'category' => $category->term_id,
                'order' => 'ASC'
            );
        $this->posts = $this->check_tag($defaults);
        }
    if(count($this->posts) > 4) { ?>
        <div class="wpb_column vc_column_container vc_col-sm-12" style="margin-bottom:20px;">
             <div class="vc_column-inner display">
                 <div class="wpb_wrapper">
                    <?= $this->get_meta_ubication() ?>
                 </div>
             </div>
        </div>
        <div class="separator normal" style="margin-top: 20px;margin-bottom: 20px;background-color: #424242;opacity: 0;height: 2px;"></div>
        <div class="wpb_column vc_column_container vc_col-sm-12" style="margin-bottom:20px;margin-top:40px;">
             <div class="vc_column-inner display">
                 <div class="wpb_wrapper">
                    <?= $this->get_data_values() ?>
                 </div>
             </div>
        </div>
        <div class="wpb_column vc_column_container vc_col-sm-12" style="margin: 20px 0px;">
            <div class="vc_column-inner ">
                 <div class="wpb_wrapper" style="text-align:center">
                      <a id="seemore">Ver más<br /><i class="fa fa-angle-down" aria-hidden="true" style="font-size: 40px;color: #003e36;"></i></a>
                      <a id="seecompact" style="display:none"><i class="fa fa-angle-up" aria-hidden="true" style="font-size: 40px;color: #003e36;"></i></a>
                 </div>
            </div>
        </div>
        <?php 
        } else {
        ?>
        <div class="wpb_column vc_column_container vc_col-sm-6" style="margin-bottom:20px;">
             <div class="vc_column-inner display">
                 <div class="wpb_wrapper">
                    <?= $this->get_meta_ubication() ?>
                 </div>
             </div>
        </div>
        <div class="wpb_column vc_column_container vc_col-sm-6" style="margin-bottom:20px;">
             <div class="vc_column-inner display">
                 <div class="wpb_wrapper">
                    <?= $this->get_data_values() ?>
                 </div>
             </div>
        </div>
        <?php
        }
    }



//get posts
    public function get_data_values() { 
        wp_enqueue_script( 'loadmore', plugins_url( 'js/more.js', __FILE__ ) );
        if(is_single()) {
            $category = $this->check_category(get_the_category());
                $defaults = array(
                    'numberposts' => -1,
                    'category' => $category->term_id,
                    'order' => 'ASC'
                );

            $this->posts = $this->check_tag($defaults);
                if(count($this->posts) > 4) {
                    $this->column = '3';
                } else {
                    $this->column = '6';
                }
            }
            foreach($this->posts as $post): setup_postdata( $post ); 
                $value = get_post_meta($post->ID, '',false);
                $categories = $this->check_category(get_the_category($post->ID)); ?>
            <div class="wpb_column vc_column_container vc_col-sm-<?= $this->column ?> values_import" style="margin-bottom:60px;display:none;">
                <div class="vc_column-inner display">
                    <div class="wpb_wrapper">
                        <div class="wpb_column vc_column_container vc_col-sm-3" style="margin-bottom:20px;">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <img src="<?= plugins_url() ?>/<?= $this->plugin_name ?>/img/casa_logo.png">
                                </div>
                            </div>
                        </div>
                        <div class="wpb_column vc_column_container vc_col-sm-9" style="margin-bottom: 0px;min-height: 85px;">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <h6 class="values_hx">
                                    <a href="<?php get_category_link($categories->category_parent) . $this->limpiar(str_replace(' ','-',strtolower($categories->name))) ?>"><?= get_bloginfo() . ' ' . strtolower($categories->name) ?></a></h6>
                                    <p class="values_direccion"><a href="tienda/?store_id=<?= $value['store_id'][0]?>"><?= $value['direc'][0]; ?></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="phone">
                            <button class="see_phone <?= $value['store_id'][0]; ?>" value="<?= $value['store_id'][0]; ?>">Ver teléfono</button>
                            <span style="display:none" id="<?= $value['store_id'][0]; ?>"><?= str_replace('.',' ',$value['tel'][0]); ?></span>
                        </div>
                    </div>
                </div>
            </div>  
            <?php
            endforeach; 
        }

//get posts by category
    public function posts($category) {
        $args = array(
            'numberposts'   => -1,
            'category'         => $category->term_id
        );
                    
        return get_posts($args);

    }
    
//get meta values
    public function get_meta_values($args) {

             $meta_val = get_post_meta($args['id'], $args['attrib'], true);
             echo '<p>' . $meta_val . '</p>';

    }


    public function meta_values_posts($posts) {
        foreach($posts as $single_post): 
        
            $direc = get_post_meta($single_post->ID, 'store_id', true);
            $lat = get_post_meta($single_post->ID, 'lat', true);
            $long = get_post_meta($single_post->ID, 'long', true);
            $ubication[] = '"' . $direc . '",' . $lat . ',' . $long;
        endforeach;

        return $ubication;
    }

    public function get_ubication_array($args) {        
        $posts = $this->check_tag($args);
        foreach($posts as $post):
                $meta = get_post_meta($post->ID, '',false);
                $ubication[] = $this->meta_ubi($meta);
        endforeach;

        return $ubication;
    }

    
    public function check_tag($args) {

        if(isset($_GET['promo'])) {
            foreach(get_posts($args) as $post):
                $tag = wp_get_post_tags($post->ID);
                if($tag[0]->name == $_GET['promo']) {
                     $posts[] = $post;
                } 
            endforeach;

            return $posts;
        } else {
            $posts = get_posts($args); 

            return $posts;
        }
    }


    public function display_weight() { ?>
        <div class="bloque3" style="display:none">
        <h2 class="BMI_resultado" id="BMI_resultado">TU IMC ES <span class="bmi_dato">26.23 ></span> > <span class="bmi_exp red">YOU ARE OVERWEIGHT</span></h2>
        <div class="wpb_column vc_column_container vc_col-sm-6">
        <div class="vc_column-inner ">
            <div class="wpb_wrapper" style="border-right: 1px solid;">
                <div class="bloque3_left">
                    <img alt="overweight" src="/wp-content/uploads/2017/01/overweight_last.jpg" id="img_bmi" name="img_bmi" /></p>
                    <div class="bmi_dato bmi_dato_2">26.23</div>
                </div>
            </div>
        </div>
        </div>
        <div class="wpb_column vc_column_container vc_col-sm-6">
        <div class="vc_column-inner ">
        <div class="wpb_wrapper">
            <div class="bloque3_right">
                <div class="definicion_BMI">
                    <p>El IMC(Indice de Masa Corporal) según la OMS es un indicador entre e lpeso y la talla qu se utilzia frecuentemente para identificar el sobrepeso y la obesidad en los adultos.</p>
                    <p>Tanto el sobrepeso como la obesidad son actualmente el 5º factor principal del riesgo de defuncion del mundo. Este test no está recomendado para los niños ni mujeres embarazadas o en periodo de lactancia.</p>
                </div>
                <div class="explicacion_BMI">
                    <b>Te encuentras por encima de tu peso óptimo</b>. Pide asesoramiento en tu centro Naturhouse más cercano, donde te ayudarán a perder los kilos que te sobran hasta alcanzar el peso más adecuado a tu altura, sexo y constitución.
                    </div>
                        <div class="BMI_results">
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="wpb_wrapper">
                                    <div class="BMI_height"><img src="<?= plugins_url() ?>/<?= $this->plugin_name ?>/img/altura.png" class="left"> ALTURA: <span class="resultado_dato" id="height_orig"></span></div>
                                 </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="wpb_wrapper">
                                    <div class="BMI_weight"><img src="<?= plugins_url() ?>/<?= $this->plugin_name ?>/img/peso.png" class="left"> PESO: <span class="resultado_dato" id="weight_orig"></span></div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="wpb_wrapper">
                                    <div class="BMI_target_weight"><img src="<?= plugins_url() ?>/<?= $this->plugin_name ?>/img/objetivo.png" class="left"> OBJETIVO DE PESO: <span class="resultado_dato resultado_dato_target" id="target_weight_orig"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <?php
    }
    
//get meta ubication   
    public function get_meta_ubication($atts = []) {
        $copy = $this->get_type_of_post();

        switch($copy):
                case 'single':
                    $category = $this->check_category(get_the_category());         
                    $args = array(
                        'numberposts' => -1,
                        'category' => $category->term_id,
                    );
                    $zoom = 11;
                break;

                case 'category':
                    $args = array(
                        'category' => $this->category_simple->term_id,
                        'numberposts' => -1
                    );     
                    $zoom = 6;
                continue;

                case 'store_id':
                    $meta = get_post_meta($this->store_posts[0]->ID, '',false);
                    $zoom = 12;
                continue;

                case 'cp_id':
                    $meta = get_post_meta($this->code[0]->ID, '',false);   
                    $zoom = 12;
                continue;

                case 'other':
                    $args = array (
                        'numberposts' => -1,
                    );
                    $zoom = 5;
                continue;
            endswitch;

        $ubication = $this->meta_values_posts($posts);  ?>
        <div id="mapa"></div>
         

                <script type="text/javascript">
                function initialize() {
                var marcadores = [
                <?php if( isset($this->store_id) OR isset($this->cp_id)) { echo '[' . $this->meta_ubi($meta) . '],'; 
                        } else { 
                      foreach($this->get_ubication_array($args) as $value): echo '[' . $value . '],'; endforeach;} ?> ];
                var map = new google.maps.Map(document.getElementById('mapa'), {
                    zoom: <?= $zoom ?>,
                    center: new google.maps.LatLng(
                        <?php  if( isset($this->store_id) OR isset($this->cp_id)) { 
                                    echo $meta['lat'][0] . ',' . $meta['long'][0]; 
                                } elseif(is_single()) {
                                    preg_match_all("/(.*\"),(.*)/", $value, $out); echo $out[2][0]; 
                                } else {
                                    if(!is_category()) {
                                        echo '40.4379332,-3.7495761'; 
                                    } else { 
                                        preg_match_all("/(\"),(.*)/", $value, $output); echo $output[2][0]; 
                                    }
                                 } ?>),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var infowindow = new google.maps.InfoWindow();
                var marker, i;
                for (i = 0; i < marcadores.length; i++) {  
                    marker = new google.maps.Marker({
                    position: new google.maps.LatLng(marcadores[i][1], marcadores[i][2]),
                    map: map
                    });
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(marcadores[i][0]);
                        infowindow.open(map, marker);
                        window.open('tienda/?store_id=' + marcadores[i][0], "_blank");
                    }
                    })(marker, i));
                }
                }
                google.maps.event.addDomListener(window, 'load', initialize);
                </script>
        <?php 
        }
    }
$GLOBALS['leadgenerator'] = new LeadGenerator_WP();
}
?>
