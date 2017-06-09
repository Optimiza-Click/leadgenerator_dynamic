<?php
if (!class_exists('Landing_WP')) 
{
	class Landing_WP 
	{	

        public $n_localidades = 10;
        public $n_provincias = 10;
        public $leadgenerator;

        public function __construct() {
            add_action('wp_head', array($this, 'modal'));
            add_action('init', array($this, 'get_parent'));
            add_action('init', array($this, 'init'));
        }

        public function init() {
            $this->leadgenerator = new LeadGenerator_WP();
        }

        public function get_parent() {
            if(isset($_GET['category_id'])) {
                    $args = array('child_of' => $_GET['category_id']);
                    if(count(get_categories($args)) < $this->n_provincias) {
                        $category = get_category($_GET['category_id']);
                        print_r(get_category_link($category));
                    } else {
                        echo (json_encode(get_categories( $args )));
                        }
                     die();
            } elseif(isset($_GET['local_id'])){ 
                $args = array(
                    'category' => $_GET['local_id'],
                    'posts_per_page' => -1
                    );
                    
                    $post = $this->leadgenerator->check_tag($args);
                    $category = get_the_category($post[0]);
   
                if(count(get_posts($args)) > $this->n_localidades) {
                    $category = $this->leadgenerator->category_cheker(get_the_category($post[0]->ID));
                    
                    $args = array(
                        'url' => get_category_link($category->category_parent) . $this->leadgenerator->limpiar(str_replace(' ','-',strtolower($category->name))),
                        'value' => true
                    );

                    print_r(json_encode($args));
                } else {

                    $category = $this->leadgenerator->category_cheker(get_the_category($post[0]->ID));

                    $args = array(
                        'url' =>  get_category_link($category->category_parent) .  $this->leadgenerator->limpiar(str_replace(' ','-',strtolower($category->name))),
                        'value' => false
                    );

                    print_r(json_encode($args));
                   
                }
                die();
            }
        }


        public function modal() {
            global $post;
            if($post->post_name == 'nutricionista-para-adelgazar') {
            wp_enqueue_script( 'simple_modal', plugins_url( 'js/jquery.simplemodal.1.4.4.min.js', __FILE__ ) );
            wp_enqueue_script( 'basic', plugins_url( 'js/basic.js', __FILE__ ) );
            wp_enqueue_style( 'style', plugins_url( 'css/style.css', __FILE__ ) );

            $args_category = array(
            'child_of'           => 0,
            'parent'  => 0,
            'class' => 'pronvincia',
            'id' => 'provincia',
            'orderby' => 'name',
            'order' => 'ASC',
            'show_option_all' => 'PROVINCIA'
            );
        ?>		
		<!-- modal content -->

		<div id="basic-modal-content" style="display:none">
         <img width="227" height="35" src="<?= plugins_url() ?>/<?= $this->leadgenerator->plugin_name ?>/includes/producto/img/separador.png" class="vc_single_image-img attachment-full" style="margin: 0 auto;display: block;padding-bottom: 10px;"alt="">
         <div class="provincia_select">
          <h4 class="message_prov">¿Cuál es tu provincia?</h4>
            <?php wp_dropdown_categories( $args_category ); ?>
            <div id="local" style="display:none">
                <select id="localidad">
                    <option value="">LOCALIDAD</option>
                </select>
                <a id="come_back"><small style="text-align:right;color:#506f67;float:right">< volver atrás</small></a>
               </div>
            </div>
            <div class="codigo_postal" style="display:none">
            <h4 class="message_prov">¿Conoces tu codigo postal?</h4>
                <input id="text_cp" class="code_postal" type="text"></input>
                <a id="categories_display"><small style="color:#506f67;text-decoration: underline;margin: 0 auto;display: table;">no lo conozco / no lo recuerdo</small></a>
            </div>
		</div>
        <?php
            } 
            elseif(isset($_GET['promo'])) 
            {   
             wp_enqueue_script( 'simple_modal', plugins_url( 'js/jquery.simplemodal.1.4.4.min.js', __FILE__ ) );
                wp_enqueue_script( 'basic', plugins_url( 'js/basic.js', __FILE__ ) );
                wp_enqueue_style( 'style', plugins_url( 'css/style.css', __FILE__ ) );

                $query=new WP_Query(array(
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'tag' => $_GET['promo']));

                foreach($query->posts as $post) {

                    $category_by_post[] = $this->leadgenerator->category_cheker_reverse(get_the_category($post));

		        }  

               $provinces = []; 
		?>

                <div id="basic-modal-content" style="display:none">
                <img width="227" height="35" src="<?= plugins_url() ?>/<?= $this->leadgenerator->plugin_name ?>/includes/producto/img/separador.png" class="vc_single_image-img attachment-full" style="margin: 0 auto;display: block;padding-bottom: 10px;"alt="">
                <div class="provincia_select">
                <h4 class="message_prov">¿Cuál es tu provincia?</h4>
                    <select name="cat" id="provincia" class="promo">
                        <option value="0" selected="selected">PROVINCIA</option>

                        <?php foreach($category_by_post as $single_categoy_by_post) {  ?>
                            <?php if(!isset($provincies[$single_categoy_by_post->name])):?>
                                        <option class="level-0" value="<?= get_category_link($single_categoy_by_post->term_id) . '?promo=' . $_GET['promo'] ?>"><?= $single_categoy_by_post->name ?></option>
                            <?php 
                            $provincies[$single_categoy_by_post->name] = 1;
                            endif;?>

                        <?php } ?>


                    </select>
                    <div id="local" style="display:none">
                        <select id="localidad">
                            <option value="">LOCALIDAD</option>
                        </select>
                        <a id="come_back"><small style="text-align:right;color:#506f67;float:right">< volver atrás</small></a>
                    </div>
                    </div>
                    <div class="codigo_postal" style="display:none">
                    <h4 class="message_prov">¿Conoces tu codigo postal?</h4>
                        <input id="text_cp" class="code_postal" type="text"></input>
                        <a id="categories_display"><small style="color:#506f67;text-decoration: underline;margin: 0 auto;display: table;">no lo conozco / no lo recuerdo</small></a>
                    </div>
                </div>
            <?php
            }
        }
    }
new Landing_WP();
}
