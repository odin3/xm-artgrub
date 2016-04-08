<?php
  /**
   * OpenGears Framework Kernel
   * @version 1.0
   * @package com.opengears.core
   * @author Denis Sedchenko [sedchenko.in.ua]
   */

class System
{

  public static $Version = "1.0";
  public static $Scope = array();


/**
 * Returns kernel instance
 * @return Systen Instance
 */
  public static function getInstance() {
    return new self;
  }
 /**
   * Loads a model class into controller
   *
   */
  public static function GetModel($model)
  {
    $f = MODELS."$model.php";
    if(!file_exists($f)) throw new ModelNotFoundException("Cannot load model, file '$f' was not found", 1);
    require_once($f);
    $f = new ReflectionClass($model);
    return $f->newInstance();
  }
  /**
   * Return full HTTP path to specified controller
   *
   * @return string
   */
  public static function GetControllerURL($controller,$func="")
  {
    $a = WWW.$controller;
    if(!defined("REWRITE_URI") || REWRITE_URI == false) $a = WWW."?path=".$controller;
    if($func!="") $a .="/".$func;
    return $a;
  }


  /**
   * Call an controller and return result
   * 
   */
  public static function Invoke($vClass,$vFunc="Main")
  {
    if(!file_exists(CONTROLLERS."$vClass.php")) throw new ControllerNotFoundException("Controller not found: '".CONTROLLERS."$vClass.php'", 1);
    require_once(CONTROLLERS."$vClass.php");
    if(!class_exists($vClass.'Controller')) throw new ControllerClassNotFoundException("Class not found: '".$vClass."Controller'", 1);
    if(!method_exists($vClass.'Controller', $vFunc)) throw new ActivityNotFoundException("Activity '$vFunc' not found in Controller '$vClass'", 1);
    
    $vClass .= "Controller";
    $CurrentController = new $vClass(System::$Scope);
    return $CurrentController->$vFunc();
  }

 /**
   * Call controller and write an output to page
   *
   */
  public static function Call($vClass,$vFunc="Main")
  {
    echo self::Invoke($vClass,$vFunc);
    return new self;
  }

  /**
   * Load all system classes
   */
  public static function LoadClasses()
  {
    foreach (glob(CLASSES."*.php") as $_class) {
        if(substr( basename($_class), 0, 1 ) !== "_") include($_class);
    }
    return new self;
  }

  /**
   * Load all system drivers
   */
  public static function LoadDrivers()
  {
    foreach (glob(DRIVERS."*.php") as $_class) {
        if(substr( basename($_class), 0, 1 ) !== "_") include($_class);
    }

    return new self;
  }

  /**
   * Load all system components
   */
  public static function Init()
  {
    header('X-Based-On: OpenGears/'.System::$Version);

    // Throw all notices and warnings as exceptions
    set_error_handler(function($num, $desc, $file, $line, $context) {
      throw new ErrorException($desc, 0, $num, $file, $line);
    });
    self::LoadClasses()->LoadDrivers();

    return new self;
  }

}


class ControllerNotFoundException extends Exception { }
class ViewNotFoundException extends Exception { }
class ControllerClassNotFoundException extends Exception { }
class ViewClassNotFoundException extends Exception { }
class ActivityNotFoundException extends Exception { }
class ModelNotFoundException extends Exception { }

?>
