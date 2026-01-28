<?php

namespace App\Models;

use CodeIgniter\Model; 
use CodeIgniter\I18n\Time;

class PennModel extends Model
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    // Auto timestamps 
    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';

    protected $dateFormat = 'datetime';

    // Sanitizer
    protected function sanitize(array $data): array
    {
        $clean = [];
        foreach ($data as $k => $v) {
            $key = preg_replace('/[^a-zA-Z0-9_]/', '', $k);
            if (is_string($v)) {
                $clean[$key] = trim($v);
            } else {
                $clean[$key] = $v;
            }
        }
        return $clean;
    }

    // Safe Insert
    public function safeInsert(array $data)
    {
        $data = $this->sanitize($data);

        // pastikan gunakan waktu zona Jakarta
        $data['created'] = Time::now('Asia/Jakarta', 'en_US');
        $data['updated'] = Time::now('Asia/Jakarta', 'en_US');

        return $this->insert($data,true);
    }

    // Safe Update
    public function safeUpdate($id, array $data)
    {
        $data = $this->sanitize($data);
        $data['updated'] = Time::now('Asia/Jakarta', 'en_US');

        return $this->update($id, $data);
    }

    // Safe Delete
    public function safeDelete($id)
    {
        return $this->delete($id);
    }
} 