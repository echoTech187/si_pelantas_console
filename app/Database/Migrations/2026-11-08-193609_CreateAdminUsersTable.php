<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminUsersTable extends Migration
{
    public function up()
    {
        // 1. Buat Tabel admin_users dengan primary key UUID v4
        $this->db->query("
            CREATE TABLE IF NOT EXISTS admin_users (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100),
                role VARCHAR(30) NOT NULL DEFAULT 'SUPER_ADMIN',
                is_active BOOLEAN NOT NULL DEFAULT TRUE,
                last_login_at TIMESTAMPTZ,
                created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
            );
        ");

        // 2. Seed Default Admin User: admin / admin123
        $existing = $this->db->table('admin_users')->where('username', 'admin')->countAllResults();
        if ($existing === 0) {
            $this->db->table('admin_users')->insert([
                'id'            => uuid_v4(),
                'username'      => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name'     => 'Administrator Satlantas',
                'email'         => 'admin.sipelantas@mojokerto.polri.go.id',
                'role'          => 'SUPER_ADMIN',
                'is_active'     => true,
            ]);
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS admin_users CASCADE;");
    }
}
