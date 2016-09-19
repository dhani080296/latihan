<?php 
class Absen_model extends CI_Model{
    public $db_tabel='absen';
    public $per_halaman=10;
    public $offset =0;
    
    public function cari_semua($offset,$id_semester){
        if(is_null($offset)|| empty($offset)){
            $this->offset=0;
        }else{
            $this->offset=($offset*$this->per_halaman)- $this->per_halaman;
            
        }
        return $this->db->select('absen.id_absen,absen.tanggal,
                absen.absen,siswa.nis,siswa.nama,kelas.kelas')->from('absen,siswa,kelas,semester')
                    ->where('siswa.id_kelas = kelas.id_kelas')
                    ->where('absen.nis = siswa.nis')
                    ->where('semester.id_semester = absen.id_semester')
                    ->where('absen.id_semester',$id_semester)
                ->order_by('absen.id_absen','desc')
                ->limit($this->per_halaman,  $this->offset)
                ->get()->result();
    }
    public function buat_tabel($absen){
        $this->load->library('table');
        $tmpl=array('row_alt_start'=>'<tr class="zebra">');
        $this->table->set_template($tmpl);
        $this->table->set_heading('No','Hari,tanggal','No Induk','Nama','Kelas','Absen','Aksi');
        //$i=0+$offset;
        $no=0+$this->offset;
        foreach ($absen as $row){
            $hari_array=array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
            $hr=date('w',  strtotime($row->tanggal));
            $hari=$hari_array[$hr];
            $tgl=date('d-m-y',  strtotime($row->tanggal));
            $hr_tgl="$hari,$tgl";
            $this->table->add_row(
                    ++$no,$hr_tgl,$row->nis,$row->nama,$row->kelas,$row->absen,
                    anchor('absen/edit/'.$row->id_absen,'Edit',array('class'=>'edit')).''.
                    anchor('absen/hapus/'.$row->id_absen,'Hapus',array('class'=>'delete',
                        'onclick'=>"return confirm('Anda yakin akan menghapus data ini?')")));
            
        }
        $tabel=  $this->table->generate();
        return $tabel;
    }
    
    public function paging($base_url){
        $this->load->library('pagination');
        $config=array('base_url'=>$base_url,
            'total_rows'=>  $this->hitung_semua(),
            'per_page'=>  $this->per_halaman,
            'num_links'=>  4,
            'use_page_numbers'=>TRUE,
            'first_link'=>'&#124;&lt; First',
            'last_link'=>'Last &gt;&#124;',
            'next_link'=>'Next &gt;',
            'prev_link'=>'&lt; Prev',
            );
            $this->pagination->initialize($config);
            return $this->pagination->create_links();
    }
    public function hitung_semua(){
        $id_semester=  $this->db->select('id_semester')->where('status','Y')
                ->limit(1)->get('semester')->row()->id_semester;
return $this->db->select('absen.id_absen,absen.tanggal,absen.absen,siswa.nis,siswa.nama,kelas.kelas')
        ->from('absen,siswa,kelas,semester')
        ->where('siswa.id_kelas = kelas.id_kelas')
        ->where('absen.nis = siswa.nis')
        ->where('semester.id_semester = absen.id_semester')
        ->where('absen.id_semester',$id_semester)
            ->order_by('absen.id_absen','desc')
            ->get()->num_rows();
    }
    
    private function load_form_rules_tambah(){
        $form=array(
            array(
                'field'=>'nis',
                'label'=>'NIS',
                'rules'=>'required|exact_length[4]|callback_is_siswa_exist'
            ),
            array(
                'field'=>'tanggal',
                'label'=>'Tanggal',
                'rules'=>'required|callback_is_format_tanggal|callback_is_double_entry_tambah'),
            array(
                'field'=>'absen',
                'label'=>'Absen',
                'rules'=>'required'
            ),
            );
            return $form;
    }
    public function validasi_tambah(){
        $form=  $this->load_form_rules_tambah();
        $this->form_validation->set_rules($form);
        if($this->form_validation->run()){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function tambah(){
        $smt=  $this->semester->cari_semester_aktif();
        $id_semester=$smt->id_semester;
        $absen=array(
            'nis'=>  $this->input->post('nis'),
            'id_semester'=>$id_semester,
            'tanggal'=>date('Y-m-d',  strtotime($this->input->post('tanggal'))),
            'absen'=>  $this->input->post('absen')
        );
        $this->db->insert($this->db_tabel,$absen);
        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    public function cari($id_absen){
        return $this->db->where('id_absen',$id_absen)->limit(1)->get($this->db_tabel)->row();
    }
    private function load_form_rules_edit(){
        $form=array(
            array(
                'field'=>'nis',
                'label'=>'NIS',
                'rules'=>'required|exact_length[4]|callback_is_siswa_exist'
            ),array(
                'field'=>'tanggal',
                'label'=>'Tanggal',
                'required|callback_is_format_tanggal|callback_is_double_entry_edit'
            ),
            array(
                'field'=>'absen',
                'label'=>'Absen',
                'rules'=>'required',
            ),
        );
        return $form;
    }
    public function validasi_edit(){
        $form=  $this->load_form_rules_edit();
        $this->form_validation->set_rules($form);
        if($this->form_validation->run()){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function edit($id_absen){
        $smt=  $this->semester->cari_semester_aktif();
        $id_semester= $smt->id_semester;
        $absen=array('nis'=>  $this->input->post('nis'),
            'id_semester'=>$id_semester,
            'tanggal'=>date('Y-m-d',  strtotime($this->input->post('tanggal'))),
            'absen'=>  $this->input->post('absen'),);
        $this->db->where('id_absen',$id_absen)->update($this->db_tabel,$absen);
        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    function hapus($id_absen){
        $this->db->where('id_absen',$id_absen)->delete($this->db_tabel);
        if($this->db->affected_rows()>0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
