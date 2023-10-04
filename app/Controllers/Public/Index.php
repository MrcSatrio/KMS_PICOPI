<?php

namespace App\Controllers\Public;
use \App\Controllers\BaseController;
use App\Models\BerkasModel;
use App\Models\AkunModel;

class Index extends BaseController
{
    protected $berkasModel;
    protected $akunModel;

    public function __construct()
    {
        $this->berkasModel = new BerkasModel();
        $this->akunModel = new AkunModel();
    }
    public function index()
    {
        $username = session()->get('username'); 
        $akun = $this->akunModel->find($username);
        $currentDate = date('Y-m-d');
        $data =
        [
            'akun' => $akun,
            'berkas' => $this->berkasModel
                ->join('akun', 'berkas.account_id = akun.account_id')
                ->join('kategori', 'berkas.id_kategori = kategori.id_kategori')
                ->where('id_status', '2')
                ->findAll(), // Mengambil semua data user (sesuaikan sesuai kebutuhan)
                'event' => $this->berkasModel
                ->join('kategori', 'berkas.id_kategori = kategori.id_kategori')
                ->where('nama_kategori', 'EVENT')
                ->orderBy('updated_at', 'DESC') // Menyortir data berdasarkan tanggal_upload secara descending
                ->limit(5) // Mengambil hanya 5 data terbaru
                ->findAll(),
                'highlight' => $this->berkasModel
                ->join('kategori', 'berkas.id_kategori = kategori.id_kategori')
                ->join('sorotan', 'berkas.id_sorot = sorotan.id_sorot')
                ->where('status_sorot', '1')
                ->where('tgl_mulai <=', $currentDate)
                ->where('tgl_akhir >=', $currentDate)
                ->limit(2)
                ->findAll(),
        ]; 
        return view('public/index', $data);
    }

    public function publikasi()
    {
        $username = session()->get('username'); 
        $akun = $this->akunModel->find($username);
        $data =
        [
            'akun' => $akun,
            'berkas' => $this->akunModel
                ->join('status_akun', 'akun.id_status_akun = status_akun.id_status_akun')
                ->where('id_role', '2')
                ->findAll(),
        ];
        return view('public/publikasi', $data);
    }

    public function knowledge($id_dokumen)
{
    // Dekode $id_dokumen dari Base64
    $dokumen = base64_decode($id_dokumen);

    // Dapatkan username dari sesi
    $username = session()->get('username');

    // Dapatkan profil pengguna berdasarkan username
    $profile = $this->akunModel->find($username);

    // Dapatkan data dokumen berdasarkan id_dokumen
    $data = [
        'profile' => $profile,
        'document' => $this->berkasModel
            ->join('akun', 'berkas.account_id = akun.account_id')
            ->join('kategori', 'berkas.id_kategori = kategori.id_kategori')
            ->join('event', 'berkas.id_event = event.id_event')
            ->where('berkas.id_dokumen', $dokumen)
            ->first(), // Menggunakan `first()` untuk mendapatkan satu baris
        'bk' => $this->berkasModel
            ->where('id_dokumen', $dokumen)
            ->first()
    ];
    return view('public/knowledge', $data);
}

}
