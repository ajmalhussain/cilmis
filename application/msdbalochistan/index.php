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
                <h3 class="heading">
                    <?php
                    echo "<h1>Welcome: " . $_SESSION['user_name'] . " </h1>";
                    ?>
                </h3>
                       <?php
                        if(!empty($_SESSION['user_role']) && $_SESSION['user_role']==38 || $_SESSION['user_role']==90)
                        {
                        ?>
                        <img src="../../public/images/warehouse.jpg">
                        <?php
                        }
                        ?>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>