<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Rekap_model extends CI_Model{
    public $db_tabel='absen';
    public function rekap($id_kelas,$id_semester){
        $sql="select siswa.nis,siswa.nama,
            IFNULL((select count(absen.absen)
            from absen
            where absen.absen='S'
            and absen.id_semester = '$id_semester'
                and absen.nis = siswa.nis
                and absen.nis in (select siswa.nis from siswa where siswa.id_kelas='$id_kelas'
                    order by siswa.nis ASC)
                    group by absen.nis
                    order by absen.nis ASC),0) AS sakit,
                    
                IFNULL((select count(absen.absen)
                from absen
                where absen.absen='I'
                and absen.id_semester = '$id_semester'
                    and absen.nis = siswa.nis
                    and absen.nis in (select siswa.nis from siswa
                    where siswa.id_kelas = '$id_kelas'
                        order by siswa.nis ASC)
                        group by absen.nis
                        order by absen.nis ASC), 0) AS ijin,
                        
                IFNULL((select count(absen.absen)
                from absen
                where absen.absen='A'
                and absen.id_semester = '$id_semester'
                    and absen.nis = siswa.nis
                    and absen.nis in(select siswa.nis
                    from siswa
                    where siswa.id_kelas='$id_kelas'
                        order by siswa.nis ASC)
                        group by absen.nis
                        order by absen.nis ASC),0) AS alpha,
                IFNULL((select count(absen.absen)
                from absen
                where absen.absen='T'
                and absen.id_semester='$id_semester'
                    and absen.nis = siswa.nis
                    and absen.nis in(select siswa.nis
                    from siswa
                    where siswa.id_kelas = '$id_kelas'
                        order by siswa.nis ASC)
                        group by absen.nis
                        order by absen.nis ASC), 0) AS terlambat
                from siswa
                where siswa.id_kelas='$id_kelas'
                group by siswa.nis
                order by siswa.nis ASC;";
        return $this->db->query($sql);
    }
    public function buat_tabel($rekap){
        $this->load->library('table');
        $tmpl=array('row_alt_start'=>'<tr class="zebra">');
        $this->table->set_template($tmpl);
        
        $this->table->set_heading('No','NIS','Nama','Sakit','Ijin','Alpha','Terlambat');
        $no=0;
        foreach ($rekap as $row){
            $this->table->add_row(
                    ++$no,
                    $row->nis,
                    $row->nama,
                    $row->sakit,
                    $row->ijin,
                    $row->alpha,
                    $row->terlambat);
        }
        $tabel=  $this->table->generate();
        return $tabel;
    }
}
