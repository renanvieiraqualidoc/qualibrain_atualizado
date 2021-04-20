<?php namespace App\Models;

use CodeIgniter\Model;

class QualiuserModel extends Model{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username','password','email','permission_group'];
}
