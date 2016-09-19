<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Kelas extends MY_Controller {
    public $data =array(
        'modul'=>'kelas',
        'breadcrumb'=>'Kelas',
        'pesan'=>'',
        'pagination'=>'',
        'tabel_data'=>'',
        'main_view'=>'kelas/kelas',
        'form_action'=>'',
        'form_value'=>'',);
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Kelas_model','kelas',TRUE);
    }
    
    public function index(){
        $this->session->unset_userdata('id_kelas_sekarang', '');
        $this->session->unset_userdata('kelas_sekarang', '');
        
        $kelas=  $this->kelas->cari_semua();
        
        if($kelas){
            
            $tabel= $this->kelas->buat_tabel($kelas);
            $this->data['tabel_data']= $tabel;
            $this->load->view('template', $this->data);
        }
       else {
            $this->data['pesan']='tidak ada data kelas.';
            $this->load->view('template',  $this->data);
       }
    }
    public function tambah(){
        $this->data['breadcrumb']='Kelas > Tambah';
        $this->data['main_view']='kelas/kelas_form';
        $this->data['form_action']='kelas/tambah';
        
        if($this->input->post('submit')){
            
            if($this->kelas->validasi_tambah()){
                if($this->kelas->tambah()){
                    $this->session->set_flashdata('pesan','Proses tambah data berhasil.');
                    redirect('kelas');
                }
                else{
                    
                    $this->data['pesan']='Proses tamnah data gagal.';
                    $this->load->view('template',$this->data);
                }
                }
                else{
                    
                    $this->load->view('template',$this->data);
                
        }
        
                }
            else{
                $this->load->view('template',  $this->data);
            }
        }
        public function edit($id_kelas=NULL){
            
            $this->data['breadcrumb']='Kelas > Edit';
            $this->data['main_view']='kelas/kelas_form';
            $this->data['form_action']='kelas/edit/'.$id_kelas;
            
            if(!empty($id_kelas)){
                if($this->input->post('submit')){
                    if($this->kelas->validasi_edit()=== TRUE){
                        
                        $this->kelas->edit($this->session->userdata('id_kelas_sekarang'));
                        $this->session->set_flashdata('pesan','Proses update data berhasil.');
                        redirect('kelas');
                    }
                    else{
                        $this->load->view('template',  $this->data);
                    }
                }
                else{
                    $kelas= $this->kelas->cari($id_kelas);
                    foreach ($kelas as $key => $value){
                        $this->data['form_value'][$key]= $value;
                    }
                    $this->session->set_userdata('id_kelas_sekarang',$kelas->id_kelas);
                    $this->session->set_userdata('kelas_sekarang',$kelas->kelas);
                    $this->load->view('template',  $this->data);
                }
            }
            else{
                redirect('kelas');
            }
        }
        
        function is_id_kelas_exist(){
            $id_kelas_sekarang = $this->session->userdata('id_kelas_sekarang');
        $id_kelas_baru =  $this->input->post('id_kelas');
        if($id_kelas_baru === $id_kelas_sekarang){
            
            return TRUE;
        }
        else{
            $query=  $this->db->get_where('kelas',array('id_kelas'=>$id_kelas_baru));
            if($query->num_rows() > 0){
                
                $this->form_validation->set_message('is_id_kelas_exist',
                        "Kelas dengan Kode $id_kelas_baru sudah terdaftar");
                return FALSE;
                        
            }
            else{
                return TRUE;
            }
        }
            
        }
    
        function is_kelas_exist(){
            $kelas_sekarang = $this->session->userdata('kelas_sekarang');
            $kelas_baru=  $this->input->post('kelas');
            if($kelas_baru === $kelas_sekarang){
                
                return TRUE;
            }
            else{
                
                $query=  $this->db->get_where('kelas',array('kelas'=>$kelas_baru));
                if($query->num_rows() > 0){
                    
                    $this->form_validation->set_message('is_kelas_exist',
                            "Kelas dengan nama $kelas_baru sudah terdaftar");
                    return FALSE;
                }
                else{
                    
                    return TRUE;
                }
            }
        }
        public function hapus($id_kelas = NULL){
            if(!empty($id_kelas)){
                if($this->kelas->hapus($id_kelas)){
                    $this->session->set_flashdata('pesan','Proses hapus data berhasil.');
                    redirect('kelas');
                }else{
                    $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                    redirect('kelas');
                }
            }else{
                $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                redirect('kelas');
            }
        }
        
        }


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

