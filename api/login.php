<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

class Auth
{
    public function connect()
    {
        //Could also use csv with fgetcsv
        $file = null;
        if (!file_exists('../db/db.txt')) {
            $file = fopen('../db/db.txt', 'w');
            fclose($file);
        }
        $file = fopen('../db/db.txt', 'r');
        return $file;
    }

    public function login()
    {
        $conn = $this->connect();
        if (!isset($conn) || !$conn) {
            $this->standard_response([], 'An error occurred', 500);
            return;
        }
        $data = $_POST;
        $tar = fopen('../db/db-tar.txt', 'w');
        $found = false;
        $date = date('d-m-Y H:i:s');
        $data = json_decode(file_get_contents('php://input'), true);
        while (!feof($conn) && filesize('../db/db.txt') > 0) {
            $row = fgets($conn);
            $split = explode(',', $row);
            if (!isset($row[0])) {
                break;
            }
            $count = (int) $split[5];
            if ($split[1] == $data['email']) {
                $found = true;
                $date = date('d-m-Y H:i:s');
                $count++;
                $line = $split[0] . ',' . $split[1] . ',' . $split[2] . ',' . $split[3] . ',' . $date . ',' . (string) $count . "\n";
                fwrite($tar, $line);
            } else {
                fwrite($tar, $row);
            }
        }
        if (!$found) {
            $line = $data['name'] . ',' . $data['email'] . ',' . $_SERVER['REMOTE_ADDR'] . ',' . $_SERVER['HTTP_USER_AGENT'] . ',' . $date . ',1' . "\n";
            fwrite($tar, $line);
        }
        unlink('../db/db.txt');
        rename('../db/db-tar.txt', '../db/db.txt');
        $this->standard_response();
    }

    public function standard_response($obj = [], $msg = 'Action completed successfully', $code = 200)
    {
        header('X-PHP-Response-Code: ' . $code, true, $code);
        print(json_encode((object) ['success' => $code == 200 ? true : false, 'msg' => $msg, 'data' => $obj]));
    }
}

$auth = new Auth();
$auth->login();
