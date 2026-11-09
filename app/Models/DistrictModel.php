<?php

namespace App\Models;

use CodeIgniter\Model;

class DistrictModel extends Model
{
    protected $table            = 'districts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'bps_code',
        'name',
        'region_type',
        'created_at',
    ];

    protected $useTimestamps = false;
}

