<?php /* Smarty version 2.6.0, created on 2010-01-06 15:57:34
         compiled from include.tpl */ ?>
<text size="10" justification="left"><?php echo $this->_tpl_vars['name']; ?>
 <b><?php echo $this->_tpl_vars['value']; ?>
</b> <i>[line <?php if ($this->_tpl_vars['slink']):  echo $this->_tpl_vars['slink'];  else:  echo $this->_tpl_vars['linenumber'];  endif; ?>]</i>

</text>