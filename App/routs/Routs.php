<?php

namespace App\routs;

class Routs
{
    public const ROUTS = [
        '/' => 'Main@index',
        '/uploadfile' => 'Main@uploadMovies',
        '/addMovie' => 'Main@addMovie',
        '/deleteMovie' => 'Main@deleteMovie',
        '/asc' => 'Main@asc',
    ];
}