<?php
/*
Plugin Name: Easy Agent Pro Social Share Image Plugin
Plugin URI: http://easyagentpro.com/
Description: This Plugin inserts Open Graph meta tags into the page header for Header Social Share plugin images.
Version: 0.0.1
Author: Yardi Fox
Author URI: http://yardifox.com
License: GPLv2 or later
Text Domain: easyagent
*/
function eap_soc_activate(){


}

register_activation_hook( __FILE__,'eap_soc_activate');
function eap_soc_img_menu(){
    $file = dirname(__FILE__).'/EapSocImg.php';
    $plugin_dir = plugin_dir_url($file);
    add_menu_page('EAP Social Image Meta','EAP Social Image Meta','manage_options','eap-social-meta','eap_soc_meta_settings','dashicons-admin-generic',null);
}
add_action('admin_menu','eap_soc_img_menu');
    /***********************************
     * WP admin Settings page output
     **********************************/
function eap_soc_meta_settings(){
    $metaimg = get_option('eap_soc_img',__(''));

    $output = <<<ADMIN
    <div class="wrap">
    <h2>Easy Agent Pro Social Share Image Settings</h2>
    <div id="icon-themes" class="icon32"></div>
    <div id="eapSocSettings">
        <table class="form-table easi_settings">
            <tr>
                <td colspan="2">
                <p class="description">When the default social share image is selected it will be used if there is no featured image selected for the post or page, otherwise the featured image will be used</p>
                </td>
            </tr>
            <tr>
                <th>
                     <label for="eapSocImg">Default Social Share Image:</label>
                </th>
                <td>
                    <img width="80%" height="" style="max-height:200px; max-width:180px;" src="{$metaimg}" class="soc_img"/>
                    <input name="eap_soc_img" id="eap_soc_img" class="widefat" type="text" size="36"  value="{$metaimg}" />
                    <input class="upload_image_button button button-primary" type="button" value="Upload Image" />
                    <button id="eapSocDel" class="btn button-secondary clear_img_btn" disabled>Clear</button>
                </td>
            </tr>
        </table>
        <button id="eapSocSave" class="btn button-primary">Save</button>
        <div class="loadCont"><div class="load" id="loadingSpin"></div><span class="val callbackNotice"></span></div>
    </div>
    </div>
    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                var cont = $('#eap_soc_img').val();
                if(cont != ''){
                    $('.clear_img_btn').removeAttr('disabled');
                }
                $('.clear_img_btn').on('click',function(e){
                    $('#eap_soc_img').val('');
                    $('img.soc_img').attr('src','');
                });
                $("#eapSocSave").on("click",function(e){
                    var imgurl = $("#eap_soc_img").val();
                    $('.loadCont .load').html('');
                    $('.loadCont .load').fadeIn('fast');
                    var target = document.getElementById('loadingSpin');
                    var spinner = new Spinner({
                        radius:10,
                        width: 8,
                        lines: 7,
                        length: 3,
                        className: 'eapvspin'
                    }).spin(target);
                    var data = {
                        'action':'eapsocmeta_save',
                        'img':imgurl
                    }
                    $.ajax({
                        type:'POST',
                         url:ajaxurl,
                        data:data,
                     success:function(res){
                            $('#loadingSpin').hide();
                            $('.callbackNotice').fadeIn().text('Settings Saved Successfully').delay(800).fadeOut('slow');
                     },
                       error:function(res){

                     }
                    });
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        })(jQuery);
    </script>
ADMIN;

    echo $output;

}

add_action('wp_ajax_eapsocmeta_save','eap_soc_meta_callback');
function eap_soc_meta_callback(){
    $res = array(
        'success'=>true,
        'data'   =>$_POST['img']
    );
    if(isset($_POST['img']))
        update_option('eap_soc_img',$_POST['img']);


    wp_send_json($res);
}
add_action('wp_head','eap_soc_img_head',1,1);
function eap_soc_img_head(){
    if(interface_exists('iHomefinderConstants')){
        $idxTitle = get_query_var(iHomefinderConstants::IHF_TYPE_URL_VAR);
        if($idxTitle != null && "" != $idxTitle && ($idxTitle == "idx-detail")) {

        }else{
            $siurl = (boolean)has_post_thumbnail() ? wp_get_attachment_url( get_post_thumbnail_id()) : __(get_option('eap_soc_img'));
            $meta=<<<META
            <meta property="og:image" content="{$siurl}" id="socImg"/>
META;
            echo $meta;
        }
    }else{
        $siurl = (boolean)has_post_thumbnail() ? wp_get_attachment_url( get_post_thumbnail_id()) : __(get_option('eap_soc_img'));
        $meta=<<<META
            <meta property="og:image" content="{$siurl}" id="socImg"/>
META;
        echo $meta;
    }
}
function eap_soc_meta_scripts(){
    wp_register_script('eap_soc_img',plugin_dir_url(__FILE__).'js/eapsi.js',array('jquery'),'',false);
    $eapsocimgData = array(
        'soc_img'   =>  __(get_option('eap_soc_img')),
        'featured_img'=>(boolean)has_post_thumbnail(),
        'featured_url'=>wp_get_attachment_url( get_post_thumbnail_id()),
        'is_home'=>(boolean)is_home()
    );
    wp_localize_script('eap_soc_img','socData',$eapsocimgData);
    wp_enqueue_script('eap_soc_img');
}

add_action('wp_enqueue_scripts','eap_soc_meta_scripts');

function eap_soc_meta_admin_scripts(){
    wp_enqueue_media();
    wp_enqueue_script('eapas_admin',plugin_dir_url(__FILE__).'js/admin.js',array('jquery'),'',true);
    wp_enqueue_script('eapas_spin',plugin_dir_url(__FILE__).'js/spin.min.js',array('jquery'),'',true);
    wp_enqueue_style('eapas_admin',plugin_dir_url(__FILE__).'css/admin.css');
};

add_action('admin_enqueue_scripts', 'eap_soc_meta_admin_scripts');