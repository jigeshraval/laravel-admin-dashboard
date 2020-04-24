<?php
namespace App\Objects;

use Illuminate\Database\Eloquent\Model;

class IDB extends Model
{
    protected $guarded = [''];

    public function changeStatus($status)
    {
        $this->status = (int) $status;
        $this->save();
    }

    public function updateColumn($column, $val)
    {
        $this->$column = (int) $val;
        $this->save();
    }

    public function getSql()
    {
        $builder = $this->getBuilder();
        $sql = $builder->toSql();
        foreach($builder->getBindings() as $binding)
        {
          $value = is_numeric($binding) ? $binding : "'".$binding."'";
          $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

    public function check($key, $val)
    {
        $data = $this->where($key, $val);
        if (isset($data->id) && $data->id) {
          return true;
        }

        return false;
    }

    public function findByURL($url)
    {
        $this->primaryKey = 'url';
        return $this->find($url);
    }

    public function joinMediaURL($size1 = null, $size2 = null, $webp = true)
    {
        $obj = $this;
        $obj->url = $this->retrieve($size1, $size2, $webp);
        return $obj;
    }

}
