<?php


namespace Controllers;

use Models\Upload;
use Models\Main as MainModel;
use Core\Controller;

class Main extends Controller
{
    private $moviesListData;

    public function index($order = false){
        $page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
        $model = new MainModel();
        $this->moviesListData = $model->getMoviesList($order, $page);
        $config = include ROOTPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'data.php';
        $this->render('index', ['data' => $this->moviesListData, 'format' => $config['format'], 'pages' => $model->getMoviesCount()]);
    }

    public function asc(){
        $this->index(true);
    }

    public function addMovie(){
        $response = array('res' => false, 'message' => 'Error');

        if(isset($_POST["Title"], $_POST["Release_Year"], $_POST["Format"], $_POST["Stars"])){
            if(is_string($_POST["Title"]) && trim($_POST["Title"]) != ""){
                if(is_numeric($_POST["Release_Year"]) && (int)$_POST["Release_Year"] >= 1850 && (int)$_POST["Release_Year"] <= 2021){
                    if(is_string($_POST["Stars"]) && trim($_POST["Stars"]) != ""){
                        $starsArr = array_map('trim', explode(',', $_POST["Stars"]));
                        if(array_unique($starsArr) == $starsArr){
                            $config = include ROOTPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'data.php';
                            if(in_array($_POST["Format"], $config['format'])){
                                $data = [
                                    'Title' => htmlspecialchars($_POST["Title"]),
                                    'Release Year' => htmlspecialchars($_POST["Release_Year"]),
                                    'Format' => htmlspecialchars($_POST["Format"]),
                                    'Stars' => htmlspecialchars($_POST["Stars"]),
                                ];

                                $response = (new MainModel())->addNewMovie($data);
                                $response['data'] = $data;
                            }else{
                                $response['message'] = 'Такого формата не существует';
                            }
                        }else{
                            $response['message'] = 'Актеры не могут дублироваться';
                        }
                    }else{
                        $response['message'] = 'Поле "Актеры" заполнено неверно';
                    }
                }else{
                    $response['message'] = 'Неверно указан год выхода. Допустимый диапазон от 1850 до 2021 года';
                }
            }else{
                $response['message'] = 'Поле "Название" заполнено неверно';
            }
        }else{
            $response['message'] = 'Заполните все данные';
        }

        echo json_encode($response);

    }

    public function uploadMovies(){
        if($_FILES['fileTxt']['type'] == 'text/plain'){
            if($_FILES['fileTxt']['error'] <= 0){
                $uploadfile = FILEDIR.basename($_FILES['fileTxt']['name']);

                if(move_uploaded_file($_FILES['fileTxt']['tmp_name'], $uploadfile)){
                    $fileContent = file(FILEDIR.$_FILES['fileTxt']['name']);
                    $dataInsert = (new Upload())->insertMoviesDataFromTxt($fileContent);
                    echo "Добавлено записей: " . $dataInsert['insertedCount'] . "<br>Не удалось добавить записей: " . $dataInsert['notInsertedCount'];
                }else{
                    echo "При добавлении данных произошла ошибка";
                }
            }else{
                echo "При загрузке файла произошла ошибка";
            }
        }else{
            echo "Неверный формат файла. Используйте файлы формата \".txt\"";
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