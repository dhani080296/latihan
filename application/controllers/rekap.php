<?php if(!defined('BASEPATH'))exit ('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Rekap extends MY_Controller{
    public $data=array(
      'modul'=>'rekap',
      'breadcrumb'=>'Rekap',
       'pesan'=>'',
        'tabel_data'=>'',
        'main_view'=>'rekap/rekap',
        'form_action'=>'rekap',
        'form_value'=>'',
        'option_kelas'=>'',
        'link_excel'=>'',
        'link_pdf'=>'',
        'id_semester'=>'',
        'kelas'=>'',
    );
    public function __construct() {
        parent::__construct();
        $this->load->model('Rekap_model','rekap',TRUE);
        $this->load->model('Semester_model','semester',TRUE);
        $this->load->model('Siswa_model','siswa',TRUE);
        $this->load->model('Kelas_model','kelas',TRUE);
    }
    public function index(){
        $kelas=  $this->kelas->cari_semua();
        if($kelas){
            foreach ($kelas as $row){
                $this->data['option_kelas'][$row->id_kelas]=$row->kelas;
            }
            if($this->input->post('submit')){
                $this->data['form_value']['id_kelas']=  $this->input->post('id_kelas');
                $id_semester=  $this->semester->cari_semester_aktif()->id_semester;
                $id_kelas=  $this->input->post('id_kelas');
                $rekap=  $this->rekap->rekap($id_kelas,$id_semester)->result();
                if($rekap){
                    $this->data['kelas']=  $this->db->select('kelas')->where('id_kelas',$id_kelas)
                            ->get('kelas')->row()->kelas;
            $this->data['id_semester']=$id_semester;
            $this->data['tabel_data']=  $this->rekap->buat_tabel($rekap);
            $this->data['link_excel']=  anchor("rekap/download_excel/$id_kelas/$id_semester",
                    'Download Excel',array('class'=>'excel'));
            $this->data['link_pdf']=  anchor("rekap/download_pdf/$id_kelas/$id_semester",
                    'Download PDF',array('class'=>'pdf'));
            $this->load->view('template',  $this->data);
                }else{
                    $this->data['pesan']='Tidak ada data rekap.Tidak ada siswa yang terdaftar di kelas yang dipilih.';
                    $this->load->view('template',  $this->data);
                }
            }else{
                $this->load->view('template',  $this->data);
            }
        }else{
            $this->data['option_kelas']['00']='-';
            $this->data['pesan']='Data kelas tidak tersedia.Silahkan isi dahulu data kelas.';
            $this->load->view('template',  $this->data);
        }
    }
    public function download_excel($id_kelas,$id_semester){
        $this->load->helper('to_excel');
        if(!empty($id_kelas)&& !empty($id_semester)){
            $kelas=  $this->db->select('kelas')->where('id_kelas',$id_kelas)->get('kelas')->row()->kelas;
            $query=  $this->rekap->rekap($id_kelas,$id_semester);
            $nama_file='REKAP_ABSEN_KELAS_'.$kelas.'_SEMESTER_'.$id_semester;
            to_excel($query,$nama_file);
        }else{
            $this->session->set_flashdata('pesan','Proses pembuatan data rekap(Excel)gagal.Parameter tidak lengkap.');
            redirect('rekap');
        }
    }
    public function download_pdf($id_kelas, $id_semester){
        error_reporting(0);
        if(!empty($id_kelas) && !empty($id_semester)){
            $kelas=  $this->db->select('kelas')->where('id_kelas',$id_kelas)->get('kelas')->row()->kelas;
            $parameters=array('paper'=>'A4','orientation'=>'portrait',);
            $this->load->library('Pdf',$parameters);
            $this->pdf->selectFont(APPPATH.'/third_party/pdf-php/fonts/Helvetica.afm');
            $this->pdf->ezImage(base_url('asset/images/logo.png'),0,200,'none','center');
            $this->pdf->ezText("Data Rekap Absensi Kelas $kelas Semester $id_semester",20,
                    array('justification'=>'centre'));
            $this->pdf->ezSetDy(-15);
            $query=  $this->rekap->rekap($id_kelas,$id_semester);
            
            $no=0;
            $i=0;
            $data_rekap=array();
            foreach($query->result_array() as $key=>$value){
                $data_rekap[$key]=$value;
                $data_rekap[$i]['no']=++$no;
                $i++;
                
            }
            $column_header=array('no'=>'No',
                'nis'=>'Nis','nama'=>'Nama','sakit'=>'Sakit',
                'ijin'=>'Ijin','alpha'=>'Alpha','terlambat'=>'Terlambat');
            $this->pdf->ezTable($data_rekap,$column_header);
            $nama_file='REKAP_ABSEN_KELAS_' .$kelas.'_SEMESTER_'.$id_semester.'.pdf';
            $this->pdf->ezStream(array('Content-Disposition'=>$nama_file));
        }
        else{
            $this->session->set_flashdata('pesan','Proses pembuatan data rekap (PDF)gagal. Parameter tidak lengkap.');
            redirect('rekap');
        }
    }
}
