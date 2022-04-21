<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\IncomingRequest;

class Matches extends ResourceController
{
    protected $modelName = 'App\Models\MatchesModel';
    protected $format    = 'json';

    public function index()
    {
        $input = service('request')->getJSON();
        if(isset($input)) {
            $d = get_object_vars($input);
            //var_dump($this->model->find($d["id"]));
            if(is_null($this->model->find($d["id"]))) {
                return $this->model->insert($d);
            } else {
                return $this->model->save($d);
            }
        }
        //return $this->respond($this->model->findAll());
    }

    public function show($id=NULL)
    {
        
        return $this->respond($this->model->findAll());
    }

    /* public function show($id=NULL)
    {
        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', 'https://api.football-data.org/v2/competitions/', [
            'matchday' => 1,
        ]);
        //var_dump($response->getBody());
        return $this->respond($response->getBody());
    } */

    // ...
}