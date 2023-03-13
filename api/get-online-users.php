<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

class GetUser
{
    public function connect()
    {
        //Could also use csv with fgetcsv
        $file = fopen('../db/db.txt', 'r');
        return $file;
    }

    public function get_online_users()
    {
        $con = $this->connect();
        if (!isset($con) || !$con) {
            $this->standard_response([], 'An error occurred', 500);
            return;
        }
        $data = [];
        while (!feof($con)) {
            $split = explode(',', fgets($con));
            $data[] = [
                'name' => $split[0],
                'email' => $split[1],
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ];
        }
        $this->standard_response($data);
    }

    private function standard_response($obj = [], $msg = 'Action completed successfully', $code = 200)
    {
        header('X-PHP-Response-Code: ' . $code, true, $code);     
        print(json_encode((object) ['success' => $code == 200 ? true : false, 'msg' => $msg, 'data' => $obj]));
    }
}

$get_user = new GetUser();
$get_user->get_online_users();
