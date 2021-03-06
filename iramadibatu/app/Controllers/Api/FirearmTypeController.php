<?php

namespace App\Controllers\Api;

use App\Core\ApiController;
use App\Models\FirearmTypeModel;
use CodeIgniter\HTTP\ResponseInterface;

class FirearmTypeController extends ApiController
{

    /******************** get **********************/
    public function index()
    {
        $firearmTypeModel = new FirearmTypeModel();
        return $this->respond([
            'status'    => ResponseInterface::HTTP_OK,
            'data'      => $firearmTypeModel->findAll(),
        ], ResponseInterface::HTTP_OK);
    }

    public function getOneData($id)
    {
        $firearmTypeModel = new FirearmTypeModel();
        $data = $firearmTypeModel->getOne($id);

        if (!$data)
            return $this->failNotFound();
        else
            return $this->respond([
                'status'    => ResponseInterface::HTTP_OK,
                'data'      => $data
            ], ResponseInterface::HTTP_OK);
    }

    /******************** datatable **********************/
    public function datatables()
    {
        $draw               = $this->request->getPost('draw');
        $searchQuery        = $this->request->getPost('search');
        $length             = $this->request->getPost('length');
        $start              = $this->request->getPost('start');
        $order              = $this->request->getPost('order') ?? [];

        $resData            = [];

        $firearmTypeModel   = new FirearmTypeModel();
        $data               = $firearmTypeModel->getDatatables($searchQuery['value'], $start, $length, $order);
        $recordsTotal       = $firearmTypeModel->getTotalRecords($searchQuery['value'], $order);
        $recordsFiltered    = $firearmTypeModel->getTotalFilteredRecords();

        $num = $start;
        foreach($data as $item) {
            $num++;

            $row        = [];
            $row[]      = "<div class=\"text-center\"><input class=\"multi_delete\" type=\"checkbox\" name=\"multi_delete[]\" data-item-id=\"$item->id\"></div>";
            $row[]      = "<input type=\"hidden\" value=\"$item->id\">{$num}.";
            $row[]      = "{$item->name}";
            $row[]      = "{$item->desc}";
            $row[]      = $this->buildStatusSwitch($item->id, $item->is_active);
            $row[]      = "{$item->created_at}";
            $row[]      = $this->buildActionButtons($item->id);

            $resData[] = $row;
        }

        $output = [
            'draw'  => $draw,
            'recordsTotal'  => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'  => $resData,
        ];

        return $this->respond($output, ResponseInterface::HTTP_OK);
    }

    /******************** insert **********************/
    public function create()
    {
        if (!$this->request->isAJAX())
            return $this->fail('Invalid request!');
        
        $rules      = ['name' => 'required', 'desc' => 'required', 'is_active' => 'required'];
        $messages   = [
            'name'      => ['required' => 'name is required'],
            'desc'      => ['required' => 'desc is required'],
            'is_active' => ['required' => 'is_active is required'],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->failValidationErrors($this->validator->getErrors(), 'validation failed!');
        } else {
            $data = $this->request->getVar();
            $firearmTypeModel = new FirearmTypeModel();
            $isAdded = $firearmTypeModel->createNew((array) $data);
            if (!$isAdded) {
                return $this->fail('Something went wrong!');
            } else {
                return $this->respondCreated([
                    'status'    => ResponseInterface::HTTP_CREATED,
                    'message'   => 'Data telah ditambahkan!'
                ]);
            }
        }
    }

    /******************** update **********************/
    public function updateStatus($id)
    {
        if (!$this->request->isAJAX())
            return $this->fail('Invalid request!');

        $rules      = ['is_active' => 'required'];
        $messages   = ['is_active' => ['required' => 'is_active is required']];
        if (!$this->validate($rules, $messages))
            return $this->failValidationErrors($this->validator->getErrors(), 'validation failed!');

        $firearmTypeModel = new FirearmTypeModel();
        if (!$firearmTypeModel->isExist((int) $id))
            return $this->failNotFound('Data not found!');

        $reqData = $this->request->getVar();
        $updateRes = $firearmTypeModel->updateStatus($id, $reqData->is_active);
        if (!$updateRes)
            return $this->fail('Something went wrong!');
        
        return $this->respondUpdated([
            'status'    => ResponseInterface::HTTP_OK,
            'message'   => 'Data telah diupdate!',
        ], 'Data telah diupdate!');
    }

    public function update($id)
    {
        if (!$this->request->isAJAX())
            return $this->fail('Invalid request!');
        
        $rules      = ['name' => 'required', 'desc' => 'required', 'is_active' => 'required'];
        $messages   = [
            'name'      => ['required' => 'name is required'],
            'desc'      => ['required' => 'desc is required'],
            'is_active' => ['required' => 'is_active is required'],
        ];

        if (!$this->validate($rules, $messages))
            return $this->failValidationErrors($this->validator->getErrors(), 'validation failed!');
        
        $firearmTypeModel = new FirearmTypeModel();
        $data = $firearmTypeModel->getOne((int)$id);

        if (!$data)
            return $this->failNotFound();
        
        $reqData = $this->request->getVar();
        $isUpdated = $firearmTypeModel->updateData((int)$id, (array) $reqData);
        if (!$isUpdated)
            return $this->fail('Something went wrong!');
        
        return $this->respondUpdated([
            'status'    => ResponseInterface::HTTP_OK,
            'message'   => 'Data telah diupdate!'
        ]);
    }
    
    /******************** delete **********************/
    public function delete($id)
    {
        $firearmTypeModel = new FirearmTypeModel();
        if (!$firearmTypeModel->isExist((int) $id))
            return $this->failNotFound('Not found!');

        $isDeleted = $firearmTypeModel->deleteData($id);
        if (!$isDeleted)
            return $this->fail('Failed to delete! please contact your administrator.');
        
        return $this->respondDeleted([
            'success'   => ResponseInterface::HTTP_OK,
            'message'   => 'Data telah dihapus!'
        ]);
    }

    public function deleteMultiple()
    {
        if (!$this->request->isAJAX())
            return $this->fail('Invalid request!');
        
        $rules      = ['ids' => 'required'];
        $messages   = [
            'ids' => ['required' => 'ids is required']
        ];
        
        if (!$this->validate($rules, $messages))
            return $this->failValidationErrors($this->validator->getErrors(), 'validation failed!');

        $ids = $this->request->getVar('ids');
        
        $firearmTypeModel = new FirearmTypeModel();
        $affectedRows = $firearmTypeModel->deleteMultipleData($ids);
        if ($affectedRows != count($ids))
            return $this->fail('Only some data gets deleted! please contact your administrator.');
        
        return $this->respondDeleted([
            'success'   => ResponseInterface::HTTP_OK,
            'message'   => 'Data yang dipilih telah terhapus!'
        ]);
    }

    /******************** purge **********************/
    public function purge($id)
    {
        $inventoryTypeModel = new FirearmTypeModel();
    }


}
