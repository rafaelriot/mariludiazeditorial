<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Subscriber;
use CodeIgniter\Model;

class SubscriberModel extends Model
{
    protected $table         = 'subscribers';
    protected $primaryKey    = 'id';
    protected $returnType    = Subscriber::class;
    protected $useTimestamps = true;
    protected $allowedFields = ['email', 'token', 'status', 'source', 'confirmed_at'];

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[subscribers.email,id,{id}]',
    ];
}
