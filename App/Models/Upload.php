<?php


namespace Models;


use App\autoloader;
use Models\Main as MainModel;

class Upload
{
    private $db;

    public function __construct()
    {
        $this->db = autoloader::$database;
    }

    public function insertMoviesDataFromTxt($data = array()){
        if(is_array($data) && count($data) > 0){
            $to_insert = array();
            $row = 0; $itr = 0;
            foreach($data as $key){

                $key = explode(': ',$key);
                if(is_array($key) && count($key) > 1 && in_array($key[0], array('Title','Release Year','Format','Stars')) && trim($key[1]) != ""){
                    if($itr++ % 4 == 0)
                        $row++;
                    $to_insert[$row][$key[0]] = $key[1];
                }
            }
            array_chunk($to_insert,4);

            $result = array(
                'insertedCount' => 0,
                'notInsertedCount' => 0,
            );
            foreach ($to_insert as $row){
                $res = (new MainModel())->addNewMovie($row)['res'];
                if($res)
                    $result['insertedCount']++;
                else
                    $result['notInsertedCount']++;
            }
            return $result;
        }
    }
}