<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 08/04/16
 * Time: 10:36
 */
class TestController extends Controller
{
    public function Main() {
        $model = Model::Get('MusicData');
        
        var_dump($model->ifArtistExists('1'));
    }
}