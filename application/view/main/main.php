<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <link rel="stylesheet" href="/media/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="/media/css/bootstrap-theme.min.css"> -->
  <script src="/media/js/jquery-2.2.2.min.js"></script>
  <script src='/media/js/script.js'></script>
  <style>
    .panic-error  {
      color:tomato;
    }
  </style>
</head>
<body data-total="<?=$total?>" data-offset="<?=$offset?>">
  <div class="container">
    <h2 class="page-header">AudiGrabber <small>Audio data downloader for XMusiX</small> </h2>

    <div class="alert alert-info start-up" id="startBar">
      <div class="row">
        <div class="col-sm-6">
          <b>Total: </b><?=$total?>; <b>Done: </b><span class='get-done'><?=$offset?></span>;
        </div>
        <div class="col-sm-6">
          <button class="btn btn-success pull-right" id='startBtn'>Start</button>
          <a href="/main/reset" class="btn btn-warning pull-right" style="margin-right: 5px;">Reset</a>
        </div>
      </div>
    </div>
    
    <div class="processing hide" id="taskBar">
      <div class="alert alert-warning">
        <div class="row">
          <div class="col-sm-6">
            Processing <b id="currentOffset" class="get-done"><?=$offset?></b> of <b><?=$total?></b>;
            <div>
               <b style="color: green;">Success: </b> <span id="successed">0</span>; <b style='color: orange;'>With warning: </b> <span id="warnings">0</span>
              ; <b style='color: tomato;'>Failed: </b> <span id="failed">0</span>
            </div>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-danger pull-right" id="stopBtn">Stop</button>
          </div>
        </div>
      </div>

      <div class="process">
        <div class="progress">
          <div class="progress-bar progress-bar-striped" id="progressBar" role="progressbar" aria-valuenow="<?=$offset?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$percents?>%;">
            <?=$percents?>%
          </div>
        </div>
      </div>
      
    </div>

    <div class="errors">
      <p>
        <div class="row">
          <div class="col-sm-6">
            <b>Log:</b>
          </div>
          <div class="col-sm-6 text-right" style="cursor: pointer">
            <u id="clearLog">Clear</u>
          </div>
        </div>
      </p>
      <div>
        <pre id="errorsList" style="height:320px; overflow: auto;">
        </pre>
      </div>
    </div>
  </div>
</body>
</html>