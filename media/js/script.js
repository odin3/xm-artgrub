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
      warnings  = 0,
      curReq    = null;

  function getPercentDone() {
    return parseFloat( (offset * 100) / total).toFixed(2);
  }

  function refreshDone() {
    $('.get-done').html(offset);
    var done = getPercentDone();
    progress.html(done+'%').css('width', done+"%");
    $('#successed').html(success);
    $('#failed').html(failed);
    $('#warnings').html(warnings);
  }

  function filtval(val) {
      val = String(val);
      return (val.length > 1) ? val : "0"+val;
  }
  function panic(msg, mark) {
    if(typeof mark == 'undefined') mark = true;
    var currentdate = new Date(); 
    var datetime =  filtval(currentdate.getDate()) + "/"
                    + filtval(currentdate.getMonth()+1)  + "/"
                    + currentdate.getFullYear() + " @ "  
                    + filtval(currentdate.getHours()) + ":"
                    + filtval(currentdate.getMinutes()) + ":"
                    + filtval(currentdate.getSeconds());
    var dmsg =   '\r\n['+datetime+']   '+msg;
    if(mark) dmsg = '<span class="panic-error">'+dmsg+"</span>";
    errorLog.append(dmsg);
    setTimeout(function(){
        errorLog.scrollTop(errorLog.prop('scrollHeight'));
    },100);
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
        curReq = $.get('/get/main/&offset='+offset).done(function(r) {
          parseResponse(r);
          ping();

        }).fail(function(e) {
          failed++;
          panic( String(e) );
          ping();
        });
      } catch(ex) {
        console.error(ex);
        panic( String(ex) );
      }
    }
  }

  function parseResponse(r) {
    var res = String(r);
    try {
      if(typeof r != 'object') r = JSON.parse(r);
      if(!r.success) {
        var mesg = r.response.message,
            code = parseInt(r.response.code);
        (code > 0) ? warnings++ : failed++;
        var red = (code == 0);
        panic(r.response.message, red);
      } else {
        success++;
      }
      offset++;
      refreshDone();


    } catch(ex) {
      console.error(ex);
      panic(ex);
    }
  }


});