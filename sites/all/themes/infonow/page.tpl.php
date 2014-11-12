<!-- Main Content -->
<div id="main_body">
    <div id="body_centre">
        <div id="area_content">
            <div id="map">
            <?php
                if($page['main-nav']):
                    if($node->field_background_image['und'][0]['filename']){ ?>
                        <?='<img src="'.image_style_url('bg', $node->field_background_image['und'][0]['filename']).'" />'?>
<?php } else { ?>

                        <?='<img src="/sites/default/files/styles/bg/public/home_screen.jpg?itok=wvURaPnA" />'?>
<?php
}
                endif;
            ?>
            
                <div id="featured">
                    <div class="section clearfix">
                        <?php print render($page['main-nav']); ?>
                    </div>
                </div>  
            </div>
            <div id="documents_holder">
                <div class="search_holder">
                    <div class="title_holder">
                        <div class="title">File List</div>
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
                        <?php if($page['file-list']): ?>
                            <?php print render($page['file-list']); ?>
                        <?php endif; ?>

                        <!--/*<li>*/-->
                        <!--/*<div class="icon">*/-->
                            <!--/*<a href="highland_risk_assessment_13_-_industrial_wiring.doc" target="_blank"><img src="/<?php echo drupal_get_path('theme', 'infonow'); ?>/img/icon-word.gif" alt="DOC (Microsoft Word Document) File" title="DOC (Microsoft Word Document) File" /></a>*/-->
                        <!--/*</div>*/-->
                        <!--/*<div class="text">*/-->
                            <!--/*<div class="title"><a href="highland_risk_assessment_13_-_industrial_wiring.doc" target="_blank">Test Document</a></div>*/-->
                            <!--/*<div class="sub_title">Test Document</div>*/-->
                        <!--/*</div>*/-->
                        <!--/*<div class="clear"></div>*/-->
                        <!--/*</li>*/-->
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
    </div></div>
</div>
</div>

