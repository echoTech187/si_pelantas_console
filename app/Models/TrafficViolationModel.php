<?php

namespace App\Models;

use CodeIgniter\Model;

class TrafficViolationModel extends Model
{
    protected $table            = 'traffic_violations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    public function getRecentViolations(int $limit = 10)
    {
        return $this->db->table('traffic_violations tv')
            ->select('tv.*, s.name AS school_name, st.full_name AS student_name, st.class_grade, st.class_name,
                      pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                      (SELECT GROUP_CONCAT(vmt.title SEPARATOR \', \') 
                       FROM traffic_violation_items tvi 
                       JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                       WHERE tvi.violation_id = tv.id) AS violation_items,
                      (SELECT COALESCE(SUM(vmt.max_fine_idr), 0) 
                       FROM traffic_violation_items tvi 
                       JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                       WHERE tvi.violation_id = tv.id) AS total_fine_idr', false)
            ->join('schools s', 's.id = tv.school_id', 'left')
            ->join('students st', 'st.id = tv.student_id', 'left')
            ->join('police_accounts pa', 'pa.id = tv.polantas_account_id', 'left')
            ->orderBy('tv.incident_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
