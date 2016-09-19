<?php if(!defined('BASEPATH')) exit ('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Siswa extends MY_Controller {
    public $data=array(
        'modul'=>'siswa',
        'breadcrumb'=>'Siswa',
        'pesan'=>'',
        'pagination'=>'',
        'tabel_data'=>'',
        'main_view'=>'siswa/siswa',
        'form_action'=>'',
        'form_value'=>'',
        'option_kelas'=>'',);
    public function __construct() {
        parent::__construct();
        $this->load->model('Siswa_model','siswa',TRUE);
        $this->load->model('Kelas_model','kelas',TRUE);
    }
    public function index($offset=0){
        $this->session->unset_userdata('nis_sekarang','');
        $siswa= $this->siswa->cari_semua($offset);
        
        if($siswa){
            $tabel=  $this->siswa->buat_tabel($siswa);
            $this->data['tabel_data']=$tabel;
            $this->data['pagination']=  $this->siswa->paging(site_url('siswa/halaman'));
        }else{
            $this->data['pesan']='Tidak ada data siswa.';
            
            
        }
        $this->load->view('template',  $this->data);
        
    }
    public function tambah(){
        $this->data['breadcrumb']='Siswa > Tambah';
        $this->data['main_view']='siswa/siswa_form';
        $this->data['form_action']='siswa/tambah';
        
        $kelas=  $this->kelas->cari_semua();
        if($kelas){
            foreach ($kelas as $row){
                $this->data['option_kelas'][$row->id_kelas]=$row->kelas;
            }
        }else{
            $this->data['option_kelas']['00']='-';
            $this->data['pesan']='Data kelas tidak tersedia.Silahkan isi dahulu data kelas.';
            //$this->load->view('template',  $this->data);
        }
        if($this->input->post('submit')){
            if($this->siswa->validasi_tambah()){
                if($this->siswa->tambah()){
                    $this->session->set_flashdata('pesan','Proses tambah data berhasil.');
                    redirect('siswa');
                }else{
                    $this->data['pesan']='Proses tambah data gagal.';
                    $this->load->view('template', $this->data);
                }
            }else{
                $this->load->view('template',  $this->data);
            }
        }else{
            $this->load->view('template',  $this->data);
        }
    }
    
    public function edit($nis=NULL){
            
            $this->data['breadcrumb']='Siswa > Edit';
            $this->data['main_view']='siswa/siswa_form';
            $this->data['form_action']='siswa/edit/'.$nis;
            $kelas= $this->kelas->cari_semua();
            foreach ($kelas as $row) {
                $this->data['option_kelas'][$row->id_kelas]= $row->kelas;
            }
            if(!empty($nis)){
                if($this->input->post('submit')){
                    if($this->siswa->validasi_edit()=== TRUE){
                        
                        $this->siswa->edit($this->session->userdata('nis_sekarang'));
                        $this->session->set_flashdata('pesan','Proses update data berhasil.');
                        redirect('siswa');
                    }
                    else{
                        $this->load->view('template',  $this->data);
                    }
                }
                else{
                    $siswa= $this->siswa->cari($nis);
                    foreach ($siswa as $key => $value){
                        $this->data['form_value'][$key]= $value;
                    }
                    $this->session->set_userdata('nis_sekarang',$siswa->nis);
                    $this->load->view('template',  $this->data);
                }
            }
            else{
                redirect('siswa');
            }
        }
function is_nis_exist(){
            $nis_sekarang = $this->session->userdata('nis_sekarang');
        $nis_baru =  $this->input->post('nis');
        if($nis_baru === $nis_sekarang){
            
            return TRUE;
        }
        else{
            $query=  $this->db->get_where('siswa',array('nis'=>$nis_baru));
            if($query->num_rows() > 0){
                
                $this->form_validation->set_message('is_nis_exist',
                        "Siswa dengan NIS $nis_baru sudah terdaftar");
                return FALSE;
                        
            }
            else{
                return TRUE;
            }
        }
            
        }
        function is_nama_exist(){
            $nama_sekarang = $this->session->userdata('nama_sekarang');
        $nama_baru =  $this->input->post('nama');
        if($nama_baru === $nama_sekarang){
            
            return TRUE;
        }
        else{
            $query=  $this->db->get_where('siswa',array('nama'=>$nama_baru));
            if($query->num_rows() > 0){
                
                $this->form_validation->set_message('is_nama_exist',
                        "Siswa dengan Nama $nama_baru sudah terdaftar");
                return FALSE;
                        
            }
            else{
                return TRUE;
            }
        }
            
        }
     public function hapus($nis = NULL){
            if(!empty($nis)){
                if($this->siswa->hapus($nis)){
                    $this->session->set_flashdata('pesan','Proses hapus data berhasil.');
                    redirect('siswa');
                }else{
                    $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                    redirect('siswa');
                }
            }else{
                $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                redirect('siswa');
            }
        }
}