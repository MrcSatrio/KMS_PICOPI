<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\AkunModel;

class Auth extends BaseController
{
    protected $akunModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
    }

    public function login()
{
    $username = $this->request->getVar('username');
    $password = $this->request->getVar('password');
    $user = $this->akunModel->where('username', $username)->first();

    if ($user) {
        if (md5($password) == $user['password']) {
            // Session untuk login
            $session = session();
            $sessionData = [
                'username' => $user['username'],
                'nama' => $user['nama'],
                'id_role' => $user['id_role']
            ];
            $session->set($sessionData);

            // Redirect berdasarkan peran pengguna (id_role)
            switch ($user['id_role']) {
                case 1:
                    return redirect()->to('admin/dashboard');
                case 2:
                    return redirect()->to('uploader');
                case 3:
                    return redirect()->to('operator/dashboard');
                default:
                    return redirect()->to('/')->withInput()->with('msg', 'Username atau Password Salah');
            }
        } else {
            return redirect()->to('/')->withInput()->with('msg', 'Username atau Password Salah');
        }
    } else {
        return redirect()->to('/')->withInput()->with('msg', 'Username atau Password Salah');
    }
}
public function logout()
    {
        // Catat logout ke LogModel
        $session = session();
        $session->destroy();
        session_write_close();
        return redirect()->to('/')->withInput()->with('berhasil', 'Berhasil Logout');
    }
    public function register()
{
    // Mengambil nilai-nilai dari form
    $username = $this->request->getVar('username');
    $password = $this->request->getVar('password');
    $email = $this->request->getVar('email');
    $name = $this->request->getVar('name');

    // Menyiapkan data user dalam bentuk array
    $datauser = [
        'nama' => $name,
        'username' => $username, // Perbaiki tanda kurung kurawal
        'password' => md5($password), // Perbaiki tanda kurung kurawal
        'email' => $email, // Perbaiki tanda kurung kurawal
        'id_role' => 2, // Perbaiki tanda kurung kurawal dan beri nilai 2
    ];

    // Memasukkan data user ke dalam database menggunakan model
    $this->akunModel->insert($datauser);

    // Menyimpan pesan sukses dalam flash data
    session()->setFlashdata('success', 'Registration successful.');

    // Mengalihkan pengguna ke halaman beranda (base url)
    return redirect()->to(base_url('/'));
}
}