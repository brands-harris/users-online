<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");


// ==========
// NOTE: Dont forget unit tests
// ==========

//TODO: change to /fetch-online-users, make POST, refresh login

class GetUser
{
    public function connect()
    {
        $file = fopen('../db/db.txt', 'r');
        return $file;
    }

    public function get_online_users()
    {
        $conn = $this->connect();
        if (!isset($conn) || !$conn) {
            $this->standard_response([], 'An error occurred', 500);
            return;
        }
        $data = [];
        while (!feof($conn)) {
            $split = explode(',', fgets($conn));
            if (isset($split[1]) && !$this->is_user_offline($split)) {
                $data[] = [
                    'name' => $split[0],
                    'email' => $split[1],
                    'ip_address' => $split[2]
                ];
            }
        }
        fclose($conn);
        $this->standard_response($data);
    }

    private function standard_response($obj = [], $msg = 'Action completed successfully', $code = 200)
    {
        header('X-PHP-Response-Code: ' . $code, true, $code);
        print(json_encode((object) ['success' => $code == 200 ? true : false, 'msg' => $msg, 'data' => $obj]));
    }

    private function is_user_offline($user)
    {
        if ((strtotime(date('d-m-Y H:i:s')) - strtotime($user[4])) > 3) {
            return true;
        }
        return false;
    }
}

$get_user = new GetUser();
$get_user->get_online_users();
