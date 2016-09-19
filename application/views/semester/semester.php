<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$form=array('submit'=>array('name'=>'submit',
    'id'=>'submit','value'=>'Simpan'));
?>
<h2><?php echo $breadcrumb ?></h2>
<?php $flash_pesan= $this->session->flashdata('pesan')?>
<?php if(!empty($flash_pesan)): ?>
<div class="pesan">
    <?php echo $flash_pesan; ?>
</div>
<?php endif; ?>

<?php echo form_open($form_action); ?>
<?php if(!empty($tabel_data)): ?>

    <?php echo $tabel_data; ?>

        <?php endif; ?>
<p><?php echo form_submit($form['submit']); ?></p>
<?php echo form_close(); ?>
