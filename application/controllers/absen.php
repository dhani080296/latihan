<?php if(!defined('BASEPATH'))exit ('No direct script access allowed');
class Absen extends MY_Controller{
    public $data=array(
        'modul'=>'absen',
        'breadcrumb'=>'Absen',
        'pesan'=>'',
        'pagination'=>'',
        'tabel_data'=>'',
        'main_view'=>'absen/absen',
        'form_action'=>'',
        'form_value'=>'',
    );
    public function __construct() {
        parent::__construct();
        $this->load->model('Absen_model','absen',TRUE);
        $this->load->model('Semester_model','semester',TRUE);
        $this->load->model('Siswa_model','siswa',TRUE);
    }
    public function index($offset = 0){
        $this->session->unset_userdata('tanggal_sekarang');
        $id_semester=  $this->semester->cari_semester_aktif()->id_semester;
        $absen=  $this->absen->cari_semua($offset,$id_semester);
        if($absen){
            $tabel= $this->absen->buat_tabel($absen);
            $this->data['tabel_data']=$tabel;
            $this->data['pagination']=  $this->absen->paging(site_url('absen/halaman'));
        }
        else{
                $this->data['pesan']='tidak ada data absen.';
            }
            $this->load->view('template', $this->data);
        }
        
        public function tambah(){
            $this->data['breadcrumb']='Absen >Tambah';
            $this->data['main_view']='absen/absen_form';
            $this->data['form_action']='absen/tambah';
            
            if($this->input->post('submit')){
                if($this->absen->validasi_tambah()){
                    if($this->absen->tambah()){
                        $this->session->set_flashdata('pesan','Proses tambah data berhasil.');
                        redirect('absen');
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
        public function is_siswa_exist($nis){
            $query = $this->db->where('nis',$nis)->get('siswa');
            if($query->num_rows()>0){
                return TRUE;
            }else{
                $this->form_validation->set_message('is_siswa_exist',
                        "Siswa dengan NIS $nis tidak terdaftar");
                return FALSE;
            }
        }
        public function is_format_tanggal($str){
            if(!preg_match('/(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})/',$str)){
                $this->form_validation->set_message('is_format_tanggal','Format tanggal tidak valid. (dd-mm-yyyy)');
                return FALSE;
            }else{
                return TRUE;
            }
        }
        public function is_double_entry_tambah(){
            $nis= $this->input->post('nis');
            $tanggal=date('Y-m-d',  strtotime($this->input->post('tanggal')));
            $this->db->where('nis',$nis)
                    ->where('tanggal',$tanggal);
            $query= $this->db->get('absen')->num_rows();
            if($query>0){
                $this->form_validation->set_message('is_double_entry_tambah',
                        'Siswa ini sudah tercatat absen pada tanggal'. $this->input->post('tanggal'));
                return FALSE;
                
            }else{
                return TRUE;
            }
        }
        public function edit($id_absen= NULL){
            $this->data['breadcrumb']='Absen > Edit';
            $this->data['main_view']='absen/absen_form';
            $this->data['form_action']='absen/edit/'.$id_absen;
            if(!empty($id_absen)){
                if($this->input->post('submit')){
                    if($this->absen->validasi_edit()=== TRUE){
                        if($this->absen->edit($id_absen)){
                            $this->session->set_flashdata('pesan','Proses update data berhasil.');
                            redirect('absen');
                        }else{
                            $this->session->set_flashdata('pesan','Ups! Entah mengapa proses update data gagal.');
                            redirect('absen');
                        }
                    }else{
                        $this->load->view('template',  $this->data);
                    }
                }else{
                  $absen= $this->absen->cari($id_absen);
                foreach ($absen as $key=> $value){
                    $this->data['form_value'][$key]=$value;
                }
                $tgl=  $this->data['form_value']['tanggal'];
                $this->data['form_value']['tanggal']=
                        date('d-m-Y',  strtotime($tgl));
                $this->session->set_userdata('tanggal_sekarang',$absen->tanggal);
                $this->load->view('template',  $this->data);  
                }
            }else{
                redirect('absen');
            }
            
        }
        public function is_double_entry_edit(){
            $tanggal_sekarang= $this->session->userdata('tanggal_sekarang');
            $tanggal_baru = date('Y-m-d',  strtotime($this->input->post('tanggal')));
            $nis=  $this->input->post('nis');
            if($tanggal_baru === $tanggal_sekarang){
                return TRUE;
            }else{
                $query=  $this->db->where('nis',$nis)->where('tanggal',$tanggal_baru)
                        ->get('absen');
                if($query->num_rows()>0){
                    $this->form_validation->set_message('is_double_entry_edit','Siswa ini sudah tercatat absen pada tanggal'.
                    $this->input->post('tanggal'));
            return FALSE;
                }else{
                    return TRUE;
                }
            }
        }
        public function hapus($id_absen=NULL){
            if(!empty($id_absen)){
                if($this->absen->hapus($id_absen)){
                    $this->session->set_flashdata('pesan','Proses hapus data berhasil.');
                    redirect('absen');
                }else{
                    $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                    redirect('absen');
                }
            }else{
                $this->session->set_flashdata('pesan','Proses hapus data gagal.');
                    redirect('absen');
            }
        }
    }

