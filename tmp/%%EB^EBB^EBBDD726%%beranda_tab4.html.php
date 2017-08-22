<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:46
         compiled from SITTI/reporting/beranda_tab4.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'SITTI/reporting/beranda_tab4.html', 166, false),array('modifier', 'stripslashes', 'SITTI/reporting/beranda_tab4.html', 245, false),array('modifier', 'strip_tags', 'SITTI/reporting/beranda_tab4.html', 245, false),array('modifier', 'round', 'SITTI/reporting/beranda_tab4.html', 256, false),)), $this); ?>
<script type="text/javascript" src="js/jquery_swfobject.js"></script>
<script>
var c_id = '<?php echo $this->_tpl_vars['c_id']; ?>
';
var f5 = [];
var fi = [];
var fk = [];
var fc = [];
<?php echo '
$(document).stopTime();
function updateInterval(){
	$(document).oneTime(\'15s\', function() {
		$.ajax({ url: "report.php?t=4&status=1", context: document.body, success: function(data){
			if(data==\'1\'){
				$(document).stopTime();
				updateInterval();
			}else if(data==\'0\'){
				$(document).stopTime();
				onTab(\'#PerformaKataKunci\',\'nav3\');
			}
		}});
	});
}
$(document).ready(function() {
		if (c_id==\'none\'){
			//kampanye
		   $.get(\'report.php\',{t:103,n:10,cache:false}, function(data) {
				f5 = eval(data);
				if (f5 != null && f5.length > 0) {
					for (var i = 0; i < f5.length; i++){
						if (f5[i].imp){
							fi.push(f5[i]);
						}
						if (f5[i].click){
							fk.push(f5[i]);
						}
						if (f5[i].ctr){
							fc.push(f5[i]);
						}
					}
					_chart3(fi);
				} else {
					$("#katakunciChartKataKunci").hide();
				}
			});
		}else{
			$.get(\'report.php\',{t:103,n:10,c_id:c_id,cache:false}, function(data) {
				f5 = eval(data);
				if (f5 != null && f5.length > 0) {
					for (var i = 0; i < f5.length; i++){
						if (f5[i].imp){
							fi.push(f5[i]);
						}
						if (f5[i].click){
							fk.push(f5[i]);
						}
						if (f5[i].ctr){
							fc.push(f5[i]);
						}
					}
					_chart3(fi);
				} else {
					$("#katakunciChartKataKunci").hide();
				}
			});
		}
		
		$("a#showKeywordByCampaign").click(function() {
			var keyword_report_link = \'report.php?t=4&c_id=\'+$(\'#campaign\').val()+\'&cache=false\';
			$("#tab04").load(keyword_report_link);
		});
	});
function togglec3(sort){
	try{
		if (sort=="imp"){
			_chart3(fi);
		}
		if (sort=="click"){
			_chart3(fk);
		}
		if (sort=="ctr"){
			_chart3(fc);
		}
	}catch(e){}
}
function _chart3(feeds){
	try{
		var _titles = {\'imp\':\'Jumlah Impresi\',\'click\':\'Jumlah Klik\',\'ctr\':\'Persentase CTR\'};
		var _data = [];
		if(feeds.length>0){
			for(var i=0;i<feeds.length;i++){
				//_cat.push(feeds[i].kata);	
				
				_data.push({name:feeds[i].kata,data:[parseFloat(feeds[i][$(\'#cc3\').val()])]}); 
			}
			
			chart = new Highcharts.Chart({
					chart: {
						renderTo: \'g3\',
						defaultSeriesType: \'column\'
					},
					exporting:{enabled:false},
					title: {
						text: _titles[$(\'#cc3\').val()]+\' Kata Kunci Terbaik\'
					},
					subtitle: {
						text: \' \'
					},
					xAxis: {
						categories: [
							\'Kata Kunci\'
						]
					},
					yAxis: {
						min: 0,
						title: {
							text: \'Jumlah\'
						}
					},
					legend: {
						layout: \'vertical\',
						backgroundColor: \'#FFFFFF\',
						align: \'left\',
						verticalAlign: \'top\',
						x: 70,
						y: 70,
						floating: true,
						shadow: true
					},
					credits:{enabled:false},
					tooltip: {
						formatter: function() {
							return \'\'+
								\'Jumlah : \'+ this.y +\'\';
						}
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
				     series: _data
				});
		}else{
			$("#g3").html("No Data");
		}
	}catch(e){}
 }
function deleteKeyword(keyword, c_id){
	$.get(\'beranda.php\',{hapus_keyword:1, c_id:c_id, k:keyword}, function(){
		window.location = "beranda.php?PerformaKataKunci";
	});
}
</script>
'; ?>


<div>
	<div style="margin-bottom:20px;">
		<table border="0" cellspacing="0" cellpadding="0" class="list zebra">
  			<tr class="head2">
    			<td>Total</td>
    			<td>Aktif</td>
    			<td>Non Aktif</td>
  			</tr>
            <tr>
            	<td><span class="f18 bold green"><?php echo ((is_array($_tmp=$this->_tpl_vars['report']['total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Kata</td>
                <td><span class="f18 bold green"><?php echo ((is_array($_tmp=$this->_tpl_vars['report']['aktif'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Kata</td>
                <td><span class="f18 bold green"><?php echo ((is_array($_tmp=$this->_tpl_vars['report']['inaktif'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Kata</td>
            </tr>
		</table>
	</div>
 <table border="0" cellspacing="0" cellpadding="0" class="list zebra" style="margin:0;">
  <tr class="head2">
    <td></td>
  </tr>
  <tr>
    <td>		<div style="float:right;">
    					</div>
    </td>
  </tr>
</table>
</div>

<div id="katakunciChartKataKunci">

	<div class="shortGrafik">
	<span> Lihat berdasarkan : </span>
	<select id='cc3' onchange="togglec3(this.value)">
	<option value="imp">Impresi</option>
	<option value="click">Klik</option>
	<option value="ctr">CTR</option>
	</select>
	</div>

	<div id="grafikBlock">
		<div id="g3"> </div>
	</div>

</div>			
<?php if ($this->_tpl_vars['UpdateInProgress']): ?>
<div class="titleBerandaZen">
<div align="center" style="text-align:left; padding:10px;" class="colLoader">
<div class="loaderBox">
<img src="images/loader_small.gif" class="loaderSmall" /><p>Mohon tunggu, kami sedang memproses perubahan Anda.</p>
</div>
</div>
</div>
<script>
  updateInterval();
</script>
<?php endif; ?>
    <div class="titleBerandaZen">
     <a class="tambahIcon" onmouseover="tooltip.show('Pindahkan ke Beranda');" onmouseout="tooltip.hide();"  href="#">&nbsp;</a>
     <!--  sementara di hide <a class="pdf" onmouseover="tooltip.show('Export ke PDF');" onmouseout="tooltip.hide();"  href="#">&nbsp;</a>
     --><a class="excel" onmouseover="tooltip.show('Export ke CSV');" onmouseout="tooltip.hide();"  href="report.php?t=4&c_id=<?php echo $this->_tpl_vars['c_id']; ?>
&csv=1&st=<?php echo $this->_tpl_vars['start']; ?>
" target="_blank">&nbsp;</a>
   </div>
   <table border="0" cellspacing="0" cellpadding="0" class="list zebra">
	<tr class="head2">
		<td width="250">Kata Kunci</td>
        <td width="250">Terpakai</td>
		<td align="center"><a href="#" class="shortTitle">Impresi<img
			class="shotIcon" src="images/short_up.png" /></a></td>
		<td align="center"><a href="#" class="shortTitle">Klik<img
			class="shotIcon" src="images/short_down.png" /></a></td>
		<td align="center"><a href="#" class="shortTitle">CTR % <img
			class="shotIcon" src="images/short_down.png" /></a></td>
            <td align="center"><a href="#" class="shortTitle">Rata2 CPC <img
			class="shotIcon" src="images/short_down.png" /></a></td>
		<td align="center"><a href="#" class="shortTitle">Rata2 Top 5 CPC <img
			class="shotIcon" src="images/short_down.png" /></a></td>
	</tr>
	<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['top_key']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr>
		<?php if ($this->_tpl_vars['top_key'][$this->_sections['i']['index']]['in_progress'] <> '1'): ?>
			<td style="width:250px;"><div style="width:250px;"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['keyword'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : stripslashes($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
 <a href="javascript:deleteKeyword('<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['keyword'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : stripslashes($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
','<?php echo $this->_tpl_vars['c_id']; ?>
')" class="deletePage" onmouseout="tooltip.hide();" onmouseover="tooltip.show('Hapus Kata kunci');" style="float:right;">&nbsp;</a></div></td>
			<td align="center"><?php if ($this->_tpl_vars['top_key'][$this->_sections['i']['index']]['status'] == '0'): ?><span class="green">Ya</span><span class="scoreGreen"><img src="images/green.gif" /></span><?php else: ?><span class="red">Tidak</span><span class="scoreRed"><img src="images/red.gif" /></span><?php endif; ?></td>
			<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['imp'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</td>
			<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['click'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

			<?php if ($this->_tpl_vars['top_key'][$this->_sections['i']['index']]['klik_change'] > 0 && $this->_tpl_vars['top_key'][$this->_sections['i']['index']]['status'] == '0'): ?>
			<span style="padding-left:10px;"><img src="images/ppc/arrow_up.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['klik_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
			<?php elseif ($this->_tpl_vars['top_key'][$this->_sections['i']['index']]['klik_change'] < 0 && $this->_tpl_vars['top_key'][$this->_sections['i']['index']]['status'] == '0'): ?>
			<span style="padding-left:10px;"><img src="images/ppc/arrow_down.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['klik_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
			<?php endif; ?>
			</td>
			<td align="center"><?php echo $this->_tpl_vars['top_key'][$this->_sections['i']['index']]['ctr']; ?>
 %</td>
			<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['avg_cpc'])) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
</td>
			<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['avg_top5_cpc'])) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
</td>
		<?php else: ?>
			<td style="width:250px;"><div style="width:250px;"><i><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['keyword'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : stripslashes($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</i></div></td>
			<td align="center"><span class="red">sedang proses</span><span class="scoreRed"><img src="images/red.gif" /></span></td>
			<td align="center"><i><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['imp'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</i></td>
			<td align="center"><i><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['click'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</i></td>
			<td align="center"><i><?php echo $this->_tpl_vars['top_key'][$this->_sections['i']['index']]['ctr']; ?>
 %</i></td>
			<td align="center"><i><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['avg_cpc'])) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
</i></td>
			<td align="center"><i><?php echo ((is_array($_tmp=$this->_tpl_vars['top_key'][$this->_sections['i']['index']]['avg_top5_cpc'])) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
</i></td>
		<?php endif; ?>
	</tr>
	<?php endfor; endif; ?>
</table>

<?php echo $this->_tpl_vars['paging']; ?>
