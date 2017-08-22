<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:42
         compiled from SITTI/reporting/beranda_tab1_new.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'SITTI/reporting/beranda_tab1_new.html', 36, false),)), $this); ?>
<script type="text/javascript">
<?php echo '
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       
       if(e.style.display == \'block\')
          e.style.display = \'none\';
       	 
       else
          e.style.display = \'block\';
       	  
    }
    function toggle_image(id) {
        var f = document.getElementById(id);
        if(f.style.backgroundImage == \'url("images/images_sorter/plus.png")\')
            f.style.backgroundImage="url(images/images_sorter/minus.png)";
         else
            f.style.backgroundImage="url(images/images_sorter/plus.png)";
     }

     $(function() {
       $("#campaign_count").change( function() {
         $("#content_kampanye_aktif").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
         $("#content_kampanye_aktif").load("report.php?t=11&campaigncount=" + parseInt($(this).val()));
       });
     });

'; ?>

</script>
<h3>Notifikasi Akun</h3>

<?php if (is_array ( $this->_tpl_vars['notif'] )): ?>	
<table cellspacing="0" cellpadding="0" border="0" class="list zebra" style="width:933px;">
 	<tr class="head2 oddRow" onclick="toggle_visibility('showhide1');toggle_image('tombol1');">
    	<td>
				 <span style="float:left"><?php echo ((is_array($_tmp=$this->_tpl_vars['notif'][0]['posted_date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</span>
				 <span id="tombol1" style="background-image:url(images/images_sorter/minus.png);width:10px;height:10px;float:right;margin:4px;"></span>
         </td>
    </tr>
</table>
<div id="showhide1" style="display:block;margin-bottom:10px;">
<table cellspacing="0" cellpadding="0" border="0" class="list zebra noMargin" style="width:933px;">
    
    	<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['notif']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
    		<?php if ($this->_tpl_vars['notif'][$this->_sections['i']['index']]['type_alert'] >= 0): ?>
				<tr class="evenRow">
					<td>
						<span class="circle_small">!</span>Anda memiliki <?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['total']; ?>
 <?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['alert_msg']; ?>

						<p>
							<ul style="margin-left:20px;">
							<?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['notif'][$this->_sections['i']['index']]['list_iklan']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
								<li><a href="beranda.php?PerformaIklan=<?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['list_iklan'][$this->_sections['j']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['list_iklan'][$this->_sections['j']['index']]['nama']; ?>
, keyword: <?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['list_iklan'][$this->_sections['j']['index']]['keyword']; ?>
</a></li>
							<?php endfor; endif; ?>
							</ul>
						</p>
		   			</td>
		   		</tr>
	   		<?php else: ?>
	   			<tr class="evenRow">
					<td>
						<?php if ($this->_tpl_vars['notif'][$this->_sections['i']['index']]['url'] != ''): ?>
							<span class="circle_small">!</span><a target="_blank" href="<?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['message']; ?>
</a>
						<?php else: ?>
							<span class="circle_small">!</span><?php echo $this->_tpl_vars['notif'][$this->_sections['i']['index']]['message']; ?>

						<?php endif; ?>
	   				</td>
	   			</tr>
	   		<?php endif; ?>
	   	<?php endfor; endif; ?>
   
</table>
</div>
<?php else: ?>
<p>Tidak ada notifikasi untuk hari ini.</p>
<?php endif; ?>
<a href="beranda.php?notifikasi=true">Lihat Semua Notifikasi</a>
<div id="tableKampanyeAktif" style="margin-top:35px;">
<h3>Daftar Kampanye Aktif</h3>
<div id="content_kampanye_aktif">
<?php echo $this->_tpl_vars['daftar_kampanye']; ?>

</div>
<br /><br />
  <div style="float:right">
   <form name="form" id="form">
     <select name="campaign_count" id="campaign_count">
       <option value='5' selected="selected">5</option>
       <option value='10'>10</option>
       <option value='20'>20</option>
       <option value='100'>semua</option>
     </select>
   </form>
  </div>
  <div  style="float:right; padding-right:5px;">
  Tampilkan
  </div>
</div>

<hr class="lineBeranda">

<h3>Forum Pengguna Sitti</h3>
<span style="float:right;"><a href="http://forum.sitti.co.id">Buka Forum SITTI</a></span>

<?php if (is_array ( $this->_tpl_vars['rss'] )): ?>
<table cellspacing="0" cellpadding="0" border="0" class="list zebra">
  <tbody>
  <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['rss']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
  <?php $this->assign('num', 0); ?>
  <?php if ($this->_sections['i']['iteration'] == 1): ?>
  <tr class="evenRow">
  <?php endif; ?>

    <td style="vertical-align:top;width:400px;">
    <a href="<?php echo $this->_tpl_vars['rss'][$this->_sections['i']['index']]['link']; ?>
"><strong><?php echo $this->_tpl_vars['rss'][$this->_sections['i']['index']]['title']; ?>
</strong></a><br />
    <?php echo $this->_tpl_vars['rss'][$this->_sections['i']['index']]['description']; ?>

    </td>
  
  <?php if ($this->_sections['i']['iteration'] % 2 == 0 && $this->_sections['i']['iteration'] < $this->_sections['i']['loop']): ?>
  <?php $this->assign('num', $this->_sections['i']['iteration']/2); ?>
  </tr>
  <tr class="<?php if ($this->_tpl_vars['num'] % 2 == 0): ?>evenRow<?php else: ?>oddRow<?php endif; ?>">
  <?php endif; ?>
  
  <?php endfor; endif; ?>
  </tbody>
</table>
<?php endif; ?>