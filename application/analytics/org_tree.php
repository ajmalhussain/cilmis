<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
?>
<!DOCTYPE html>

<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <title>LMIS - Org Heirarchy</title>
    <style>
        html, body {
    margin: 0px;
    padding: 0px;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

#tree {
    width: 100%;
    height: 100%;
}


/*partial*/
#tree {
    font-family: 'Gochi Hand', cursive;
}
    </style>

</head>
<body>
    
<link href="https://fonts.googleapis.com/css?family=Gochi+Hand" rel="stylesheet">

<script src="https://balkangraph.com/js/latest/OrgChart.js"></script>

<div id="tree"></div>
    <script>
    
window.onload = function () { 
    var chart = new OrgChart(document.getElementById("tree"), {
        template: "belinda",//derek,polina,belinda
        enableDragDrop: true,
        layout: OrgChart.tree,
        align: OrgChart.ORIENTATION,
        menu: {
            pdf: { text: "Export PDF" },
            png: { text: "Export PNG" },
            svg: { text: "Export SVG" },
            csv: { text: "Export CSV" }
        },
        nodeMenu: {
            details: { text: "Details" },
            add: { text: "Add New" },
            edit: { text: "Edit" },
            remove: { text: "Remove" },
        },
        nodeBinding: {
            field_0: "name",
            field_1: "title",
            img_0: "img",
            field_number_children: "field_number_children"
        },
        nodes: [
            <?php
             $qry_in ="
                SELECT
tbl_locations.PkLocID,
tbl_locations.LocName,
tbl_locations.LocLvl,
tbl_locations.ParentID,
tbl_locations.LocType,
tbl_locationtype.LoctypeName,
tbl_locationtype.TypeLvl
FROM
tbl_locations
INNER JOIN tbl_locationtype ON tbl_locations.LocType = tbl_locationtype.LoctypeID
WHERE
tbl_locations.PkLocID NOT IN (9) 
ORDER BY
tbl_locationtype.TypeLvl ASC,
tbl_locations.LocLvl ASC

";
            $qryRes_wh = mysql_query($qry_in); 
            $c =0 ;
            while ($row = mysql_fetch_assoc($qryRes_wh)) {
                echo '{ id: '.$row['PkLocID'];
                if($c>0){
                    echo ', pid: '.$row['ParentID'];
                }
                $c++;
                echo ', name: "'.$row['LocName'].'", title: "'.$row['LoctypeName'].'", img: "" },';


            }
            ?>
            
        ]
    });
};

    </script>
</body>
</html>
