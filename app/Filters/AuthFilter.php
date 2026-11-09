<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Filter pemeriksaan sesi sebelum mengakses rute admin
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('isLoggedIn') !== true) {
            // Simpan target URL sebelum dialihkan ke login
            session()->set('redirect_url', current_url());

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses Admin Console.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}

