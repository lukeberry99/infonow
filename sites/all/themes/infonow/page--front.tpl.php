<!-- Main Content -->
<?php
print render($page['menu']); 
?>

<div id="main_body">
    <div id="body_centre">
        <div id="area_content">
            <div id="map">
                <img src="/<?php echo drupal_get_path('theme', 'infonow');?>/img/home_screen.jpg" alt="" />
                <?php if($page['menu']): ?>
                <div id='main-menu' class='navigation'>
                    <?php print render($page['menu']); ?>
                </div>
                <?php endif; ?>
            </div>
            <div id="documents_holder">
                <div class="search_holder">
                    <div class="title_holder">
                        <div class="title">Search</div>
                        <div class="toggle down"></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="documents">
                    <div class="layout_switcher_holder">
                        <div class="switch gallery">
                                <a href="?layout=gallery"></a>
                        </div>
                    </div>
                    <ul class="doc_list">
                        <li>
                            <div class="icon">
                                <a href="highland_risk_assessment_13_-_industrial_wiring.doc" target="_blank"><img src="/<?php echo drupal_get_path('theme', 'infonow'); ?>/img/icon-word.gif" alt="DOC (Microsoft Word Document) File" title="DOC (Microsoft Word Document) File" /></a>
                            </div>
                            <div class="text">
                                <div class="title"><a href="highland_risk_assessment_13_-_industrial_wiring.doc" target="_blank">Test Document</a></div>
                                <div class="sub_title">Test Document</div>
                            </div>
                            <div class="clear"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
            </div></div>
    </div>
</div>
<?php 
    $elements = drupal_get_form("user_login"); 
    $form = drupal_render($elements);
    echo $form;
?>
