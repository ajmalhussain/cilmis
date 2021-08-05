<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");


if($_REQUEST['module']=='po'){
    $strSql = "SELECT
        sysuser_tab.sysusr_name,
        purchase_order.po_number,
        purchase_order.reference_number,
        purchase_order.created_date
        FROM
        purchase_order
        LEFT JOIN sysuser_tab ON purchase_order.created_by = sysuser_tab.UserID 
        WHERE
        purchase_order.pk_id = '".$_REQUEST['unique_id']."'
    ";
    //echo $strSql;
    $rsSql = mysql_query($strSql);
    $created_data = mysql_fetch_assoc($rsSql);
    $created_by = $created_data['sysusr_name'];
    $created_on = $created_data['created_date'];
    $created_text='';
    $created_text .= 'PO Number : '.$created_data['po_number'].' ('.$created_data['reference_number'].').';
}

$strSql = "SELECT
    approval_log.pk_id,
    approval_log.module,
    approval_log.unique_id,
    approval_log.approval_level,
    approval_log.approval_by,
    approval_log.approval_on,
    approval_log.comments,
    approval_log.updated_status,
    sysuser_tab.sysusr_name
    FROM
    approval_log
    INNER JOIN sysuser_tab ON approval_log.approval_by = sysuser_tab.UserID
    WHERE
    approval_log.module = '".$_REQUEST['module']."' AND
    approval_log.unique_id = '".$_REQUEST['unique_id']."'
";
$rsSql = mysql_query($strSql);
echo '<ul class="timeline">';
if(!empty($created_data['po_number'])){
?>
    <li class="timeline-yellow">
            <div class="timeline-time">
                <span class="date">
                <?=date('Y-m-d',strtotime($created_on))?> </span>
            </div>
            <div class="timeline-icon">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="timeline-body">
                <b>Created</b> by <?=$created_by?>
                <div class="timeline-content">
                        <?=nl2br($created_text)?>
                </div>
            </div>
        </li>
<?php
}
while($row = mysql_fetch_assoc($rsSql)){
    
    $li_cls = 'green';
    $fa_icon = 'comments';
    $st_text = 'APPROVED';
    if($row['updated_status'] == 'reject'){
        $li_cls = 'red';
        $fa_icon = 'times';
        $st_text = 'REJECTED';
    }
    
    ?>
        <li class="timeline-<?=$li_cls?>">
            <div class="timeline-time">
                <span class="date">
                <?=date('Y-m-d',strtotime($row['approval_on']))?> </span>
                <span class="time">
                <?=date('H:i',strtotime($row['approval_on']))?>  </span>
            </div>
            <div class="timeline-icon">
                <i class="fa fa-<?=$fa_icon?>"></i>
            </div>
            <div class="timeline-body">
                <b><?=$st_text?></b> by <?=$row['sysusr_name']?>
                <div class="timeline-content">
                        <?=nl2br($row['comments'])?>
                </div>
            </div>
        </li>

<?php
}

echo '</ul>';
	