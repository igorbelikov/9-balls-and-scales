<?php

namespace app;

/**
 * Class Response
 */
class Response
{
    /**
     * @var bool
     */
    public $success = true;

    /**
     * @var array
     */
    public $data = [];

    /**
     * Response constructor.
     * @param array $data
     * @param bool $success
     */
    public function __construct($data = [], $success = true)
    {
        $this->data = $data;
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->prepareResponse();
    }

    /**
     * @return array
     */
    public function generateData()
    {
        return [
            'success' => true,
            'data' => $this->data,
        ];
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        header('Content-Type: application/json');
        return json_encode($this->generateData());
    }
}