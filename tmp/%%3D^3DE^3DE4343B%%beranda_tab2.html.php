<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:43
         compiled from SITTI/reporting/beranda_tab2.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'SITTI/reporting/beranda_tab2.html', 446, false),array('modifier', 'stripslashes', 'SITTI/reporting/beranda_tab2.html', 528, false),array('modifier', 'strip_tags', 'SITTI/reporting/beranda_tab2.html', 528, false),array('modifier', 'date_format', 'SITTI/reporting/beranda_tab2.html', 536, false),array('function', 'secureurl', 'SITTI/reporting/beranda_tab2.html', 531, false),)), $this); ?>
<script type="text/javascript" src="js/jquery_swfobject.js"></script>
<!-- Additional files for the Highslide popup effect -->
<script type="text/javascript" src="js/charts/highslide-full.min.js"></script>
<script type="text/javascript" src="js/charts/highslide.config.js" charset="utf-8"></script>
<script type="text/javascript" src="js/jquery.numberformatter-1.1.0.js"></script>
<link rel="stylesheet" type="text/css" href="js/charts/highslide.css" />
<script>
<?php if ($this->_tpl_vars['tglAwalChart'] && $this->_tpl_vars['tglAkhirChart']): ?>
var default_tgl_awal = <?php echo $this->_tpl_vars['tglAwalChart']; ?>
;
var default_tgl_akhir = <?php echo $this->_tpl_vars['tglAkhirChart']; ?>
;
<?php endif; ?>

var chart_no_latest_30days_data = "<?php echo $this->_tpl_vars['chart_no_latest_30days_data']; ?>
";
var chart_no_chosen_dates_data = "<?php echo $this->_tpl_vars['chart_no_chosen_dates_data']; ?>
";
var chart_start_date_biggerthan_end_date = "<?php echo $this->_tpl_vars['chart_start_date_biggerthan_end_date']; ?>
";

var c_id = <?php echo $this->_tpl_vars['campaign_id_list']; ?>
;
//alert(c_id.length);

<?php echo '

function DayDiff(date1, date2) {
    return (date2.getTime() - date1.getTime()) / 1000 / 60 / 60 / 24;
}

function DiffDate(date, interval) {
    var ms = date.getTime() - (interval * 1000 * 60 * 60 * 24);
	return new Date(ms);
}

function GetToday() {
	var today = new Date();
	today = DatetoString(today);
	return today;
}

function SetDatepicker(datepicker_obj,date) {
	var realDate = StringtoDate(date);
	$(datepicker_obj).datepicker(\'setDate\', realDate);
}

function StringtoDate(strdate){
	var dateParts = strdate.match(/(\\d+)/g);
	var realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); // months are 0-based!
	
	return realDate;
}

function DatetoString(date){
	var dd = date.getDate();
	var mm = date.getMonth()+1; //January is 0!

	var yyyy = date.getFullYear();
	if(dd<10){dd=\'0\'+dd} if(mm<10){mm=\'0\'+mm} var today = yyyy+\'-\'+mm+\'-\'+dd;
	return today;
}

var f1 = [];
var f2 = [];
$(document).ready(function() {
	   
   //kampanye
   $("#g1").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
   $.get(\'report.php\',{t:101,cache:false}, function(data) {
		f1 = eval(data);

		if (f1 != null && f1[0].stats != null) { // just checking first stat, not so good
            chart1(f1);
        } else {
            $("#g1").html(chart_no_latest_30days_data);
        }
	});

    $("#tglAwalChart,#tglAkhirChart").datepicker({ 
            dateFormat: \'yy-mm-dd\'
        });

    $(\'#tglAwalChart\').bind(\'change\', function () {
        $(\'#tglAkhirChart\').val(\'\');     
    });       
       
    $(\'#tglAkhirChart\').bind(\'change\', function () {
        var tgl_awal = $(\'#tglAwalChart\').datepicker("getDate");
        var tgl_akhir = $(\'#tglAkhirChart\').datepicker("getDate");
        interval = DayDiff(tgl_awal, tgl_akhir);
        
        GetChartData(interval);
		SetExportLink();
		//GetTableData(interval);
    });
	
	$(\'#tglPilihanChart\').bind(\'change\', function () {
		var option = $(\'#tglPilihanChart\').val();
		var akhir = GetToday();
		var awal;
		var interval = 0;
		switch(option){
			case "1":
				interval = 30;
				break;
			case "2":
				interval = 7;
				break;
			case "3":
				interval = 1;
				break;
			case "4":
				interval = -1;
				break;
		}
		
		if (interval==-1){
			awal = "2011-01-01";
			interval = 1;
		}else{
			date_akhir = StringtoDate(akhir);
			date_awal = DiffDate(date_akhir,interval);
			awal = DatetoString(date_awal);
		}
		
		SetDatepicker(\'#tglAwalChart\',awal);
		SetDatepicker(\'#tglAkhirChart\',akhir);
		
		GetChartData(interval);
		SetExportLink();
		//GetTableData(interval);
	});
	
	$(\'#action_button\').bind(\'click\', function () {
		var list = [];
		$("input.checkbox").each(function(){
			if ($(this).attr("checked") == true){
				list.push($(this).val());
			}
		});
		if (list.length>0){
			var msg = "";
			if (list.length==1){
				msg = "Anda akan melakukan perubahan pada kampanye, Lanjutkan?";
			}else{
				msg = "Anda akan melakukan perubahan pada lebih dari satu kampanye, Lanjutkan?";
			}
			var answer = confirm(msg);
			if (answer){
				var action = $("#action option:selected").val();
				switch(action){
					case "active":
						//alert("aktifkan");
						MultiEnable(list);
						break;
					case "disable":
						//alert("nonaktifkan");
						MultiDisable(list);
						break;
					case "delete":
						//alert("hapus");
						MultiDelete(list);
						break;
				}
			}
		}else{
			alert("Tidak ada kampanye yang dipilih!");
		}
    });
});

function MultiEnable(list){
	var id="";
	for(i=0;i<list.length;i++){
		if (id!=""){
			id+=",";
		}
		id+=list[i];
	}
	$.get(\'beranda.php\',{m_enable_c:1, id:id}, function(){
		window.location = "beranda.php?PerformaAkun";
	});
}

function MultiDisable(list){
	var id="";
	for(i=0;i<list.length;i++){
		if (id!=""){
			id+=",";
		}
		id+=list[i];
	}
	$.get(\'beranda.php\',{m_disable_c:1, id:id}, function(){
		window.location = "beranda.php?PerformaAkun";
	});
}

function MultiDelete(list){
	var id="";
	for(i=0;i<list.length;i++){
		if (id!=""){
			id+=",";
		}
		id+=list[i];
	}
	$.get(\'beranda.php\',{m_delete_kampanye:1, id:id}, function(){
		window.location = "beranda.php?PerformaAkun";
	});
}

function SetExportLink(){
	var tglawal = $(\'#tglAwalChart\').val();
	var tglakhir = $(\'#tglAkhirChart\').val();
	$(\'#export_xls\').attr(\'href\',\'report.php?t=201&xls=1&st=0&tgl_awal=\'+tglawal+\'&tgl_akhir=\'+tglakhir);
	$(\'#export_csv\').attr(\'href\',\'report.php?t=201&csv=1&st=0&tgl_awal=\'+tglawal+\'&tgl_akhir=\'+tglakhir);
}

function GetChartData(interval){
	if ($(\'#tglAwalChart\').val() != \'\' && interval > 0) {

		var awal = $(\'#tglAwalChart\').val();
		var akhir = $(\'#tglAkhirChart\').val();

		$("#g1").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		$.get(\'report.php\',{t:101, tgl_awal: awal, tgl_akhir: akhir, cache:false}, function(data) {
			f1 = eval(data);
			if (f1 != null && f1[0].stats != null) { // just checking first stat, not so good
				chart1(f1);
			} else {
				$("#g1").html(chart_no_chosen_dates_data);
			}
			GetTableData(interval);
		});

	} else {
		$(\'#tglAkhirChart\').val(\'\');
		if (interval <= 0) {
			alert(chart_start_date_biggerthan_end_date);
		}
	}
}

function GetTableData(interval){
	if ($(\'#tglAwalChart\').val() != \'\' && interval > 0) {
		var awal = $(\'#tglAwalChart\').val();
		var akhir = $(\'#tglAkhirChart\').val();
		
		$.get(\'report.php\',{t:201, tgl_awal: awal, tgl_akhir: akhir, cache:false}, function(data) {
			f2 = eval(data);
			if (f2 != null) {
				//alert(f2.length);
				SetTableData(f2);
			} else {
				//alert("no data");
			}
		});
	}
}

function SetTableData(feeds){
	var nn = feeds.length;
	var n = c_id.length;
	var flag;
	var id;
	for(j=0;j<n;j++){
		flag = 0;
		id = c_id[j];
		id = id.replace(/\\./,"\\\\.");
		for(i=0;i<nn;i++){
			if (c_id[j]==feeds[i].enc_campaign_id){
				flag = 1;
				$(\'#imp_\'+id).html(feeds[i].imp).format({format:"#,###", locale:"en"});
				$(\'#click_\'+id).html(feeds[i].click).format({format:"#,###", locale:"en"});
				$(\'#ctr_\'+id).html(feeds[i].ctr+" %");
				$(\'#avg_cpc_\'+id).html(feeds[i].avg_cpc);
				$(\'#row_\'+id).show();
				break;
			}
		}
		if (flag==0){
			$(\'#row_\'+id).hide();
		}
	}
}

function togglec1(){
	try{
       if (f1 != null && f1[0].stats != null) { // just checking first stat, not so good
            chart1(f1);
        } else {
            $("#g1").html(chart_no_latest_30days_data);
        }
        
    }catch(e){}
}

function chart1(feeds){
	try{
		
		  // define the options
		var nn = feeds.length;
		var _series = [];
		var _plots = [];
		var _titles = {\'imp\':\'Jumlah Impresi\',\'click\':\'Jumlah Klik\',\'ctr\':\'Persentase CTR\'};
		for(i=0;i<nn;i++){
			_series.push({name:feeds[i].kampanye});
			_plots.push(feeds[i].stats);
		}

        var options = {
    		exporting:{enabled:false},
            chart: {
                renderTo: \'g1\'
            },
            
            title: {
                text: _titles[$("#cc1").val()]+\' Kampanye Terbaik\'
            },
            
            subtitle: {
                text: \' \'
            },
            
            xAxis: {
                type: \'datetime\',
                tickInterval: 7 * 24 * 3600 * 1000, // one week
                tickWidth: 0,
                gridLineWidth: 1,
                labels: {
                    align: \'left\',
                    x: 3,
                    y: -3 
                }
            },
            
            yAxis: [{ // left y axis
                title: {
                    text: null
                },
                labels: {
                    align: \'left\',
                    x: 3,
                    y: 16,
                    formatter: function() {
                        return Highcharts.numberFormat(this.value, 0);
                    }
                },
                showFirstLabel: false
            }, { // right y axis
                linkedTo: 0,
                gridLineWidth: 0,
                opposite: true,
                title: {
                    text: null
                },
                labels: {
                    align: \'right\',
                    x: -3,
                    y: 16,
                    formatter: function() {
                        return Highcharts.numberFormat(this.value, 0);
                    }
                },
                showFirstLabel: false
            }],
            
            legend: {
                align: \'left\',
                verticalAlign: \'top\',
                y: 20,
                floating: true,
                borderWidth: 0
            },
            credits:{enabled:false},
            tooltip: {
                shared: true,
                crosshairs: true
            },
            
            plotOptions: {
				exporting:{enabled:false},
                series: {
                    cursor: \'pointer\',
                    point: {
                        events: {
                            click: function() {
                                hs.htmlExpand(null, {
                                    pageOrigin: {
                                        x: this.pageX, 
                                        y: this.pageY
                                    },
                                    headingText: this.series.name,
                                    maincontentText: Highcharts.dateFormat(\'%b %e, %Y\', this.x) +\':<br/> \'+ 
                                        this.y,
                                    width: 200
                                });
                            }
                        }
                    },
                    marker: {
                        lineWidth: 1
                    }
                }
            },
            
            series: _series
        }
        
        var n_plots = _plots.length;
		
		for(i=0;i<n_plots;i++) {
			
            var dd = _plots[i];

            if (dd != null) {
    			var oo = [];
    			for(j=0;j<dd.length;j++){
    				if(dd[j][$(\'#cc1\').val()]==null){
    					dd[j][$(\'#cc1\').val()]= 0.0;
    				}
    				var parts = dd[j].capture_date.match(/(\\d+)/g);
    				var dt = new Date(parts[0],parts[1]-1,parts[2],24);
    		      		
                    oo.push([
                            //Date.parse(dd[j].capture_date), 
    						Date.parse(dt),
                            parseFloat(dd[j][$(\'#cc1\').val()], 10)
                        ]);
    			}
    			options.series[i].data = oo;
            }		
		}
        
        chart = new Highcharts.Chart(options);
        
	}catch(e){
       console.log(e);
	}
 }
</script>
'; ?>


<div style="margin-bottom:20px;">
<table border="0" cellspacing="0" cellpadding="0" class="list zebra">
  <tr class="head2">
    <td>Jumlah Kampanye Aktif</td>
    <td>Total Impresi</td>
    <td>Total CTR</td>
  </tr>
  <tr>
    <td><span class="f18 bold green"><?php if ($this->_tpl_vars['report']['kampanye_aktif'] == 0): ?>-<?php else:  echo ((is_array($_tmp=$this->_tpl_vars['report']['kampanye_aktif'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Kampanye<?php endif; ?></span></td>
    <td> <span class="f18 bold green"><?php if ($this->_tpl_vars['report']['total_impresi'] == 0): ?>-<?php else:  echo ((is_array($_tmp=$this->_tpl_vars['report']['total_impresi'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Impressi<?php endif; ?></span></td>
    <td> <span class="f18 bold green"><?php if ($this->_tpl_vars['report']['total_ctr'] == 0): ?>-<?php else:  echo $this->_tpl_vars['report']['total_ctr']; ?>
%<?php endif; ?></span> </td>
  </tr>
</table>
</div>
<select id="tglPilihanChart">
	<option value="1">1 Bulan Terakhir</option>
	<option value="2">1 Minggu Terakhir</option>
	<option value="3">Hari Kemarin</option>
	</select>
Tanggal <input name="tglAwalChart" id="tglAwalChart" class="inputText" style="width: 100px;" type="text" value="<?php echo $this->_tpl_vars['tglAwalChart']; ?>
" /> s/d
<input name="tglAkhirChart" id="tglAkhirChart" class="inputText" style="width: 100px;" type="text" value="<?php echo $this->_tpl_vars['tglAkhirChart']; ?>
" />
<a id="export" class="excel" style="width: auto; padding-left: 20px;cursor:pointer;">Export</a>
<div id="exportPop" class="round7" style="background:black; opacity:0.7;display:none;position:absolute;top:220px;right:44px;padding:5px 10px;color:white">
	<a id="export_xls" href="report.php?t=201&xls=1&st=<?php echo $this->_tpl_vars['start']; ?>
" style="color:white;cursor:pointer;">Excel</a> | <a id="export_csv" href="report.php?t=201&csv=1&st=<?php echo $this->_tpl_vars['start']; ?>
" style="color:white;cursor:pointer;">CSV</a>
</div>
<div id="akunChartKampanye" style="margin-top: 15px;">
    <div class="shortGrafik">
    <span> Lihat berdasarkan : </span>
        <select id='cc1' onchange="togglec1()">
        <option value="imp">Impresi</option>
        <option value="click">Klik</option>
        <option value="ctr">CTR</option>
        </select>
    </div>
    
    <div id="grafikBlock">
        <div id="g1"> </div>
    </div>
    
</div>
   <div class="titleBerandaZen">
     <a class="tambahIcon" onmouseover="tooltip.show('Pindahkan ke Beranda');" onmouseout="tooltip.hide();"  href="#">&nbsp;</a>
     <!--  sementara di hide <a class="pdf" onmouseover="tooltip.show('Export ke PDF');" onmouseout="tooltip.hide();"  href="#">&nbsp;</a>
     -->
   </div>
   <select id="selector" class="selStyle round7">
   		<option id="none">tidak sama sekali</option>
   		<option id="all">semua</option>
   		<option id="reverse">balikan</option>
   </select>
   <select id="action" class="selStyle round7">
   		<option value="active">aktifkan</option>
   		<option value="disable">nonaktifkan</option>
   		<option value="delete">hapus</option>
   </select>
   <input id="action_button" type="button" value="Lakukan" class="submitButton"/>
<table id="t1" border="0" cellspacing="0" cellpadding="0" class="list zebra" style="margin:5px 0 0;">
	<tr class="head2">
		<td align="center"></td>
		<td>Nama Kampanye/Program</td>
		<td align="center">Status</td>
		<td align="center">Pemutakhiran  Terakhir</td>
		<td align="center"><a href="#" class="shortTitle">Impresi<img
			class="shotIcon" src="images/short_up.png" /></a></td>
		<td align="center"><a href="#" class="shortTitle">Klik<img
			class="shotIcon" src="images/short_down.png" /></a></td>
		<td align="center"><a href="#" class="shortTitle">CTR % <img
			class="shotIcon" src="images/short_down.png" /></a></td>
			</tr>
	<?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['campaign_list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['j']['show'] = true;
$this->_sections['j']['max'] = $this->_sections['j']['loop'];
$this->_sections['j']['step'] = 1;
$this->_sections['j']['start'] = $this->_sections['j']['step'] > 0 ? 0 : $this->_sections['j']['loop']-1;
if ($this->_sections['j']['show']) {
    $this->_sections['j']['total'] = $this->_sections['j']['loop'];
    if ($this->_sections['j']['total'] == 0)
        $this->_sections['j']['show'] = false;
} else
    $this->_sections['j']['total'] = 0;
if ($this->_sections['j']['show']):

            for ($this->_sections['j']['index'] = $this->_sections['j']['start'], $this->_sections['j']['iteration'] = 1;
                 $this->_sections['j']['iteration'] <= $this->_sections['j']['total'];
                 $this->_sections['j']['index'] += $this->_sections['j']['step'], $this->_sections['j']['iteration']++):
$this->_sections['j']['rownum'] = $this->_sections['j']['iteration'];
$this->_sections['j']['index_prev'] = $this->_sections['j']['index'] - $this->_sections['j']['step'];
$this->_sections['j']['index_next'] = $this->_sections['j']['index'] + $this->_sections['j']['step'];
$this->_sections['j']['first']      = ($this->_sections['j']['iteration'] == 1);
$this->_sections['j']['last']       = ($this->_sections['j']['iteration'] == $this->_sections['j']['total']);
?>
		<?php $this->assign('flag', 0); ?>
		<?php $this->assign('index', -1); ?>
		<?php $this->assign('i', 0); ?>
		<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
			<?php if ($this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['campaign_id'] == $this->_tpl_vars['list'][$this->_sections['i']['index']]['campaign_id']): ?>
				<?php $this->assign('flag', 1); ?>
				<?php $this->assign('index', $this->_tpl_vars['i']); ?>
			<?php endif; ?>
			<?php $this->assign('i', $this->_tpl_vars['i']+1); ?>
		<?php endfor; endif; ?>
		
		<tr id="row_<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
" <?php if ($this->_tpl_vars['flag'] == 0): ?>style="display:none"<?php endif; ?>>
			<td align="center">
				<input class="checkbox" name="pilih" type="checkbox" value="<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
" />
			</td>
			<td>
			<div class="AdsNameList" style="float:left;">
				<a class="namaCampaign" href="javascript:void(0);" onclick="onCampaignDetail('<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
');return false;"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['campaign_name'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : stripslashes($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</a>
			</div>
			<div class="actionBtn" style="float:right;">
				<a onmouseover="tooltip.show('Ganti Kampanye');" onmouseout="tooltip.hide();"  class="editPage" href="<?php echo smarty_function_secureurl(array('url' => 'beranda.php','edit_kampanye' => 1,'id' => $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['campaign_id']), $this);?>
">&nbsp;</a>
				<a onmouseover="tooltip.show('Hapus Kampanye');" onmouseout="tooltip.hide();"  class="deletePage" href="<?php echo smarty_function_secureurl(array('url' => 'beranda.php','delete_kampanye' => 1,'id' => $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['campaign_id']), $this);?>
">&nbsp;</a> 
			</div>
			</td>
			<td align="center"><?php if ($this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['status'] == '0'): ?><span class="green">Aktif</span><span class="scoreGreen"><img src="images/green.gif" /></span><?php else: ?><span class="red">Tidak Aktif</span><span class="scoreRed"><img src="images/red.gif" /></span><?php endif; ?></td>
			<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['last_update'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d-%m-%Y %H:%I:%S") : smarty_modifier_date_format($_tmp, "%d-%m-%Y %H:%I:%S")); ?>
</td>
			<td id="imp_<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
" align="center"><?php if ($this->_tpl_vars['flag'] == 1):  echo ((is_array($_tmp=$this->_tpl_vars['list'][$this->_tpl_vars['index']]['imp'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp));  endif; ?></td>
			<td id="click_<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
" align="center"><?php if ($this->_tpl_vars['flag'] == 1):  echo ((is_array($_tmp=$this->_tpl_vars['list'][$this->_tpl_vars['index']]['click'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp));  endif; ?></td>
			<td id="ctr_<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['j']['index']]['enc_campaign_id']; ?>
" align="center"><?php if ($this->_tpl_vars['flag'] == 1):  echo $this->_tpl_vars['list'][$this->_tpl_vars['index']]['ctr']; ?>
 %<?php endif; ?></td>
					</tr>
        
	<?php endfor; endif; ?>
</table>
<?php echo $this->_tpl_vars['paging']; ?>

<?php echo '
	<script type="text/javascript">
		$(\'#export\').toggle(function() {
			$("#exportPop").fadeIn();
		}, function() {
			$("#exportPop").fadeOut();
		});
		$(\'#exportPop a\').click(function(){
			$("#exportPop").fadeOut();
		});
		$("#selector").change(function () {
	          $("#selector option:selected").each(function () {
	                str = $(this).text();
	                if (str == \'semua\'){
		                $("input.checkbox").each(function(){
		    				$(this).attr("checked","checked");
		    			});
	                }else if(str == "tidak sama sekali"){
	                	$("input.checkbox").each(function(){
	        				$(this).attr("checked",false);
	        			});
		            }else if(str == "balikan"){
		            	$("input.checkbox").each(function(){
		    				if ($(this).attr("checked") == true){
		    					$(this).attr("checked",false);
		    				}else if($(this).attr("checked") == false){
		    					$(this).attr("checked",true);
		    				}
		    			});
				    }
	              });
	        });
	</script>
'; ?>
                  