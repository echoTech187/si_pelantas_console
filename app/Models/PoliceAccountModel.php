<?php

namespace App\Models;

use CodeIgniter\Model;

class PoliceAccountModel extends Model
{
    protected $table            = 'police_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'nrp',
        'username',
        'password_hash',
        'full_name',
        'rank_title',
        'role',
        'division',
        'sector_area',
        'phone_wa',
        'fcm_token',
        'is_active',
        'last_login_at',
        'created_at',
        'updated_at',
        'photo_url',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari akun polisi dengan filter pencarian dan role/divisi
     */
    public function getFilteredList(?string $search = null, ?string $division = null)
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(full_name)', strtolower($search))
                ->orLike('nrp', $search)
                ->orLike('LOWER(rank_title)', strtolower($search))
                ->orLike('LOWER(username)', strtolower($search))
                ->groupEnd();
        }

        if (!empty($division) && $division !== 'ALL') {
            $builder->where('division', $division);
        }

        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }
}

