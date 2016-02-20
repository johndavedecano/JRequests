<?php
/**
 * Created by PhpStorm.
 * User: jdecano
 * Date: 6/16/15
 * Time: 10:44 PM
 */

namespace App\JRequests;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use \Validator;

abstract class AbstractRequest
{
    use DispatchesJobs;
    /**
     * @var array
     */
    protected $response = [];
    /**
     * @var array
     */
    protected $rules = [];
    /**
     * @var array
     */
    protected $messages = [];
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     * @return array
     */
    public function start(array $data) {
        $this->before_merge();
        $this->data = array_merge($this->data, $data);
        $this->after_merge();
        return $this->run_validation();
    }

    /**
     * @return array|void
     */
    public function run_validation() {

        try {
            $validator = Validator::make($this->data, $this->rules, $this->messages);

            if ($validator->fails()) {
                return $this->reject($validator->errors());
            }

            return $this->handle();

        } catch (\Exception $e) {

            if($e instanceof HttpException) {

                return $this->reject(['error' => [$e->getMessage()]], $e->getStatusCode());

            } else {

                $this->log($e);

                return $this->reject(['error' => [$e->getMessage()]], 500);
            }
        }
    }
    /**
     * @param $data
     * @return array
     */
    public function resolve($data) {
        $this->response = [];
        $this->response['status'] = 'success';
        $this->response['code'] = 200;
        $this->response['data'] = $data;
        return response($this->response, $this->response['code']);
    }

    /**
     * @param array $data
     * @param int $code
     * @return array
     */
    public function reject($data, $code = 400) {
        $this->response = [];
        $this->response['status'] = 'failure';
        $this->response['code'] = $code;
        $this->response['message'] = $data;
        return response($this->response, $this->response['code']);
    }
    /**
     * @return void
     */
    public function before_merge() {
        return;
    }

    /**
     * @return void
     */
    public function after_merge() {
        return;
    }

    /**
     * @return void|mixed
     */
    public function handle() {
        return;
    }

    /**
     * @param $e mixed
     */
    private function log($e) {
        return \Log::error($e);
    }
}