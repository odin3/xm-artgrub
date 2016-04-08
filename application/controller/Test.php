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

      //$model->createTag('1', 'Test');
//      var_dump(
//          $model->tagExists('1', 'Test')
//      );
      var_dump(
        $model->artistExists('2187a615-ca39-4c3b-9c3f-b7d4fd53f816')
      );
      //var_dump($model->ifArtistExists('1'));
    }
}