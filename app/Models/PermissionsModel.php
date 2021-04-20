<?php namespace App\Models;

use CodeIgniter\Model;

class PermissionsModel extends Model{
    protected $table = 'permission_groups';
    protected $primaryKey = 'id';
    protected $allowedFields = ['group_name'];
}
