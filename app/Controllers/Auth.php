<?php

namespace App\Controllers;

use App\Models\AdminUserModel;

class Auth extends BaseController
{
    protected AdminUserModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminUserModel();
    }

    /**
     * Tampilkan Halaman Login Admin Console
     */
    public function login()
    {
        // Jika sudah login, langsung lempar ke Dashboard
        if (session()->get('isLoggedIn') === true) {
            return redirect()->to('/');
        }

        $data = [
            'title'  => 'Login Administrator',
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('auth/login', $data);
    }

    /**
     * Proses Verifikasi Kredensial Login
     */
    public function attemptLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim($this->request->getPost('username'));
        $password = (string)$this->request->getPost('password');

        $admin = $this->adminModel->findByUsername($username);

        if (!$admin) {
            return redirect()->to('/login')->withInput()->with('error', 'Username atau kata sandi tidak valid.');
        }

        if (!$admin['is_active']) {
            return redirect()->to('/login')->withInput()->with('error', 'Akun administrator ini berstatus non-aktif. Silakan hubungi IT Satlantas.');
        }

        if (!password_verify($password, $admin['password_hash'])) {
            return redirect()->to('/login')->withInput()->with('error', 'Username atau kata sandi tidak valid.');
        }

        // Regenerate session id untuk mencegah session fixation attack
        session()->regenerate();

        // Simpan session login
        session()->set([
            'isLoggedIn'     => true,
            'admin_id'       => $admin['id'],
            'admin_username' => $admin['username'],
            'admin_name'     => $admin['full_name'],
            'admin_email'    => $admin['email'] ?? '',
            'admin_role'     => $admin['role'] ?? 'SUPER_ADMIN',
            'login_time'     => date('Y-m-d H:i:s'),
        ]);

        // Perbarui riwayat last_login_at
        $this->adminModel->update($admin['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        $redirectUrl = session()->get('redirect_url') ?? '/';
        session()->remove('redirect_url');

        return redirect()->to($redirectUrl)->with('success', 'Selamat datang, ' . esc($admin['full_name']) . '!');
    }

    /**
     * Logout dari Sistem
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout dari Admin Console.');
    }
}
