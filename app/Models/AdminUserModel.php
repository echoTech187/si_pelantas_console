<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table            = 'admin_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'username',
        'password_hash',
        'full_name',
        'email',
        'role',
        'is_active',
        'last_login_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari admin aktif berdasarkan username
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('LOWER(username)', strtolower(trim($username)))
                    ->first();
    }

    /**
     * Ambil daftar admin dengan pencarian dan filter peran
     */
    public function getFilteredList(?string $search = null, ?string $role = null): array
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(username)', strtolower($search))
                ->orLike('LOWER(full_name)', strtolower($search))
                ->orLike('LOWER(email)', strtolower($search))
                ->groupEnd();
        }

        if (!empty($role) && $role !== 'ALL') {
            $builder->where('role', $role);
        }

        return $builder->orderBy('created_at', 'ASC')->get()->getResultArray();
    }
}

