<?php

namespace App\Core;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class ApiModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = '';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['setupCommonFields'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    // sys user
    protected $loggedUsername = '';

    public function __construct()
    {
        parent::__construct();
    }

    protected function setupCommonFields(array $data)
    {
        $currentTime = Time::now();
        $currentUser  = $this->loggedUsername;

        $data['data']['created_at'] = $currentTime->toDateTimeString();
        $data['data']['sys_created_user'] = $currentUser;
        $data['data']['updated_at'] = $currentTime->toDateTimeString();
        $data['data']['sys_updated_user'] = $currentUser;
        $data['data']['deleted_at'] = null;
        $data['data']['sys_deleted_user'] = null;
        $data['data']['restored_at'] = null;
        $data['data']['sys_restored_user'] = null;

        return $data;
    }

    protected function getCommondFields() : array
    {
        $currentTime = Time::now();
        return [
            'created_at'        => $currentTime->toDateTimeString(),
            'sys_created_user'  => $this->loggedUsername,
            'updated_at'        => $currentTime->toDateTimeString(),
            'sys_updated_user'  => $this->loggedUsername,
            'deleted_at'        => null,
            'sys_deleted_user'  => null,
            'restored_at'       => null,
            'sys_restored_user' => null,
        ];
    }

    public function setLoggedUsername(string $username)
    {
        $this->loggedUsername = $username;
    }

    /**
     * ********************* get *********************************
     */
    public function getOne(int $id): ?object
    {
        $this->builder()->select('*');
        $this->builder()->where('id', $id);
        $this->builder()->where('deleted_at', null);
        $this->builder()->limit(1);
        return $this->builder()->get()->getRow();
    }

    public function getDatatables(string $searchQuery, int $start, int $length, array $order) {
        $i = 0;
        foreach($this->columnSearch as $column) {
            if ($searchQuery) {
                if ($i === 0) {
                    $this->builder()->groupStart();
                    $this->builder()->like($column, $searchQuery);
                } else {
                    $this->builder()->orLike($column, $searchQuery);
                }

                if (count($this->columnSearch) - 1 === $i)
                    $this->builder()->groupEnd();
            }
            $i++;
        }

        if ($order)
            $this->builder()->orderBy($this->columnOrder[$order['0']['column']], $order['0']['dir']);

        if ($length !== -1)
            $this->builder()->limit($length, $start);

        $this->builder()->where('deleted_at', null);

        $result = $this->builder()->get();
        return $result->getResult();
    }

    public function getTotalRecords(string $searchQuery, array $order)
    {
        $i = 0;
        foreach($this->columnSearch as $column) {
            if ($searchQuery) {
                if ($i === 0) {
                    $this->builder()->groupStart();
                    $this->builder()->like($column, $searchQuery);
                } else {
                    $this->builder()->orLike($column, $searchQuery);
                }

                if (count($this->columnSearch) - 1 === $i)
                    $this->builder()->groupEnd();
            }
            $i++;
        }

        if ($order)
            $this->builder()->orderBy($this->columnOrder[$order['0']['column']], $order['0']['dir']);

        $this->builder()->where('deleted_at', null);

        return $this->builder()->countAllResults();
    }

    public function getTotalFilteredRecords()
    {
        $this->builder()->where('deleted_at', null);
        return $this->builder()->countAllResults();
    }

    /**
     * ********************* checking *********************************
     */
    public function isExist(int $id) : bool {
        $this->builder()->select('count(*) as count');
        $this->builder()->where('id', $id);
        $this->builder()->where('deleted_at is null');
        $this->builder()->limit(1);
        $count = $this->builder()->get()->getRow()->count;
        // print_r($this->db->getLastQuery());die();
        
        return $count > 0 ? true : false;
    }

    public function isExistDeleted(int $id) : bool 
    {
        $this->builder()->select('count(*) as count');
        $this->builder()->where('id', $id);
        $this->builder()->where('deleted_at is not null');
        $this->builder()->limit(1);
        $count = $this->builder()->get()->getRow()->count;
        
        return $count > 0;
    }

    /**
     * ********************* insert *********************************
     */
    public function createNew(array $data) {
        $this->insert($data);

        return $this->db->affectedRows() > 0 ? true : false;
    }

    /**
     * ********************* update *********************************
     */
    public function updateStatus(int $id, bool $isActive) : bool
    {
        $this->where('id', $id)
            ->set(['is_active' => $isActive])
            ->update();

        return $this->db->affectedRows() > 0 ? true : false;
    }

    public function updateData(int $id, array $data) : bool
    {
        $this->where('id', $id)
            ->set($data)
            ->update();

        return $this->db->affectedRows() > 0 ? true : false;
    }

    /**
     * ********************* delete *********************************
     */
    public function deleteData(int $id): bool
    {
        $this->delete($id);

        return $this->db->affectedRows() > 0;
    }

    public function deleteMultipleData(array $ids) : int {
        $this->delete($ids);

        return $this->db->affectedRows();
    }

    public function purgeData(int $id): bool
    {
        $this->delete($id, true);

        return $this->db->affectedRows() > 0;
    }

    public function purgeMultipleData(array $ids)
    {
        $this->delete($ids, true);
    }
}
