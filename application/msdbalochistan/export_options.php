<div class="row pull-right">
    <div class="col-md-12 right">
        <img class="dont_print" src="<?php echo PUBLIC_URL;?>images/print-16.png" onClick="printContents4()" alt="Print" style="cursor:pointer;" />
        <img class="dont_print" src="<?php echo PUBLIC_URL;?>images/excel-16.png" onClick="generic_export_excel('export', 'sheet 1', '<?php echo (isset($fileName)?$fileName:'export'); ?>')" alt="Excel" style="cursor:pointer;" />
    </div>
</div>
<div class="row" id="export">
<style>
       /* Print styles */
    @media only print
    {
       
        #doNotPrint{display: none !important;}
        .dont_print{display: none !important;}
        .header navbar{display: none !important;}
        .header-menu{display: none !important;}
        .row_filter{display: none !important;}
        .footer{display: none !important;}
            
    }
</style>

<script>
	function printContents4() {
		window.print();
	}
        
        var generic_export_excel = (function () {
	var uri = 'data:application/vnd.ms-excel;base64,',
	template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
	base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) },
	format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
	return function (table, name, filename) {
		
		if (!table.nodeType) table = document.getElementById(table)
			var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
			
			var newA = document.createElement('a');
			newA.setAttribute('id',"dlink");
			document.body.appendChild(newA);
			
			document.getElementById("dlink").href = uri + base64(format(template, ctx));
			document.getElementById("dlink").download = filename+'.xls';
			document.getElementById("dlink").click();
			document.body.removeChild(newA);
		}
})()
	
</script>
