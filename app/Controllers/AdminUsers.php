<?php

namespace App\Controllers;

use App\Models\AdminUserModel;

class AdminUsers extends BaseController
{
    protected AdminUserModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminUserModel();
    }

    /**
     * Tampilkan Daftar Akun Administrator Console
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $role   = $this->request->getGet('role');

        $admins = $this->adminModel->getFilteredList($search, $role);

        $data = [
            'title'      => 'Manajemen Akun Administrator',
            'pageTitle'  => 'Manajemen Akun Admin',
            'activeMenu' => 'admin_users',
            'admins'     => $admins,
            'search'     => $search,
            'role'       => $role ?? 'ALL',
            'totalCount' => count($admins),
        ];

        return view('admin_users/index', $data);
    }

    /**
     * Endpoint DataTables Server-Side untuk Akun Administrator
     */
    public function datatable()
    {
        $currentAdminId = session()->get('admin_id');

        return \App\Libraries\FluentDatatables::of('admin_users')
            ->setPrimaryKey('id')
            ->select('id, username, full_name, email, role, is_active, last_login_at, created_at')
            ->set_column_search(['username', 'full_name', 'email', 'role'])
            ->set_orderable_columns(['id', 'full_name', 'role', 'email', 'last_login_at', 'is_active', ''])
            ->set_default_order(['created_at' => 'desc'])
            ->filter(function ($builder, $params) {
                $role = $this->request->getPost('role') ?? $this->request->getGet('role');
                if (!empty($role) && $role !== 'ALL') {
                    $builder->where('role', $role);
                }
            })
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('id, username, full_name, email, role, is_active, last_login_at, created_at');
            })
            ->addColumn('admin_html', function ($row) use ($currentAdminId) {
                $initials = strtoupper(substr($row->full_name, 0, 2));
                $nameEsc  = esc($row->full_name);
                $userEsc  = esc($row->username);
                $isSelf   = ($row->id === $currentAdminId);
                $selfBadge = $isSelf ? '<span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">Anda</span>' : '';

                return '
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-900 to-slate-800 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                        ' . $initials . '
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900 flex items-center gap-2">
                            <span>' . $nameEsc . '</span>
                            ' . $selfBadge . '
                        </div>
                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                            @' . $userEsc . '
                        </div>
                    </div>
                </div>';
            })
            ->addColumn('role_html', function ($row) {
                if ($row->role === 'SUPER_ADMIN') {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60"><i class="fa-solid fa-crown mr-1 text-[9px] text-amber-500"></i> Super Admin</span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60"><i class="fa-solid fa-user-gear mr-1 text-[9px]"></i> Operator</span>';
                }
            })
            ->addColumn('email_html', function ($row) {
                return '<span class="text-slate-600 font-mono text-[11px]">' . esc($row->email ?: '-') . '</span>';
            })
            ->addColumn('last_login_html', function ($row) {
                if (!empty($row->last_login_at)) {
                    return '<span class="font-semibold text-slate-700">' . date('d M Y, H:i', strtotime($row->last_login_at)) . ' WIB</span>';
                }
                return '<span class="text-slate-400 italic">Belum pernah</span>';
            })
            ->addColumn('status_html', function ($row) {
                if (to_bool($row->is_active)) {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif</span>';
                } else {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Non-Aktif</span>';
                }
            })
            ->addColumn('actions_html', function ($row) use ($currentAdminId) {
                $isSelf = ($row->id === $currentAdminId);
                $toggleBtn = '';
                $deleteBtn = '';

                if (!$isSelf) {
                    $isActive    = to_bool($row->is_active);
                    $toggleTitle = $isActive ? 'Nonaktifkan Admin' : 'Aktifkan Admin';
                    $toggleIcon  = $isActive ? 'fa-user-xmark' : 'fa-user-check';
                    $toggleUrl   = base_url('/admin-users/toggle/' . $row->id);
                    $toggleBtn   = '<a href="' . $toggleUrl . '" title="' . $toggleTitle . '" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition"><i class="fa-solid ' . $toggleIcon . ' text-xs"></i></a>';

                    $delUrl = base_url('/admin-users/delete/' . $row->id);
                    $delMsg = 'Hapus akun administrator ' . esc($row->full_name) . '? Tindakan ini tidak dapat dibatalkan.';
                    $deleteBtn = '<a href="' . $delUrl . '" onclick="return confirm(\'' . addslashes($delMsg) . '\')" title="Hapus Administrator" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition"><i class="fa-solid fa-trash-can text-xs"></i></a>';
                }

                $editUrl = base_url('/admin-users/edit/' . $row->id);
                $editBtn = '<a href="' . $editUrl . '" title="Edit Administrator" class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition"><i class="fa-solid fa-pen-to-square text-xs"></i></a>';

                return '<div class="inline-flex items-center gap-1.5">' . $toggleBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->make();
    }

    /**
     * Form Tambah Admin Baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Administrator Baru',
            'pageTitle'  => 'Tambah Akun Admin',
            'activeMenu' => 'admin_users',
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
            'old'        => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('admin_users/create', $data);
    }

    /**
     * Simpan Administrator Baru
     */
    public function store()
    {
        $rules = [
            'username'  => 'required|min_length[4]|max_length[50]|is_unique[admin_users.username]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'password'  => 'required|min_length[6]',
            'email'     => 'permit_empty|valid_email|max_length[100]',
            'role'      => 'required|in_list[SUPER_ADMIN,ADMIN_OPERATOR]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $passwordHash = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        $insertData = [
            'id'            => uuid_v4(),
            'username'      => strtolower(trim($this->request->getPost('username'))),
            'password_hash' => $passwordHash,
            'full_name'     => trim($this->request->getPost('full_name')),
            'email'         => trim($this->request->getPost('email')) ?: null,
            'role'          => $this->request->getPost('role'),
            'is_active'     => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        try {
            $this->adminModel->insert($insertData);
            return redirect()->to('/admin-users')->with('success', 'Akun administrator ' . esc($insertData['full_name']) . ' berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Administrator
     */
    public function edit(string $id)
    {
        $admin = $this->adminModel->find($id);

        if (!$admin) {
            return redirect()->to('/admin-users')->with('error', 'Akun administrator tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Administrator - ' . esc($admin['full_name']),
            'pageTitle'  => 'Edit Akun Admin',
            'activeMenu' => 'admin_users',
            'admin'      => $admin,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
        ];

        return view('admin_users/edit', $data);
    }

    /**
     * Update Data Administrator
     */
    public function update(string $id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to('/admin-users')->with('error', 'Akun administrator tidak ditemukan.');
        }

        $username = strtolower(trim($this->request->getPost('username')));
        $usernameRule = ($username === $admin['username'])
            ? 'required|min_length[4]|max_length[50]'
            : 'required|min_length[4]|max_length[50]|is_unique[admin_users.username]';

        $rules = [
            'username'  => $usernameRule,
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email'     => 'permit_empty|valid_email|max_length[100]',
            'role'      => 'required|in_list[SUPER_ADMIN,ADMIN_OPERATOR]',
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $updateData = [
            'username'  => $username,
            'full_name' => trim($this->request->getPost('full_name')),
            'email'     => trim($this->request->getPost('email')) ?: null,
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        // Jangan izinkan admin yang sedang login menonaktifkan dirinya sendiri
        if ($id === session()->get('admin_id') && !to_bool($updateData['is_active'])) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang aktif digunakan.');
        }

        if (!empty($newPassword)) {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        try {
            $this->adminModel->update($id, $updateData);

            // Jika mengupdate akun sendiri, update data session
            if ($id === session()->get('admin_id')) {
                session()->set([
                    'admin_username' => $updateData['username'],
                    'admin_name'     => $updateData['full_name'],
                    'admin_email'    => $updateData['email'] ?? '',
                    'admin_role'     => $updateData['role'],
                ]);
            }

            return redirect()->to('/admin-users')->with('success', 'Akun administrator ' . esc($updateData['full_name']) . ' berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui admin: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Administrator
     */
    public function delete(string $id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to('/admin-users')->with('error', 'Akun administrator tidak ditemukan.');
        }

        // Cegah self-deletion
        if ($id === session()->get('admin_id')) {
            return redirect()->to('/admin-users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Pastikan tidak menghapus SUPER_ADMIN terakhir
        if ($admin['role'] === 'SUPER_ADMIN') {
            $superCount = $this->adminModel->where('role', 'SUPER_ADMIN')->countAllResults();
            if ($superCount <= 1) {
                return redirect()->to('/admin-users')->with('error', 'Tidak dapat menghapus SUPER_ADMIN terakhir dalam sistem.');
            }
        }

        try {
            $this->adminModel->delete($id);
            return redirect()->to('/admin-users')->with('success', 'Akun administrator ' . esc($admin['full_name']) . ' berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin-users')->with('error', 'Gagal menghapus administrator: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status Aktif / Non-Aktif
     */
    public function toggleStatus(string $id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to('/admin-users')->with('error', 'Akun administrator tidak ditemukan.');
        }

        // Cegah self-deactivation
        if ($id === session()->get('admin_id')) {
            return redirect()->to('/admin-users')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang login.');
        }

        $currentStatus = to_bool($admin['is_active']);
        $newStatus     = !$currentStatus;
        $this->adminModel->update($id, ['is_active' => $newStatus ? 't' : 'f']);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/admin-users')->with('success', 'Akun administrator ' . esc($admin['full_name']) . " berhasil $statusText.");
    }
}

