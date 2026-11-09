<?php

namespace App\Controllers;

use App\Models\PoliceAccountModel;
use App\Models\SchoolModel;
use App\Models\SchoolBkAccountModel;
use App\Models\ViolationMasterTypeModel;
use App\Models\InspectionLogModel;
use App\Models\TrafficViolationModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $policeModel = new PoliceAccountModel();
        $schoolModel = new SchoolModel();
        $bkModel     = new SchoolBkAccountModel();
        $vModel      = new ViolationMasterTypeModel();
        $inspModel   = new InspectionLogModel();
        $violModel   = new TrafficViolationModel();

        // Count totals
        $totalPolice  = $policeModel->countAllResults();
        $totalSchools = $schoolModel->countAllResults();
        $totalBk      = $bkModel->countAllResults();
        $totalVTypes  = $vModel->countAllResults();

        // Traffic Violations total
        $totalViolations = $db->table('traffic_violations')->countAllResults();

        // Kamsel Inspections total
        $totalInspections = $db->table('kamsel_inspection_logs')->countAllResults();

        // Average Feedback Rating from BK teachers
        $avgRatingRow = $db->table('kamsel_counseling_feedbacks')
            ->select('ROUND(AVG(rating_stars), 1) AS avg_rating, COUNT(*) AS total_reviews')
            ->get()
            ->getRowArray();
        $avgRating    = $avgRatingRow['avg_rating'] ?? 5.0;
        $totalReviews = $avgRatingRow['total_reviews'] ?? 0;

        // Breakdown by division/role
        $kamselCount   = $policeModel->where('division', 'UNIT_KAMSEL')->countAllResults();
        $gakkumCount   = $policeModel->where('division', 'GAKKUM_LANTAS')->countAllResults();
        $turjawaliCount = $policeModel->where('division', 'TURJAWALI_LANTAS')->countAllResults();

        // Breakdown by education level
        $smaCount = $schoolModel->where('education_level', 'SMA')->countAllResults();
        $smkCount = $schoolModel->where('education_level', 'SMK')->countAllResults();
        $maCount  = $schoolModel->where('education_level', 'MA')->countAllResults();

        // Recent Sidak & Violations
        $recentInspections = $inspModel->getRecentInspections(5);
        $recentViolations  = $violModel->getRecentViolations(5);

        $data = [
            'title'             => 'Overview Dashboard',
            'pageTitle'         => 'Overview',
            'activeMenu'        => 'dashboard',
            'totalPolice'       => $totalPolice,
            'totalSchools'      => $totalSchools,
            'totalBk'           => $totalBk,
            'totalVTypes'       => $totalVTypes,
            'totalViolations'   => $totalViolations,
            'totalInspections'  => $totalInspections,
            'avgRating'         => $avgRating,
            'totalReviews'      => $totalReviews,
            'kamselCount'       => $kamselCount,
            'gakkumCount'       => $gakkumCount,
            'turjawaliCount'    => $turjawaliCount,
            'smaCount'          => $smaCount,
            'smkCount'          => $smkCount,
            'maCount'           => $maCount,
            'recentInspections' => $recentInspections,
            'recentViolations'  => $recentViolations,
        ];

        return view('dashboard/index', $data);
    }
}

