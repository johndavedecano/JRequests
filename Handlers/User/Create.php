<?php 

namespace App\JRequests\Handlers\User;

use App\Services\AbstractService;
use App\User;

class Create extends AbstractRequest
{

    /**
     * @var array
     */
    protected $rules = [
        'first_name' => 'required',
        'last_name'  => 'required',
        'email'      => 'required|email|unique:users,email'
    ];

    /**
     * @var array
     */
    protected $messages = [
        'first_name.required' => 'Hey first name is needed.',
        'last_name.required'  => 'Hey last name is needed.',
        'email.required'      => 'Foo!!!',
        'email.email'         => 'Bar!!!'
        'email.unique'        => 'Oink!!!'
    ];
    /**
     * Default values
     * @var array
     */
    protected $data = [
        'last_name' => 'Indong'
    ];
    /**
     * This is where you handle the request after validation
     * @return void|mixed
     */
    public function handle() {
        $user = new User($this->data);
        $user->save();
        return $this->resolve($user);
        /**
         * Or you can return
         * $response_code = 400; // 500, 403 etc.
         * $this->reject(['user_id' => ['Blbasdgfasdgasdgasdgasdgasg']], $response_code);
         */
    }
}