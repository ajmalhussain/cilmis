<?php
/**
 * dateToDbFormat
 * 
 * @param type $date
 * @return type
 */
function nice_number($n, $type = '') {

    // first strip any formatting;
    $n = (0 + str_replace(",", "", $n));
    
    // is this a number?
    if (!is_numeric($n))
        return false;

    if (!empty($type)) {
        switch ($type) {
            case 'm':
                return round(($n / 1000000), 2);
                break;
            case 'b':
                return round(($n / 1000000000), 2);
                break;
        }
    } else {
        // now filter it;
        if ($n > 1000000000000)
            return round(($n / 1000000000000), 2) . ' trillion';
        elseif ($n > 1000000000)
            return round(($n / 1000000000), 2) . ' billion';
        elseif ($n > 1000000)
            return round(($n / 1000000), 2) . ' million';
        elseif ($n > 1000)
            return round(($n / 1000), 2) . ' thousand';

        return number_format($n);
    }
}

function dateToDbFormat($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $yy . "-" . $mm . "-" . $dd;
    }
}

/**
 * dateToUserFormat
 * 
 * @param type $date
 * @return type
 */
function dateToUserFormat($date) {
    if (!empty($date)) {
        list($yy, $mm, $dd) = explode("-", $date);
        return $dd . "/" . $mm . "/" . $yy;
    }
}

/**
 * yearFromDate
 * 
 * @param type $date
 * @return type
 */
function yearFromDate($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $yy;
    }
}

/**
 * monthFromDate
 * 
 * @param type $date
 * @return type
 */
function monthFromDate($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $mm;
    }
}

/**
 * dateFormat
 * 
 * @param type $date
 * @param type $string
 * @param type $format
 * @return type
 */
function dateFormat($date, $string, $format) {
    $d = new DateTime($date);
    $d->modify($string);
    return $d->format($format);
}

/**
 * pr
 * @param type $data
 * @param type $exit
 */
function pr($data, $exit = true) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    if ($exit == true) {
        exit;
    }
}

// $url should be an absolute url
/**
 * redirect
 * 
 * @param type $url
 */
function redirect($url) {
    if (headers_sent()) {
        die('<script type="text/javascript">window.location.href="' . $url . '";</script>');
    } else {
        header('Location: ' . $url);
        die();
    }
}


//----------------Functions for GENERIC APPROVAL FLOW ------START-------

$module_approval_status = array();
$module_approval_flow = array();
function fetch_approval_flow(){
    
    $qry = "SELECT
                approval_flow.pk_id,
                approval_flow.module,
                approval_flow.role_id,
                approval_flow.approval_level,
                approval_flow.display_approve,
                approval_flow.display_reject,
                roles.role_name
            FROM
                approval_flow
            INNER JOIN roles ON approval_flow.role_id = roles.pk_id
            ORDER BY
                approval_flow.approval_level ASC,
                approval_flow.module ASC
   ";
    $res = mysql_query($qry);
    global $module_approval_flow;
    while ($row = mysql_fetch_assoc($res)) {
        $module_approval_flow[$row['module']][$row['approval_level']][$row['role_id']] = $row['role_name'];
    }
     
}
function fetch_approval_status($module){
    
    $qry = "SELECT
                approval_log.pk_id,
                approval_log.module,
                approval_log.unique_id,
                approval_log.approval_level,
                approval_log.approval_by,
                approval_log.approval_on,
                approval_log.comments,
                approval_log.updated_status
            FROM
                approval_log
            WHERE
                approval_log.module = '".$module."'
            ORDER BY 
                approval_log.unique_id ASC,
                approval_log.pk_id ASC ";
    $res = mysql_query($qry);
    global $module_approval_status;
    while ($row = mysql_fetch_assoc($res)) {
        $module_approval_status[$row['unique_id']] = $row;
    }
}
function show_approval_btn($module,$unique_id){
    global $module_approval_flow;
    global $module_approval_status;
    $curr_app = (!empty($module_approval_status[$unique_id]['approval_level']) ? $module_approval_status[$unique_id]['approval_level']:0);
    $next_app = $curr_app + 1;

    $return='';
    if(!empty($module_approval_flow[$module][$next_app][$_SESSION['user_role']])){
        $return = '<a href="approval_screen.php?module='.$module.'&unique_id='.$unique_id.'&approval_level='.$next_app.'" class="badge badge-error" style="padding-bottom:20px !important;"><i class="fa fa-edit"></i> Approve</a>';
    }
    return $return;
}
function show_approval_status($module,$unique_id){
    global $module_approval_flow;
    global $module_approval_status;
    $curr_app = (!empty($module_approval_status[$unique_id]['approval_level']) ? $module_approval_status[$unique_id]['approval_level']:0);
    
    if(!empty($module_approval_status[$unique_id]['updated_status']) && $module_approval_status[$unique_id]['updated_status'] == 'reject'){
        $next_app = $curr_app - 1;
    }else{
        $next_app = $curr_app + 1;
    }
    
    if(!empty($module_approval_flow[$module][$next_app])){
        $return = '';
        if(!empty($module_approval_status[$unique_id]['updated_status']) && $module_approval_status[$unique_id]['updated_status'] == 'reject'){
            $return .= '<span class="badge badge-danger show_app_history" module="'.$module.'" unique_id="'.$unique_id.'" style="height:auto !important;">';
            $return .= 'Rejected by: '.implode(" OR ",$module_approval_flow[$module][$curr_app]).'.</span>';
        }
        $return .= '<span class="badge badge-dark show_app_history" module="'.$module.'" unique_id="'.$unique_id.'" style="height:auto !important;">Pending approval from:<br/>'.implode(" <br/>OR ",$module_approval_flow[$module][$next_app]).'</span>';
    }else{
        $return = '<span class="badge badge-success show_app_history" module="'.$module.'" unique_id="'.$unique_id.'">Approved</span>';
    }
    return $return;
    
}

//----------------Functions for GENERIC APPROVAL FLOW -------END------







?>