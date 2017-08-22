<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:30
         compiled from common/message.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'stripslashes', 'common/message.html', 4, false),)), $this); ?>
<div align="center" style="padding:100px 0 0 0; min-height:355px;">
    <div align="center" class="suksesMessage">
      <p>
          <?php echo ((is_array($_tmp=$this->_tpl_vars['msg'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : stripslashes($_tmp)); ?>

      </p>
      
      <p><a class="continue" href="<?php echo $this->_tpl_vars['url']; ?>
">lanjut</a></p>
    </div>
</div>