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

    public function getMoviesCount(){
        $query = $this->db->pdo->prepare("SELECT COUNT(*) AS pages FROM movies");
        $query->execute();
        return (int)ceil($query->fetch()['pages'] / 10);
    }

    public function getMoviesList($order = false, $page = 1){
        $query = "SELECT * FROM movies LIMIT " . 10 * ($page - 1) . ", 10";
        if($order) $query = "SELECT * FROM movies ORDER BY title ASC LIMIT " . 10 * ($page - 1) . ", 10";
        $query = $this->db->pdo->prepare($query);
        $query->execute();
        return $query->fetchAll();
    }

    public function addNewMovie($data = array()){

        $check = $this->checkMovieToInsert($data);
        if($check === true){

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

            return array('res' => true, 'id' => $this->db->pdo->lastInsertId(), 'message' => 'Фильм успешно добавлен.');
        }
        return array('res' => false, 'message' => $check);
    }

    public function deleteMovie($id)
    {
        $query = $this->db->pdo->prepare("
            DELETE FROM movies
            WHERE id=$id");
        $query->execute();
    }

    private function checkMovieToInsert($data = array()){
        if(is_array($data) && count($data) == 4 && isset($data['Title'], $data['Release Year'], $data['Format'], $data['Stars'])){
            if(is_string($data["Title"]) && trim($data["Title"]) != ""){
                if(is_numeric($data["Release Year"]) && (int)$data["Release Year"] >= 1850 && (int)$data["Release Year"] <= 2021){
                    if(is_string($data["Stars"]) && trim($data["Stars"]) != ""){
                        $starsArr = array_map('trim', explode(',', $data["Stars"]));
                        if(array_unique($starsArr) == $starsArr){
                            $config = include ROOTPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'data.php';
                            if(in_array($data["Format"], $config['format'])){
                                $query = $this->db->pdo->prepare("
                                SELECT * FROM movies
                                WHERE title=:title");
                                $query->bindParam(':title', $data["Title"]);
                                $query->execute();
                                $result = $query->fetchAll();

                                $unique = true;
                                if(count($result) > 0){
                                    foreach ($result as $movie) {
                                        if($movie['release_year'] == $data['Release Year'] && $movie['stars'] == $data['Stars']){
                                            $unique = false;
                                            break;
                                        }
                                    }
                                }

                                if($unique)
                                    return true;
                                else
                                    return 'Такой фильм уже существует';
                            }else{
                                return 'Такого формата не существует';
                            }
                        }else{
                            return 'Актеры не могут дублироваться';
                        }
                    }else{
                        return 'Поле "Актеры" заполнено неверно';
                    }
                }else{
                    return 'Неверно указан год выхода. Допустимый диапазон от 1850 до 2021 года';
                }
            }else{
                return 'Поле "Название" заполнено неверно';
            }
        }else{
            return 'Заполните все данные';
        }
    }
}