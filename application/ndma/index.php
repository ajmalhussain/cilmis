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
                                        NDMA - Management Information System </a>
                                </h3>
                                <p>
                                    Pakistan Logistics Management Information System
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <img src="http://c.lmis.gov.pk/application/ndma/IK.jpg" width="100%"/>
                            </div>
                            <div class="col-md-4" valign="center">
                                <p style="text-align: justify;">Prime Minister Imran Khan has given the fellow countrymen a five-point plan to fight the war against novel coronavirus which has infected at least 237 people amid fears of a larger outbreak. He said: "We as a nation need to win this war against coronavirus". He asked the people to adopt five precautionary measures to overcome the highly contagious disease.</p>
                            </div>
                            
                        
                            <div class="col-md-2">
                                <img src="http://c.lmis.gov.pk/application/ndma/ZM.jpg" width="100%"/>
                            </div>
                            <div class="col-md-4" valign="center">
                                <p style="text-align: justify;">
Special Assistant to the Prime Minister on Health Dr Zafar Mirza said  that people should not panic and instead focus on the positive aspect of the situation. "Ninety-seven to 98% of the people who are infected with the coronavirus recover. 
</p>
                            </div>
                            
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <img src="http://c.lmis.gov.pk/application/ndma/DA.png" width="100%"/>
                            </div>
                            <div class="col-md-10" valign="center">
                                <p style="text-align: justify;">National Disaster Management Authority (NDMA) is the lead agency at the Federal level to deal with the whole spectrum of Disaster Management activities. It is the executive arm of the National Disaster Management Commission (NDMC) which has been established under the Chairmanship of the Prime Minister as the apex policy making body in the field of Disaster Management. In the event of a disaster, all stakeholders including Government Ministries/Departments/Organizations, Armed Forces, INGOs, NGOs, UN Agencies work through and form part of the NDMA to conduct one window operations. </p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>