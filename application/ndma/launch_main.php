<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//include("../includes/classes/AllClasses.php");
require_once("../includes/classes/Configuration.inc.php");
include(PUBLIC_PATH . "html/header.php");
?>
<div style="position:fixed;">
    <a>
        <img src="<?php echo PUBLIC_URL ?>images/bg2-1.jpg" style="width:155%;overflow: hidden;">
    </a>
    <video style="display:none;" id="video_1" controls autoplay muted allowfullscreen width="120%">
        <source src="<?php echo PUBLIC_URL ?>videos/animation_firework.mp4" type="video/mp4"> 
    </video>
</div>
<script>
    $(function () {
//        var vid = document.getElementById("video_1");
//        vid.muted = true;

    });
    $("a").click(function () {
       var vid = document.getElementById("video_1");
        vid.muted = false;
        $('#video_1')[0].load();
        $("img").hide();
        $("video").show();
//        $("video").play();
        setTimeout(function(){ 
           window.location.href = "<?php echo APP_URL ?>ndma/launch_login.php";
        }, 12000);

    });
</script>