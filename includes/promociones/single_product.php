<?php
if (!class_exists('Product_WP_Landing'))
{
	class Product_WP_Landing
	{
    public function __construct() {
        add_action( 'admin_init', array( $this, 'promo_leadgenerator_meta_boxes' ) );
        add_filter( 'save_post', array( $this, 'save_shops_promo_leadgenerator' ), 10, 2 );
        add_action( 'admin_print_scripts', array( $this, 'promo_leadgenerator_admin_js_css') );
        add_shortcode( 'get_meta_value_promo', array( $this, 'get_meta_value_promo') );
    }

    public function promo_leadgenerator_admin_js_css() {
		wp_register_style( 'select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', false, '1.0.0' );
		wp_enqueue_style( 'select2_css' );

        wp_enqueue_style( 'custom_css_css', plugins_url( 'css/custom.css', __FILE__ ) );

		wp_enqueue_script( 'select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
        wp_enqueue_script( 'codes_select2_custom', plugins_url( 'js/custom.js', __FILE__ ));
    }

    public function promo_leadgenerator_meta_boxes() {
        add_meta_box("promo_leadgenerator_shop_meta_boxd", "Select Shop LeadGenerator", array( $this,"add_select_shops_promo_leadgenerator"), "promo-naturhouse", "normal", "low");
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


    public function get_meta_value_promo() {
        global $post;

        $serialize = get_post_meta($post->ID, 'promo_leadgenerator_store_id', true);

        if(in_array($_GET['store_id'], $serialize)) {
                $post = get_posts(array(
                'numberposts'	=> -1,
                'post_type'		=> 'post',
                'meta_key'		=> 'store_id',
                'meta_value'	=> $_GET['store_id']
                ));
            }else {
                $post = get_posts(array(
                'numberposts'	=> -1,
                'post_type'		=> 'post',
                'meta_key'		=> 'store_id',
                'meta_value'	=> $serialize[0]
                ));
            }

            $value = get_post_meta($post[0]->ID, '', false); 
            $cate = $this->check_category(get_the_category($post[0]->ID)); ?>
            <div class="wpb_column vc_column_container vc_col-sm-12 values_import" style="margin-bottom:60px;width: 50%;float: none;margin: 0 auto;">
                <div class="vc_column-inner display">
                    <div class="wpb_wrapper">
                        <div class="wpb_column vc_column_container vc_col-sm-3" style="margin-bottom:20px;">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <img src="<?= plugins_url() ?>/leadgenerator_dynamic/img/casa_logo.png">
                                </div>
                            </div>
                        </div>
                        <div class="wpb_column vc_column_container vc_col-sm-9" style="margin-bottom: 0px;min-height: 85px;">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <h6 class="values_hx">
                                        <a href="">Naturhouse <?= $cate->name ?></a>
                                    </h6>
                                    <p class="values_direccion"><a href="tienda/?store_id=<?= $value['store_id'][0]?>"><?= $value['direc'][0]; ?></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="phone">
                            <button class="see_phone <?= $value['store_id'][0]; ?>" value="<?= $value['store_id'][0]; ?>"><?= $value['tel'][0]; ?></button>
                            <span style="display:none" id="<?= $value['store_id'][0]; ?>"><?= str_replace('.',' ',$value['tel'][0]); ?></span>
                        </div>
                    </div>
                </div>
            </div>  
            <?php
            
        }

    public function add_select_shops_promo_leadgenerator() {
        global $post;
        $custom = get_post_custom( $post->ID );
        $values = get_post_meta($post->ID, '', false);

        $defaults = array(
            'category' => 0,
            'numberposts' => -1,
        );

        $posts = get_posts($defaults);

        foreach($posts as $post) {
            $store_ids[] = array(
                'store_id' => get_post_meta($post->ID, 'store_id', true),
                'poblacion' => $this->check_category(get_the_category($post->ID)),
                'direccion' => get_post_meta($post->ID, 'direc', true),
                'provincia' => $this->category_cheker_reverse(get_the_category($post->ID)),
            );
        }
        ?>
        <div style="display:none" id="prueba"><?= $values['promo_leadgenerator_store_id'][0] ?></div>
        <div id="selections">
        <label>Select Store ID:</label><br />
            <select multiple="multiple" class="select2" id="promo_leadgenerator_store_id" name="promo_leadgenerator_store_id[]">
            <?php foreach($store_ids as $store_id): ?>
                <option name="promo_leadgenerator_store_id_single" value="<?= $store_id['store_id'] ?>"><?= $store_id['store_id'] . ', ' . $store_id['direccion'] . ' - ' . $store_id['poblacion']->name . ' - ' . $store_id['provincia']->name  ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <p>
       
            <label>Banner for shop with promotion:</label><br />
            <textarea cols="150" rows="15" name="bannerpromo" class="width99"><?= @$custom["bannerpromo"][0] ?></textarea>
        </p>
        <p>
            <label>Comentarios:</label><br />
            <textarea cols="150" rows="5" name="comments_promo" class="width99"><?= @$custom["comments_promo"][0] ?></textarea>
        </p>
        <?php
    }

    function save_shops_promo_leadgenerator(){
        global $post;
            if ( $post ) {
            update_post_meta($post->ID, "bannerpromo", $_POST['bannerpromo']);
            update_post_meta($post->ID, "promo_leadgenerator_store_id", $_POST['promo_leadgenerator_store_id']);
            update_post_meta($post->ID, "comments_promo", $_POST["comments_promo"]);


            $meta = get_post_meta($post->ID, 'promo_leadgenerator_store_id', true);

            foreach($meta as $single_meta) {
                $posts_meta = get_posts(array(
                    'numberposts'	=> -1,
                    'post_type'		=> 'post',
                    'meta_key'		=> 'store_id',
                    'meta_value'	=> $meta
                ));
                    foreach($posts_meta as $post_meta) {
                        wp_set_post_tags($post_meta->ID, $post->post_name, true);
                    }
                }
            }
        }

     }
    new Product_WP_Landing();
}