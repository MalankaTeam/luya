<?php

namespace tests\src\web\models;

class AdminModelStorageEffectTest extends BaseModel
{
    public function getModel()
    {
        return new \admin\models\StorageEffect();
    }
    
    public function getParams()
    {
        return [
            'name',
            'identifier',
            'imagine_name',
            'imagine_json_params'
        ];
    }
}