<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:42
         compiled from SITTI/reporting/beranda_tab1_new_kampanyeaktif.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'SITTI/reporting/beranda_tab1_new_kampanyeaktif.html', 2, false),array('modifier', 'number_format', 'SITTI/reporting/beranda_tab1_new_kampanyeaktif.html', 16, false),)), $this); ?>
<?php if (is_array ( $this->_tpl_vars['campaign_list'] )): ?>
<span style=" float:right">Update terakhir : <?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][0]['last_update'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%e %B %Y") : smarty_modifier_date_format($_tmp, "%e %B %Y")); ?>
</span>
 <table cellspacing="0" cellpadding="0" border="0" class="list zebra">
  <tbody><tr class="head2 oddRow">
    <td>Kampanye</td>
        <td>Impresi</td>
    <td>Klik</td>
    <td>CTR</td>
      </tr>
  <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['campaign_list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
  	<td><a href="javascript:void(0);" onclick="onCampaignDetail('<?php echo $this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['enc_campaign_id']; ?>
');return false;"><?php echo $this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['campaign_name']; ?>
</a></td>
        <td><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['imp'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</strong>
    <?php if ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['imp_change'] > 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_up.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['imp_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php elseif ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['imp_change'] < 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_down.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['imp_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php else: ?>
    <span style="padding-left:10px;"><img src="images/ppc/green.gif" /></span>
    <?php endif; ?>
    </td>
    <td><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['click'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</strong>
    <?php if ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['klik_change'] > 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_up.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['klik_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php elseif ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['klik_change'] < 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_down.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['klik_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php else: ?>
    <span style="padding-left:10px;"><img src="images/ppc/green.gif" /></span>
    <?php endif; ?>
    </td>
    <td><strong><?php echo $this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['ctr']; ?>
%</strong>
    <?php if ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['ctr_change'] > 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_up.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['ctr_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php elseif ($this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['ctr_change'] < 0): ?>
    <span style="padding-left:10px;"><img src="images/ppc/arrow_down.png" onmouseout="tooltip.hide();" onmouseover="tooltip.show('<?php echo ((is_array($_tmp=$this->_tpl_vars['campaign_list'][$this->_sections['i']['index']]['ctr_change'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
%');" /></span>
    <?php else: ?>
    <span style="padding-left:10px;"><img src="images/ppc/green.gif" /></span>
    <?php endif; ?>
    </td>
	   </tr>
   <?php endfor; endif; ?>
</tbody>
</table>
<?php else: ?>
<p>Anda tidak mempunyai kampanye aktif.</p>
<?php endif; ?>