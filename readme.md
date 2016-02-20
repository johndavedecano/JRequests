# JRequests
A request abstraction for Laravel. It helps you to standardize the structure of your response and eliminate fat controllers.

## Installation
Simple clone the repo and put the folder inside your App folder then ```composer dumpautoload```.

## Usage

1. See the sample on the Handlers folder, you just simply define your rules, data which is the default values of data, then error messages for each validations.
2. Controller

```
<?php
/**
 * Created by PhpStorm.
 * User: jdecano
 * Date: 10/10/2015
 * Time: 4:53 PM
 */

namespace App\Http\Controllers;
use App\JRequests\Handlers\User\Create;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller {
    /**
     * @return \Illuminate\View\View
     */
    public function index(Create $request) {
        return $request->start(request()->all()); // You simple pass the form values 
    }
}
```

3. The $this->handle() method. This is where you actually handle the data after the validation. You can also call $this->dispatch here so you can queue some jobs.
4. The $this->reject($array, $response_code) method. Use for returning error response
5. The $this->resolve($array, $response_code) method. Use for returning success response

## Hooks

after_merge() - called after merging the default data and the data passed from the controller.
before_merge() - called before merging the default data and the data passed from the controller.

These are useful when you like to update the rules after certain data is met.

## Queuing

```

<?php 

namespace App\JRequests\Handlers\User;

use App\Services\AbstractService;
use App\User;
use App\Jobs\UserCreatedJob;

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
        $this->dispatch(new UserCreatedJob($user));
        return $this->resolve($user);
    }
}
```

## Sample Response

```
{
    "status": "failure",
    "code": 400,
    "message": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```
