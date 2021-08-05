<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="top-news">
                            <a class="btn yellow-casablanca btn-rounded">
                                <span>
                                    <?php
                                    echo "<div>Welcome: " . $_SESSION['user_name'] . " </div>";
                                    ?> 
                                </span>                                
                                <i class="fa fa-globe top-news-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="portlet">
                    <div class="portlet-body">



                        <div class="well well-sm">
                            <div class="news-blocks">
                                <h3>
                                    <a>
                                        Ministry of National Health Services, Regulations and Coordination (MoNHSR&C) </a>
                                </h3>
                                <p>
                                    Pakistan Logistics Management Information System
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <img src="http://c.lmis.gov.pk/application/nhsrc/ik.png" width="100%"/>
                            </div>
                            <div class="col-md-6">
                                <p style="text-align: justify;">Prime Minister Imran Khan has given the fellow countrymen a five-point plan to fight the war against novel coronavirus which has infected at least 237 people amid fears of a larger outbreak. He said: "We as a nation need to win this war against coronavirus". He asked the people to adopt five precautionary measures to overcome the highly contagious disease.</p>
                                <br>
                                <p style="text-align: justify;">
Special Assistant to the Prime Minister on Health Dr Faisal Sultan said  that people should not panic and instead focus on the positive aspect of the situation. "Ninety-seven to 98% of the people who are infected with the coronavirus recover. 
                                </p><h3>Mission</h3>
<p style="text-align: justify;">Ministry of National Health Services, Regulations and Coordination is committed for helping the people of Pakistan to maintain and improve their health and to make our population among the healthier in the region.
</p><h3>Vision</h3>
<ul><li>Provides efficient, equitable, accessible & affordable health services with the objective to support people and communities to improve their health status.
    </li><li>National and International Coordination in  the field of  Public Health
</li><li>Oversight for regulatory bodies in health sector
</li><li> Population welfare coordination
</li><li>Enforcement of Drugs Laws and Regulations
</li><li> Coordination of all preventive programs, funded by GAVI/GFATM</li></ul>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>