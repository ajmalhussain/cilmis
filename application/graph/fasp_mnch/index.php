<?php
/**
 * index
 * @package fasp
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

?>
<link href="demo.css" rel="stylesheet">
<link href="introjs.css" rel="stylesheet">
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im	
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row well well-dark">
                   <div class="portlet   dark center">
                        <h1 >Forecasting & Quantification</h1> 
                        <h3 >MNCH Products</h3> 
                        <div ><a href="indicator_details.php">( Indicator Details )</a></div> 

                   </div>
                </div> 
                 
                
                <div class="row well " id="mnch_div" style=" ">
                    <div class="col-md-12">
                        <div class="col-md-4">
                    <div class="tiles">
                        
                        
                                      <div   class="tile double bg-green">
                                          <a href="demographics_list.php">
                                              <div class="tile-body">
                                                      <i class="fa fa-bell-o1"><img width="120px" src="images/dmg.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               Demographics
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>
                        
                                    <div  class="tile double bg-red-sunglo">
                                        <a href="product_settings.php?product_category=mnch">
                                              <div class="tile-body">
                                                      <i class="fa fa-cogs1"><img width="80px" src="images/settings.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               Base Settings
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>
                                      <div  class="tile double bg-yellow-crusta">
                                          <a href="forecasting_master.php?product_category=mnch">
                                              <div class="tile-body">
                                                      <i class="fa fa-plus1"><img width="70px" src="images/add.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               New Forecast
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>
                        
                                      <div   class="tile double bg-green">
                                          <a href="forecasting_list.php">
                                              <div class="tile-body">
                                                      <i class="fa fa-list1"><img width="70px" src="images/list.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               Forecast List
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>

                              </div></div>
                        <div class="col-md-2"><img style="padding-top:50px" src="brackets.png"></div>
                        <div class="col-md-4" style="padding-top:70px">
                            <div class="tiles">

                                              <div  adata-intro1="Forecasting form will be displaying the Demographic , Consumption / Morbidity Data , with option for adjustments and remarks. This form will generate a proposed forecasting result." class="tile double-down  bg-yellow-lemon">
                                                  <a href="forecasting_list.php">
                                                      <div class="tile-body"><i class="fa fa-bar-chart-o1"><img width="120px" src="images/fc.png"></i>
                                                      </div>
                                                      <div class="tile-object">
                                                              <div class="name">
                                                                       MNCH Products Forecasting Calculations
                                                              </div>
                                                              <div class="number">

                                                              </div>
                                                      </div>
                                                  </a>
                                              </div>
                                              <div data-intro1="Quantification Form fetches the proposed forecasting result. Fetches Stock on hand , pipeline , and consumption data from cLMIS . And uses this data for quantifying against the prices of units." class="tile double-down bg-blue-madison">
                                                  <a href="forecasting_list.php">
                                                      <div class="tile-body">
                                                          <i class="fa fa-flask1"><img width="100px" src="images/qnt.png"></i>
                                                      </div>
                                                      <div class="tile-object">
                                                              <div class="name">
                                                                       Quantification
                                                              </div>
                                                              <div class="number">

                                                              </div>
                                                      </div>
                                                  </a>
                                              </div>

                            </div>
                                
                        </div>
                        
                        
                    </div>
                </div>
                
                
                
            </div>
        </div>
    </div>
    

    <script src="../../public/js/jquery-1.4.4.js" type="text/javascript"></script>

    <script type="text/javascript" src="intro.js"></script>
    <script>
        $('#mnch_btn').click(function(){
            var x = document.getElementById("mnch_div");
            x.style.display = "block";
            document.getElementById("fp_div").style.display = "none";
            $(this).removeClass("btn-default").addClass("btn-success");
            $('#fp_btn').removeClass("btn-success").addClass("btn-default");
            $('#about_btn').hide();
        });
        $('#fp_btn').click(function(){
            var x = document.getElementById("fp_div");
            x.style.display = "block";
            document.getElementById("mnch_div").style.display = "none";
            $(this).removeClass("btn-default").addClass("btn-success");
            $('#mnch_btn').removeClass("btn-success").addClass("btn-default");
            $('#about_btn').show();
        });
    </script>
</body>
</html>