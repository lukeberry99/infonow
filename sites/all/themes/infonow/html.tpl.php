<!DOCTYPE html5>
<html lang="EN">
  <head>
    <?php print $head; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title><?php print $head_title; ?></title>
      <meta name="description" content="Home" />
      <meta name="keywords" content="" />
      <meta name="viewport" content="target-densitydpi=device-dpi" />
      <?php print $styles; ?>
      <?php print $scripts; ?>
    </head>
    <body id="website_5">
      <div id="skipLinks"><a href="#body_centre">Skip to content</a></div>
      <div id="site_content">
        <div id="header"><div id="hdrInr">
          <div id="hdrLogo">
            <a href="/" title="Focused projects">
              <p>
                <img alt="" src="<?php echo path_to_theme();?>/img/info-now_v1.01_(1).png" style="width: 205px; height: 49px;" />
              </p>
            </a>
        </div>
        <div id="menuMain_holder">
          <div id="menuMain">
            <div class="menuOuter menuOuter1">
              <ul class="menu1">
                <li class="menuItem1 menuItem1First menuItem1Last layout_popup_page"><a href="/help/" title="Help"><span class="menuSpan1">Help</span></a></li>
              </ul>
            </div>
            <div class="clear"></div>
          </div>
          <div class="logout"><a href="/?logout" title="Logout"><div></div></a></div>
          <div class="clear"></div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
    <div id="breadcrumbs">
      <div id="breadcrumbs">
        <ul>
        <li id="breadcrumb_home_link"><a href="/" title="Home" rel="nofollow" class="selected"><img src="<?php echo path_to_theme();?>/img/home_icon.png" /></a></li>
        </ul>
        <div class="clear"></div>
      </div>
    </div>
    <?php print $page_top; ?>
    <?php print $page; ?>
    <?php print $page_bottom; ?>
  </body>
</html>
