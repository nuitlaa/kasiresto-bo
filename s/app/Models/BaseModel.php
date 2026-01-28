<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [];

    // --- SAFE GET ONE ---
    public function getting(array $options = [])
    {
        $builder = $this->applyOptions($options);
        return $builder->get()->getRowArray() ?? [];
    }

    // --- SAFE GET All ---
    public function gettings(array $options = [])
    {
        $builder = $this->applyOptions($options);
        return $builder->get()->getResultArray() ?? [];
    }

    // --- SAFE GET PAGINATE ---
    public function getpage(array $options = [], int $perPage = 20, string $group = 'default')
    {
        $builder = $this->applyOptions($options);
        return $this->paginate($perPage, $group, $builder);
    }

    // --- SAFE COUNT ---
    public function counting(array $options = [])
    {
        $builder = $this->applyOptions($options);
        return $builder->countAllResults();
    }

    // --- SAFE SUM ---
    public function summing(string $field, array $options = [])
    {
        $builder = $this->applyOptions($options);
        $builder->selectSum($field);

        $row = $builder->get()->getRowArray();
        return $row[$field] ?? 0;
    }

    // --- APPLY OPTIONS ---
    protected function applyOptions(array $options)
    {
        $builder = $this->builder();

        if (!empty($options['select'])) {
            $builder->select($options['select']);
        }

        if (!empty($options['where'])) {
            $builder->where($options['where']);
        }

        if (!empty($options['like'])) {
            $builder->like($options['like']);
        }

        if (!empty($options['order'])) {
            foreach ($options['order'] as $field => $direction) {
                $builder->orderBy($field, $direction);
            }
        }

        if (!empty($options['limit'])) {
            $builder->limit($options['limit']);
        }

        return $builder;
    }
}
