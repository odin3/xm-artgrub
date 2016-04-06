<?php

class MainController extends Controller
{
  private $model;

  // Main action
  public function Main() {
    $total    = Session::get('total', 0);
    $offset   = Session::get('offset', 0);
    $percents = intval( ($offset * 100) / $total);

    $this->Set('total', $total)
         ->Set('offset', $offset)
         ->Set('percents', $percents)
         ->SetView('main', 'main');
     
    return $this->Execute();
  }

  public function reset() {
    Session::set('offset', 0);
    header('Location: /');
    return $this->Execute();
  }

  public function __construct() {

    // Get model
    $this->model = Model::Get('MusicStorage');

    // Save total count of rows in session, and disallow overwrite
    Session::set( 'total', $this->model->getItemsCount(), false );
    Session::set( 'offset', 0, false);
  }


 
}
?>
