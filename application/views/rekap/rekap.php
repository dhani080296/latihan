<h2><?php echo $breadcrumb ?></h2>
<?php $flash_pesan= $this->session->flashdata('pesan') ?>
<?php if(!empty($flash_pesan)):?>
<div class="pesan">
    <?php echo $flash_pesan;?>
</div>
<?php endif; ?>
<?php if(!empty($pesan)):?>
<div class="pesan">
    <?php echo $pesan; ?>
</div>
<?php endif; ?>
<?php echo form_fieldset('Pilih Kelas'); ?>
<?php echo form_open($form_action);?>
<p>
    <?php echo form_label('Kode Kelas', 'id_kelas');?>
    <?php echo form_dropdown('id_kelas',$option_kelas,  isset($form_value['id_kelas'])? 
            $form_value['id_kelas']:''); ?>
</p>
<?php echo form_error('id_kelas','<p class="field_error">','</p>');?>
<p>
  <?php  echo form_submit(array('name'=>'submit','id'=>'submit','value'=>'OK'));?>
    
</p>
<?php echo form_close(); ?>
<?php echo form_fieldset_close();?>

<?php if(!empty($kelas)):?>
<?php echo 'Kelas: <b>'.$kelas.'</b><br/>'; ?>
<?php endif; ?>
<?php if(!empty($id_semester)):?>
<?php ($id_semester == 1)? ($id_semester = '<b>1 (Ganjil)</b>'):($id_semester ='<b>2(Genap)</b>');
echo 'semester:'.$id_semester.'<br/><br/>'; ?>
<?php endif; ?>
<?php if(!empty($tabel_data)):?>
<?php echo $tabel_data; ?>
<?php endif; ?>
<?php if(!empty($link_excel)&& !empty($link_pdf)):?>
<div id="bottom_link">
    <?php echo $link_excel.'&nbsp;'.$link_pdf;?>
</div>
<?php endif; ?>




