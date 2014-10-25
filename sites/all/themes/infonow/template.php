<?php
function get_main_menu($main_menu) {
    print theme('links__system_main_menu', array(
        'links' => $main_menu,
        'attributes' => array(
            'id' => 'main-menu-links',
            'class' => array('links', 'clearfix'),
        ),
        'heading' => array(
            'text' => t('Main menu'),
            'level' => 'h2',
            'class' => array('element-invisible'),
        ),
    ));
}
?>
