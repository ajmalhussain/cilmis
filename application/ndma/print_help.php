<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

?>
</head>
 <body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
              <div class="portlet light">
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12 blog-page">
							<div class="row">
                                                            <div class="col-md-3 blog-sidebar">
									<h3 style="margin-top:0;">How to:</h3>
									<div class="top-news">
										
										<a href="javascript:;" class="btn blue">
										<span>
										Step 1</span>
										<em>Click on 'More Settings'</em>
										<em>
										
										</a>
									</div>
									 
								</div>
								<div class="col-md-9 article-block">
									<div class="blog-tag-data">
										<img style="height:350px" src="<?=PUBLIC_URL?>/images/n1.png" class="img-responsive" alt="">
									</div>
									
								</div>
							</div><hr>  

							<div class="row">
                                                            <div class="col-md-3 blog-sidebar">
									<div class="top-news">
										<a href="javascript:;" class="btn blue">
										<span>
										Step 2</span>
										<em>Change the Scale option to :</em>
										<em> 'Custom'</em>
										
										</a>
										<a href="javascript:;" class="btn blue">
										<span>
										Step 3</span>
										<em>Change the value to '100'</em>
										<em>
										
										</a>
									</div>
									 
								</div>
								<div class="col-md-9 article-block">
									<div class="blog-tag-data">
										<img style="height:310px" src="<?=PUBLIC_URL?>/images/n2.png" class="img-responsive" alt="">
									</div>
									
								</div>
							</div><hr> 
							<div class="row">
                                                            <div class="col-md-3 blog-sidebar">
									<h3 style="margin-top:0;">How to:</h3>
									<div class="top-news">
										
										<a href="javascript:;" class="btn blue">
										<span>
										Step 4</span>
										<em>Press the SAVE button</em>
										<em>
										
										</a>
									</div>
									 
								</div>
								<div class="col-md-9 article-block">
									<div class="blog-tag-data">
										<img  src="<?=PUBLIC_URL?>/images/n3.png" class="img-responsive" alt="">
									</div>
									
								  
								</div>
							</div> <hr> 
						</div>
					</div>
				</div>
			</div>
            </div>
        </div>
    </div>
    <?php
    include PUBLIC_PATH . "/html/footer.php";
    ?>
</body>
</html>