<?php


namespace Models;


use App\autoloader;

class Main
{
    private $db;

    public function __construct()
    {
        $this->db = autoloader::$database;
    }

    public function getMoviesList($order = false){
        $query = "SELECT * FROM movies";
        if($order) $query = "SELECT * FROM movies ORDER BY title ASC";
        $query = $this->db->pdo->prepare($query);
        $query->execute();
        return $query->fetchAll();
    }

    public function addNewMovie($data = array()){
        if(is_array($data) && count($data) == 4 && isset($data['Title'], $data['Release Year'], $data['Format'], $data['Stars'])){

            $query = $this->db->pdo->prepare("
                INSERT INTO movies 
                    (title, release_year, format, stars) 
                VALUES 
                    (:title, :release_year, :format, :stars)
            ");
            $query->bindParam(':title', $data['Title']);
            $query->bindParam(':release_year', $data['Release Year']);
            $query->bindParam(':format', $data['Format']);
            $query->bindParam(':stars', $data['Stars']);
            $query->execute();
            return $this->db->pdo->lastInsertId();
        }
        return false;
    }

    public function deleteMovie($id)
    {
        $query = $this->db->pdo->prepare("
            DELETE FROM movies
            WHERE id=$id");
        $query->execute();
    }
}