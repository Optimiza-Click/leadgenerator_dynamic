<?php
if (!class_exists('Landing_WP'))
{
	class Landing_WP
	{

        public $n_localidades = 10;
        public $n_provincias = 10;
        public $plugin_name = 'leadgenerator_dynamic';

        public function __construct() {
            add_action('wp_footer', array($this, 'modal'));
            add_action('init', array($this, 'get_parent'));
            add_action('init', array($this, 'cp_data'));
        }

        public function cp_data(){
        global $leadgenerator;
            if(isset($_GET['cp_data'])) {

                    $cp = $_GET['cp_data'];

                $provincia = get_category($_GET['provincia']);
                $localidad = get_category($_GET['localidad']);

                    print_r(json_encode(get_site_url() . '/' . $provincia->slug . '/' . str_replace(' ', '-',strtolower($localidad->name) . '/?cp=' . $cp)));

                die();
            }
        }

        public function get_parent() {
            global $leadgenerator;
            if(isset($_GET['category_id'])) {
                    $args = array('child_of' => $_GET['category_id']);
                    $nElements = 0;

                    if(isset($_GET['promo'])){
                        $leadgenerator->init();
                        $elements = $leadgenerator->getCategories();

                        foreach($elements as $element){
                            if($element['category']->term_id == $_GET['category_id'] ){

                                $nStores = $leadgenerator->countStoresByParentCategory($_GET['category_id']);

                                $nElements = count($element['childs']);


                                if($nElements > 1 && $nStores > $leadgenerator->n_localidades){
                                    echo json_encode($element['childs']);
                                } else {
                                    echo (get_category_link($element['category']).'?promo='. "".$_GET['promo']);

                                }
                            }
                        }
                         die();
                    } else {
                        $nElements = count(get_categories($args));
                    }

                    if($nElements < $this->n_provincias) {
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

                    $post = $leadgenerator->check_tag($args);

                    $category = get_the_category($post[0]);

                    if(count(get_posts($args)) > $this->n_localidades && !$leadgenerator->isPromo()) {
                        $category = $leadgenerator->check_category(get_the_category($post[0]->ID));

                        $args = array(
                            'url' => get_site_url() . '/' . strtolower($category->cat_name) . '/' .  strtolower($category->name),
                            'value' => true
                        );

                        print_r(json_encode($args));
                    } else {

                        $category = $leadgenerator->check_category(get_the_category($post[0]->ID));
                        $url = get_category_link($category->category_parent) . $leadgenerator->limpiar(str_replace(' ','-',strtolower($category->name)));

                        if($leadgenerator->isPromo()){
                            $url.='?promo='. "".$_GET['promo'];
                        }
                        $args = array(
                            'url' =>  $url,
                            'value' => false
                        );

                        print_r(json_encode($args));

                    }
                die();
            }
        }


        public function modal() {
            global $post, $leadgenerator;

            if($leadgenerator->get_type_of_post() == NULL) {

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



                $query=new WP_Query($options);


                ?>
                <!-- modal content -->

                <div id="basic-modal-content" style="display:none">
                <img width="227" height="35" src="<?= plugins_url() ?>/<?= $this->plugin_name ?>/includes/producto/img/separador.png" class="vc_single_image-img attachment-full" style="margin: 0 auto;display: block;padding-bottom: 10px;"alt="">
                <div class="provincia_select">
                <h4 class="message_prov">¿Cuál es tu provincia?</h4>

                    <?php if($leadgenerator->isPromo()):?>
                    <select name="cat" id="provincia" class='pronvincia' >
                        <option value="0" selected="selected" >PROVINCIA</option>
                        <?php

                        $hiearchy = $leadgenerator->getCategories();

                        foreach($hiearchy as $term_id => $category) { ?>
                                        <option class="level-0" value="<?= $category['category']->term_id  . '&promo=' . $_GET['promo'] ?>">
                                        <?= $category['category']->name ?></option>
                        <?php } ?>


                    </select>
                    <?php else:?>
                        <?php wp_dropdown_categories( $args_category ); ?>
                    <?php endif;?>


                    <div id="local" style="display:none">
                        <select id="localidad">
                            <option value="">LOCALIDAD</option>
                        </select>
                        <a id="come_back"><small style="text-align:right;color:#506f67;float:right">< volver atrás</small></a>
                    </div>
                    </div>
                    <div class="codigo_postal" style="display:none">
                    <h4 class="message_prov">¿Conoces tu código postal?</h4>
                    <form action="#">
                            <input id="text_cp" class="code_postal" type="text"></input>
                           <button type="button" id="send_cp">Enviar</button>
                    </form>
                        <a id="categories_display"><small style="color:#506f67;text-decoration: underline;margin: 0 auto;display: table;">no lo conozco / no lo recuerdo</small></a>
                        <a id="come_back_cp"><small style="text-align:right;color:#506f67;float:right">< volver atrás</small></a>
                    </div>
                </div>
                <?php
            }

        }
    }
new Landing_WP();
}
