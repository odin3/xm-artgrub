$(document).ready(function() {
  var $d        = document,
      $b        = $('body'),
      total     = parseInt($b.attr('data-total')),
      offset    = parseInt($b.attr('data-offset')),
      startBar  = $('#startBar'),
      taskBar   = $('#taskBar'),
      progress  = $('#progressBar'),
      startBtn  = $('#startBtn'),
      stopBtn   = $('#stopBtn'),
      stopped   = true,
      errorLog  = $('#errorsList'),
      failed    = 0,
      success   = 0,
      curReq    = null;


  console.log(total, offset);    
  function getPercentDone() {
    return parseFloat( (offset * 100) / total).toFixed(2);
  }

  function refreshDone() {
    $('.get-done').html(offset);
    var done = getPercentDone();
    progress.html(done+'%').css('width', done+"%");
    $('#successed').html(success);
    $('#failed').html(failed);
  }

  function panic(msg) {
    var currentdate = new Date(); 
    var datetime =  currentdate.getDate() + "/"
                    + (currentdate.getMonth()+1)  + "/" 
                    + currentdate.getFullYear() + " @ "  
                    + currentdate.getHours() + ":"  
                    + currentdate.getMinutes() + ":" 
                    + currentdate.getSeconds();
    errorLog.append('\r\n['+datetime+']   '+msg);                
  }  

  function showTaskBar(act) {
    stopped = !act;
    startBar[(act) ? 'addClass' : 'removeClass']('hide');
    taskBar[(act) ? 'removeClass' : 'addClass']('hide');
  }


  startBtn.on('click', function() {
    showTaskBar(true);
    ping();
  });

  stopBtn.on('click', function() {
    showTaskBar(false);
    curReq.abort();
    //document.location.reload();
  });

  $('#clearLog').on('click', function() {
    errorLog.html('');
  });

  function ping(inc) {
    if(stopped) return false;
    if(inc === true) offset = offset + 1;
    setTimeout(function() {
      pong();
    }, 1000);
  }

  function pong() {
    if(offset >= total) {
      // finish
    } else {
      try {
        console.log(offset);
        curReq = $.get('/get/main/&offset='+offset).done(function(r) {
          parseResponse(r);
          ping();

        }).fail(function(e) {
          failed++;
          panic( String(e) );
          ping();
        });
      } catch(ex) {
        panic( String(ex) );
      }
    }
  }

  function parseResponse(r) {
    var res = String(r);
    try {
      if(typeof r != 'object') r = JSON.parse(r);
      if(!r.success) {
        failed++;
        panic(r.response);
      } else {
        success++;
      }
      offset++;
      refreshDone();


    } catch(ex) {
      panic(ex);
    }
  }


});