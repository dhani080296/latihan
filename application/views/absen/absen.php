<h2><?php
echo $breadcrumb
?></h2>
<?php $flash_pesan= $this->session->flashdata('pesan')?>
<?php if(!empty($flash_pesan)):?>
<div class="pesan">
    <?php echo $flash_pesan; ?>
</div>
<?php endif; ?>
<?php if(!empty($pesan)):?>
<div class="pesan">
    <?php echo $pesan; ?>
</div>
<?php endif; ?>
<?php if(!empty($pagination)):?>
<div id="pagination">
    <?php echo $pagination; ?>
</div>
<?php endif; ?>
<?php if(!empty($tabel_data)):?>
<?php echo $tabel_data; ?>
<?php endif; ?>
<div id="bottom_link">
    <?php echo anchor('absen/tambah/','Tambah',array('class'=>'add'))?>
</div>




