<?php

namespace App\Controllers;

use App\Models\SchoolBkAccountModel;
use App\Models\SchoolModel;

class BkAccounts extends BaseController
{
    protected SchoolBkAccountModel $bkModel;
    protected SchoolModel $schoolModel;

    public function __construct()
    {
        $this->bkModel     = new SchoolBkAccountModel();
        $this->schoolModel = new SchoolModel();
    }

    /**
     * Tampilkan daftar Akun Guru BK
     */
    public function index()
    {
        $search   = $this->request->getGet('search');
        $schoolId = $this->request->getGet('school_id');

        $accounts = $this->bkModel->getBkAccountsWithSchool($search, $schoolId);
        $schools  = $this->schoolModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Master Akun Guru Bimbingan Konseling (BK)',
            'pageTitle'  => 'Manajemen Akun Guru BK',
            'activeMenu' => 'bk',
            'accounts'   => $accounts,
            'schools'    => $schools,
            'search'     => $search,
            'schoolId'   => $schoolId ?? 'ALL',
            'totalCount' => count($accounts),
        ];

        return view('bk/index', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query untuk Akun Guru BK
     */
    public function datatable()
    {
        return \App\Libraries\FluentDatatables::of('school_bk_accounts')
            ->setPrimaryKey('id')
            ->select('school_bk_accounts.id, school_bk_accounts.school_id, school_bk_accounts.nip_or_nuptk, school_bk_accounts.username, school_bk_accounts.full_name, school_bk_accounts.phone_wa, school_bk_accounts.is_primary_pic, school_bk_accounts.is_active, school_bk_accounts.created_at')
            ->set_column_search(['school_bk_accounts.full_name', 'school_bk_accounts.nip_or_nuptk', 'school_bk_accounts.username', 'school_bk_accounts.phone_wa'])
            ->set_orderable_columns(['id', 'school_bk_accounts.full_name', 'school_bk_accounts.school_id', 'school_bk_accounts.phone_wa', 'school_bk_accounts.is_primary_pic', 'school_bk_accounts.is_active', ''])
            ->set_default_order(['school_bk_accounts.full_name' => 'asc'])
            ->filter(function ($builder, $params) {
                $schoolId = $this->request->getPost('school_id') ?? $this->request->getGet('school_id');
                if (!empty($schoolId) && $schoolId !== 'ALL') {
                    $builder->where('school_bk_accounts.school_id', $schoolId);
                }
            })
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('school_bk_accounts.*, s.name AS school_name, s.npsn AS school_npsn, s.education_level')
                        ->join('schools s', 's.id = school_bk_accounts.school_id', 'left');
            })
            ->addColumn('teacher_html', function ($row) {
                $initials = strtoupper(substr($row->full_name, 0, 2));
                $nameEsc  = esc($row->full_name);
                $nipEsc   = esc($row->nip_or_nuptk ?: 'Non-NIP');
                $userEsc  = esc($row->username);
                $picBadge = $row->is_primary_pic ? '<span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">PIC Utama</span>' : '';

                return '
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                        ' . $initials . '
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900 flex items-center gap-2">
                            <span>' . $nameEsc . '</span>
                            ' . $picBadge . '
                        </div>
                        <div class="text-[11px] font-mono text-slate-400 flex items-center gap-2 mt-0.5">
                            <span>NIP/NUPTK: ' . $nipEsc . '</span>
                            <span>&bull;</span>
                            <span>@' . $userEsc . '</span>
                        </div>
                    </div>
                </div>';
            })
            ->addColumn('school_html', function ($row) {
                $schName = esc($row->school_name ?? 'Sekolah Tidak Terdaftar');
                $npsn    = esc($row->school_npsn ?? '-');
                return '
                <div class="font-bold text-slate-800">' . $schName . '</div>
                <div class="text-[11px] font-mono text-slate-400 mt-0.5">NPSN: ' . $npsn . '</div>';
            })
            ->addColumn('contact_html', function ($row) {
                $wa = esc($row->phone_wa);
                return '
                <a href="https://wa.me/' . $wa . '" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold hover:underline">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>' . $wa . '</span>
                </a>';
            })
            ->addColumn('primary_html', function ($row) {
                $toggleUrl = base_url('/bk-accounts/toggle-primary/' . $row->id);
                if (to_bool($row->is_primary_pic)) {
                    return '
                    <a href="' . $toggleUrl . '" title="Klik untuk mengubah status PIC Utama" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60 hover:bg-amber-100 transition">
                        <i class="fa-solid fa-star text-[9px] text-amber-500"></i>
                        <span>PIC Utama</span>
                    </a>';
                } else {
                    return '
                    <a href="' . $toggleUrl . '" title="Klik untuk jadikan PIC Utama" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-50 text-slate-500 border border-slate-200/60 hover:bg-slate-100 transition">
                        <span>Anggota</span>
                    </a>';
                }
            })
            ->addColumn('status_html', function ($row) {
                if (to_bool($row->is_active)) {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif</span>';
                } else {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Non-Aktif</span>';
                }
            })
            ->addColumn('actions_html', function ($row) {
                $isActive    = to_bool($row->is_active);
                $toggleTitle = $isActive ? 'Nonaktifkan Akun' : 'Aktifkan Akun';
                $toggleIcon  = $isActive ? 'fa-user-xmark' : 'fa-user-check';
                $toggleUrl   = base_url('/bk-accounts/toggle/' . $row->id);
                $editUrl     = base_url('/bk-accounts/edit/' . $row->id);
                $delUrl      = base_url('/bk-accounts/delete/' . $row->id);
                $delMsg      = 'Yakin ingin menghapus akun Guru BK ' . esc($row->full_name) . '?';

                return '
                <div class="inline-flex items-center gap-1.5">
                    <a href="' . $toggleUrl . '" title="' . $toggleTitle . '" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                        <i class="fa-solid ' . $toggleIcon . ' text-xs"></i>
                    </a>
                    <a href="' . $editUrl . '" title="Edit Guru BK" class="p-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 transition">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </a>
                    <a href="' . $delUrl . '" onclick="return confirm(\'' . addslashes($delMsg) . '\')" title="Hapus Guru BK" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </a>
                </div>';
            })
            ->make();
    }

    /**
     * Form Tambah Akun Guru BK Baru
     */
    public function create()
    {
        $schools = $this->schoolModel->where('is_active', true)->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Tambah Akun Guru BK Baru',
            'pageTitle'  => 'Registrasi Guru BK',
            'activeMenu' => 'bk',
            'schools'    => $schools,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
            'old'        => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('bk/create', $data);
    }

    /**
     * Simpan Akun Guru BK ke Database
     */
    public function store()
    {
        $rules = [
            'school_id'    => 'required',
            'nip_or_nuptk' => 'required|min_length[5]|max_length[35]|is_unique[school_bk_accounts.nip_or_nuptk]',
            'username'     => 'required|min_length[4]|max_length[60]|is_unique[school_bk_accounts.username]',
            'password'     => 'required|min_length[6]',
            'full_name'    => 'required|min_length[3]|max_length[120]',
            'phone_wa'     => 'required|min_length[9]|max_length[25]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        // Format WA agar sesuai regex ^628[0-9]{7,13}$
        $phone = preg_replace('/[^0-9]/', '', $this->request->getPost('phone_wa'));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        $passwordHash = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        $schoolId     = $this->request->getPost('school_id');
        $isPrimary    = $this->request->getPost('is_primary_pic') ? true : false;

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Jika akun baru diset sebagai PIC Utama, demote PIC lama pada sekolah tersebut
            if ($isPrimary) {
                $this->bkModel->where('school_id', $schoolId)->set(['is_primary_pic' => 'f'])->update();
            }

            $insertData = [
                'id'             => uuid_v4(),
                'school_id'      => $schoolId,
                'nip_or_nuptk'   => trim($this->request->getPost('nip_or_nuptk')),
                'username'       => strtolower(trim($this->request->getPost('username'))),
                'password_hash'  => $passwordHash,
                'full_name'      => trim($this->request->getPost('full_name')),
                'phone_wa'       => $phone,
                'is_primary_pic' => $isPrimary ? 't' : 'f',
                'is_active'      => $this->request->getPost('is_active') ? 't' : 'f',
            ];

            $this->bkModel->insert($insertData);
            $db->transCommit();

            return redirect()->to('/bk-accounts')->with('success', 'Akun Guru BK ' . esc($insertData['full_name']) . ' berhasil didaftarkan!');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan Guru BK: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Akun Guru BK
     */
    public function edit(string $id)
    {
        $account = $this->bkModel->find($id);

        if (!$account) {
            return redirect()->to('/bk-accounts')->with('error', 'Akun Guru BK tidak ditemukan.');
        }

        $schools = $this->schoolModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Edit Akun Guru BK - ' . esc($account['full_name']),
            'pageTitle'  => 'Edit Guru BK',
            'activeMenu' => 'bk',
            'account'    => $account,
            'schools'    => $schools,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
        ];

        return view('bk/edit', $data);
    }

    /**
     * Update Akun Guru BK
     */
    public function update(string $id)
    {
        $account = $this->bkModel->find($id);
        if (!$account) {
            return redirect()->to('/bk-accounts')->with('error', 'Akun Guru BK tidak ditemukan.');
        }

        $nip = trim($this->request->getPost('nip_or_nuptk'));
        $nipRule = ($nip === $account['nip_or_nuptk'])
            ? 'required|min_length[5]|max_length[35]'
            : 'required|min_length[5]|max_length[35]|is_unique[school_bk_accounts.nip_or_nuptk]';

        $rules = [
            'school_id'    => 'required',
            'nip_or_nuptk' => $nipRule,
            'full_name'    => 'required|min_length[3]|max_length[120]',
            'phone_wa'     => 'required|min_length[9]|max_length[25]',
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $phone = preg_replace('/[^0-9]/', '', $this->request->getPost('phone_wa'));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        $schoolId  = $this->request->getPost('school_id');
        $isPrimary = $this->request->getPost('is_primary_pic') ? 't' : 'f';

        $updateData = [
            'school_id'      => $schoolId,
            'nip_or_nuptk'   => $nip,
            'full_name'      => trim($this->request->getPost('full_name')),
            'phone_wa'       => $phone,
            'is_primary_pic' => $isPrimary,
            'is_active'      => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        if (!empty($newPassword)) {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if ($isPrimary === 't') {
                // Pastikan yang lain bukan primary
                $this->bkModel->where('school_id', $schoolId)->where('id !=', $id)->set(['is_primary_pic' => 'f'])->update();
            }

            $this->bkModel->update($id, $updateData);
            $db->transCommit();

            return redirect()->to('/bk-accounts')->with('success', 'Data Guru BK ' . esc($updateData['full_name']) . ' berhasil diperbarui!');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data Guru BK: ' . $e->getMessage());
        }
    }

    /**
     * Hapus / Nonaktifkan Akun Guru BK
     */
    public function delete(string $id)
    {
        $account = $this->bkModel->find($id);
        if (!$account) {
            return redirect()->to('/bk-accounts')->with('error', 'Akun Guru BK tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $feedbacksCount = $db->table('kamsel_counseling_feedbacks')->where('bk_account_id', $id)->countAllResults();

        if ($feedbacksCount > 0) {
            // Lindungi integritas log feedback penilaian Kamsel
            $this->bkModel->update($id, ['is_active' => false]);
            return redirect()->to('/bk-accounts')->with('success', 'Guru BK ' . esc($account['full_name']) . ' memiliki riwayat feedback evaluasi. Status akun dinonaktifkan.');
        }

        try {
            $this->bkModel->delete($id);
            return redirect()->to('/bk-accounts')->with('success', 'Akun Guru BK ' . esc($account['full_name']) . ' berhasil dihapus permanen.');
        } catch (\Throwable $e) {
            return redirect()->to('/bk-accounts')->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status Aktif / Non-Aktif
     */
    public function toggleStatus(string $id)
    {
        $account = $this->bkModel->find($id);
        if (!$account) {
            return redirect()->to('/bk-accounts')->with('error', 'Akun tidak ditemukan.');
        }

        $currentStatus = to_bool($account['is_active']);
        $newStatus     = !$currentStatus;
        $this->bkModel->update($id, ['is_active' => $newStatus ? 't' : 'f']);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/bk-accounts')->with('success', 'Akun Guru BK ' . esc($account['full_name']) . " berhasil $statusText.");
    }

    /**
     * Jadikan Sebagai PIC Utama Sekolah
     */
    public function togglePrimary(string $id)
    {
        $account = $this->bkModel->find($id);
        if (!$account) {
            return redirect()->to('/bk-accounts')->with('error', 'Akun tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $isCurrentlyPrimary = to_bool($account['is_primary_pic']);
            if ($isCurrentlyPrimary) {
                // Lepas PIC utama
                $this->bkModel->update($id, ['is_primary_pic' => 'f']);
                $msg = esc($account['full_name']) . ' diubah menjadi anggota tim BK.';
            } else {
                // Reset semua PIC pada sekolah tersebut ke 'f', lalu set akun ini ke 't'
                $this->bkModel->where('school_id', $account['school_id'])->set(['is_primary_pic' => 'f'])->update();
                $this->bkModel->update($id, ['is_primary_pic' => 't']);
                $msg = esc($account['full_name']) . ' berhasil ditetapkan sebagai Koordinator PIC Utama BK sekolah.';
            }

            $db->transCommit();
            return redirect()->to('/bk-accounts')->with('success', $msg);
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/bk-accounts')->with('error', 'Gagal memperbarui PIC utama: ' . $e->getMessage());
        }
    }
}

