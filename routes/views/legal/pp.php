<?php  
use MythicalDash\Handlers\ConfigHandler as cfg;
$router->add('/legal/pp', function() {
    global $renderer;
    $renderer->assign("cfg_name", cfg::get('app','name'));
    $renderer->assign("cfg_logo", cfg::get('app','logo'));
    $renderer->assign("cfg_background", cfg::get('app','background'));
    $renderer->assign("cfg_theme", cfg::get('app','theme'));    
    $renderer->assign("cfg_seo_title", cfg::get('seo','title'));
    $renderer->assign("cfg_seo_description", cfg::get('seo','description'));
    $renderer->assign("cfg_seo_keywords", cfg::get('seo','keywords'));
    $renderer->assign("cfg_seo_author", cfg::get('seo','author'));
    $renderer->assign("cfg_seo_image", cfg::get('seo', 'image'));
    
    $renderer->display("legal/pp.html");
});
?>