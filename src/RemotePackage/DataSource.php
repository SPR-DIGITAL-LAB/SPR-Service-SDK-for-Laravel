<?php

namespace Spr\SprLaravelServiceSdk\RemotePackage;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DataSource extends RemotePackage
{

    protected function source()
    {
        return collect();
    }

    public function normalizeData($data)
    {
        if (!isset($data['filters'])) {
            $data['filters'] = [];
        }

        if (!isset($data['forced'])) {
            $data['forced'] = false;
        }

        if (!isset($data['data'])) {
            $data['data'] = [];
        }
        if (!isset($data['with'])) {
            $data['with'] = [];
        }
        return $data;
    }

    public function create($data)
    {
        Log::info('error');
        $data = $this->normalizeData($data);
        $p = $this->purify($data['data'], $this->preValidateData());
        if (!$p) {
            $query = $this->source();
            if ($query) {
                $insert = $query->insert($data['data']);
                return $this->actionOK(["data" => $data['data'], "insert" => $insert]);
            } else {
                return $this->actionFailed("QUERY_FAILED");
            }
        } else {
            return $this->actionFailed('DATA_VALIDATION', $p);
        }
    }

    public function delete($data)
    {
        $data = $this->normalizeData($data);
        $query = $this->filterQuery($data);
        if (count($data['filters']) || $data['forced'] == true) {
            $delete = $query->delete();
            return $this->actionOK(['data' => $data, 'delete' => $delete]);
        }
        if (!$data['forced']) {
            return $this->actionFailed('UNFILTERED_CHANGE');
        }
    }
    public function update($data)
    {
        $data = $this->normalizeData($data);
        $query = $this->filterQuery($data);
        $p = $this->purify($data['data'], $this->postValidateData());
        if ($p) {
            return $this->actionFailed('DATA_VALIDATION', $p);
        }
        if (count($data['filters']) || $data['forced'] == true) {

            $update = $query->update($data['data']);
            return $this->actionOK(["data" => $data, "update" => $update]);
        }
        if (!$data['forced']) {
            return $this->actionFailed('UNFILTERED_CHANGE');
        }
    }


    public function filterQuery($data)
    {
        $data = $this->normalizeData($data);
       

        $query = $this->source($data);

        $withRelation = $data['with'];
        foreach ($withRelation as $relation) {
            $query = $query->with($relation);
        }
        $filters = $data['filters'];
        foreach ($filters as $filter) {
            $v1 = $filter['v1'];
            $op = $filter['op'];
            $v2 = $filter['v2'];
            $relate = isset($filter['relate']) ? $filter['relate'] : 'and';
            if (isset($filter['relation'])) {
                $relation = $filter['relation'];
                if ($relate === 'and') {
                    $query = $query->whereRelation($relation, $v1, $op, $v2)->with($relation);
                }
            } else {
                if ($relate === 'and') {
                    $query = $query->where($v1, $op, $v2);
                } elseif ($relate === 'or') {
                    $query = $query->orWhere($v1, $op, $v2);
                }
            }
        }
        if (!isset($data['paginate']))
            return $query->paginate($query->count(), ['*'], 'page', 1);
        else {
            $perPage = $data['paginate']['perPage'];
            $page = $data['paginate']['page'];
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
    }

    public function query($data)
    {
        $data = $this->normalizeData($data);
        $query = $this->filterQuery($data);
        $ut = microtime(true);
        $results = $query->items();
        $et = microtime(true) - $ut;
        return $this->actionOK(
            [
                'query' => $results,
                'hasPages' => $query->hasPages(),
                'perPage' => $query->perPage(),
                'total' => $query->total(),
                'currentPage' => $query->currentPage(),
                'hasMorePages' => $query->hasMorePages(),
                'tl' => ($et * 1000)
            ]
        );
    }

    public function purify($data, $rules)
    {
        if (count($rules) == 0) return false;
        $validator = Validator::make($data, $rules);
        try {
            $validator->validate();
            return false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // return $this->actionFailed('VALIDATION_FAILED', $e);
            return $validator->failed();
        }
    }

    public function preValidateData()
    {
        $rules = [];
        return $rules;
    }


    public function postValidateData()
    {
        $rules = [];
        return $rules;
    }
}
