<?php


namespace Controllers;

use Models\Upload;
use Models\Main as MainModel;
use Core\Controller;

class Main extends Controller
{
    private $moviesListData;

    public function index($order = false){
        $this->moviesListData = (new MainModel())->getMoviesList($order);
        $this->render('index', ['data' => $this->moviesListData]);
    }

    public function asc(){
        $this->index(true);
    }

    public function addMovie(){
        if(isset($_POST["Title"], $_POST["Release_Year"], $_POST["Format"], $_POST["Stars"])){
            $data = [
                'Title' => $_POST["Title"],
                'Release Year' => $_POST["Release_Year"],
                'Format' => $_POST["Format"],
                'Stars' => $_POST["Stars"],
            ];

            $insertMovie = (new MainModel())->addNewMovie($data);
            if($insertMovie && is_int($insertMovie));
                echo json_encode(array('res'=>true, 'id'=>$insertMovie));
                exit;
        }
        echo json_encode(array('res'=>false));
    }

    public function uploadMovies(){
        if($_FILES['fileTxt']['type'] == 'text/plain' && $_FILES['fileTxt']['error'] <= 0){
            $uploadfile = FILEDIR.basename($_FILES['fileTxt']['name']);

            if(move_uploaded_file($_FILES['fileTxt']['tmp_name'], $uploadfile)){
                $fileContent = file(FILEDIR.$_FILES['fileTxt']['name']);
                $dataInsert = (new Upload())->insertMoviesDataFromTxt($fileContent);
                echo "Добавлено записей: " . $dataInsert;
            }else{
                echo "При добавлении данных произошла ошибка";
            }
        }
        echo '<br><a href="./">К списку фильмов.</a>';
    }

    public function deleteMovie(){
        if(isset($_POST['id'])){
            (new MainModel())->deleteMovie($_POST['id']);
            echo json_encode(true);
        }else
            echo json_encode(false);
    }
}