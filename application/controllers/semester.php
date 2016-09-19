<?php if(!defined('BASEPATH'))
     exit ('No direct script access allowed');
class Semester extends MY_Controller{
    public $data =array(
        'modul'=>'semester',
        'breadcrumb'=>'Semester',
        'pesan'=>'',
        'tabel_data'=>'',
        'main_view'=>'semester/semester',
        'form_action'=>'semester',
        'form_value'=>'',
    );
    public function __construct() {
        parent::__construct();
        $this->load->model('Semester_model','semester',TRUE);
    }
    public function index(){
        
        $semester=  $this->semester->cari_semua();
        if($semester){
            $tabel=  $this->semester->buat_tabel($semester);
            $this->data['tabel_data']=$tabel;
        }
        if($this->input->post('submit')){
            
            $this->semester->set();
            $this->session->set_flashdata('pesan','Proses ubah Semester berhasil.');
            redirect('semester');
        }
        else{
            
            $this->load->view('template',  $this->data);
        }
    }
    
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

