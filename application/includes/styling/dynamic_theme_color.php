<?php
//009688 = bluish green
//2272b7 = blue1
//428bca = blue2
$theme_main_color = '#2272b7';
$theme_light_color = '#00bcd4';
$theme_dark_color = '#1a5384';
if(!empty($theme_main_color))
{
    ?>  
    <!--Following style is used to force override all styling from other sources-->
    <style>
        .page-sidebar-menu > li {
                background-color: <?=$theme_main_color?> !important;
        }

        .page-sidebar .page-sidebar-menu .sub-menu > li:hover > a, 
        .page-sidebar .page-sidebar-menu .sub-menu > li.active > a {
                background-color: <?=$theme_light_color?> !important;
        }           
        .page-content-wrapper {
                background: <?=$theme_main_color?> !important;
        }
        .header-menu {
            border-top: 7px solid <?=$theme_main_color?> !important;
            border-bottom: 1px solid <?=$theme_main_color?> !important;
        }
        .footer {
            background-color: <?=$theme_dark_color?> !important;
        }
        ul.page-sidebar-menu li > ul.sub-menu > li > a {
            color: #333 !important;
        }
        .page-sidebar-menu ul {
            background-color: <?=$theme_light_color?> !important;
        }
        ul.page-sidebar-menu > li > a {
            border-top: 1px solid #ffffff !important;
        }
        ul.page-sidebar-menu > li:last-child > a {
            border-bottom: 1px solid white !important;
            border-top: 1px solid white !important;
    /*        border:none !important;*/
        }
        div > .widget > .widget-head {
            background: <?=$theme_main_color?> !important;
            border: 1px solid <?=$theme_main_color?> !important;
        }
        table.table thead .sorting_disabled,
        table.table thead .sorting 
        {
            background: <?=$theme_main_color?> !important;
            color: #FFF;
         }
    </style>
    <?php
}
?>