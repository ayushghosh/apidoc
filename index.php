<?php
include_once('ApiDoc.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Api Ref</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/codemirror.css" rel="stylesheet">

    <script src="js/jquery.js"></script>
    <script src="js/vkbeautify.js"></script>
  </head>
  <body>
    <div id="pageContainer" class="">
      <header class="">
        <div id="headerMenuToggle"><i class="glyphicon glyphicon-tasks">&nbsp;</i>Docs</div>
        <!-- <div id="headerInspectorToggle"><i class="glyphicon glyphicon-stats">&nbsp;</i>Inspector</div>
        <div id="headerReportToggle"><i class="glyphicon glyphicon-file">&nbsp;</i>Report</div> -->
        <div id="headerRightContent" class="pull-right">
          <?php
            echo $apiDoc->project_name;
          ?>
        </div>  
      </header>
      <div class="page-headers">
        <?php
              echo $apiDoc->objectHeaderHTML;
              echo $apiDoc->resourceHeaderHTML;
              echo $apiDoc->resourcePlayerHTML;
        ?>
      </div>
      <div id="midContainer" class="container">
        <?php
              echo $apiDoc->objectContainerHTML;
        ?>
      </div>
      <footer class="">
        <div>Version: <?php echo $apiDoc->version; ?> </div>
        <div class="pull-right"><?php echo $apiDoc->date; ?></div>
        
      </footer>
      
    </div>
    <div id="menuLeftContainer" class="">
      <ul class="list-unstyled">
        <li class=""><i class="glyphicon glyphicon-link">&nbsp;</i>REST API</li>
        <li class="objectName" data-objectName="debtors"><i class="glyphicon glyphicon-tower">&nbsp;</i>Debtors</li>
        <li class="objectName" data-objectName="subdebtors"><i class="glyphicon glyphicon-user">&nbsp;</i>Sub Debtors</li>
        <li class="objectName" data-objectName="offers"><i class="glyphicon glyphicon-gift">&nbsp;</i>Offers</li>
        <li class="objectName" data-objectName="payments"><i class="glyphicon glyphicon-transfer">&nbsp;</i>Payments</li>
        <hr>
        <!-- <li><i class="glyphicon glyphicon-off">&nbsp;</i>Installation</li>
        <li><i class="glyphicon glyphicon-retweet">&nbsp;</i>DB Migration</li>
        <li><i class="glyphicon glyphicon-fire">&nbsp;</i>Testing</li>
        <li><i class="glyphicon glyphicon-send">&nbsp;</i>Extending</li>
        <hr>
        <li><i class="glyphicon glyphicon-picture">&nbsp;</i>DB Schema</li> -->



      </ul>
      

    </div>
    <div id="modalContainer">
    <span id="modalClose">X</span>
      <div id="modalContent">
        
      </div> 
    </div>
    <script type="text/javascript">
    init();
    var object = "";
    var resourceSeq ="";
    var resourceTr;
    var cm;

    function init()
    {
      $('#midContainer').css("min-height",window.innerHeight-100+'px');
      initLeftMenu(); 
    }

    function initLeftMenu()
    {
      $('#headerMenuToggle').click(function(){
         $('#menuLeftContainer').animate({left: '0px'},'fast');
       });

      $('#menuLeftContainer').mouseleave(function(){
        $('#menuLeftContainer').animate({left: '-200px'},'fast');
      });
    }

    $('#modalClose').click(function(){
      $('#modalContainer').fadeOut('slow');
      $('#modalContent').empty();
      $('#pageContainer').fadeIn('slow');
      $('#Morph'+object+resourceSeq).css('visibility','hidden');
    });

    $('.morphResponse').click(function(){

      $('#pageContainer').fadeOut('slow');
      $('#modalContainer #modalContent').empty();
      $('#modalContainer').fadeIn('slow');
      jsonData=$('#Ta'+$(this).attr('data-id')).val();
      jsonData2 = JSON.parse($('#Ta'+$(this).attr('data-id')).val());
      generate(jsonData2);
    });

    $('.player-method').click(function(){
        postData = $('#fPost'+object+resourceSeq).serialize();
        urlParams = $('#fUrl'+object+resourceSeq).serialize();
        iUrl = $(this).attr('data-resourceUrl');
        url = $('#iUrl'+object+resourceSeq).val();

        var aUrl = $('<a>',{ href:url})[0];
        url = aUrl.protocol+'//'+aUrl.hostname+aUrl.pathname+'?'+urlParams;
        method = $(this).html();
        $('#iUrl'+object+resourceSeq).val(url);
        

        apiCallAjax(url,postData,method);
        $('#Morph'+object+resourceSeq).css('visibility','visible');

    });
      $('.resource-back').click(function(){
        showObject(object);
      });

      $('.player-back').click(function(){
          $('#'+object+'ResourcePlayer'+resourceSeq).hide();
          $('#midContainerResourcePlayerHeader').hide();

          $('#'+object+'ResourceHeader'+resourceSeq).show();
          $('#midContainerResourceHeader').show();
          showResource(resourceSeq);
      });


      $('.resource-method').click(function(){
          $('#'+object+'ResourceHeader'+resourceSeq).hide();
          $('#midContainerResourceHeader').hide();

          $('#'+object+'ResourcePlayer'+resourceSeq).show();
          $('#midContainerResourcePlayerHeader').fadeIn('fast');

          resourceTr.next().fadeOut('slow',function(){
          resourceTr.next().next().fadeIn('slow');          
          });          

      });
      

      $('.resourceTr').click(function(){
        resourceSeq=$(this).attr('data-resourceSeq');
        resourceTr = $(this);
        showResource(resourceSeq);
        });

      function showResource(resourceSeq)
      {
          $('.resource').hide();
          $('.resourceTr').hide();
          $('.resourceDescTr').hide();
          $('.resourcePlayTr').hide();
          $('.player').hide();
          $('.player').hide();

          $('#Ta'+object+resourceSeq).val("");
          $('#Status'+object+resourceSeq).empty().html('Status : &nbsp; &nbsp; &nbsp;&nbsp;');
          $('#Time'+object+resourceSeq).empty().html('Time : &nbsp; &nbsp;&nbsp;');


          $('#midContainerObjectHeader, #midContainerResourcePlayerHeader').fadeOut('fast',function(){
          $('#'+object+'ResourceHeader'+resourceSeq).show();
          $('#midContainerResourceHeader').fadeIn('fast');
          });
          
          resourceTr.next().fadeIn('slow');          
      }


      $('.objectName').click(function(){
        $('#menuLeftContainer').animate({left: '-200px'},'fast');
        object=$(this).attr('data-objectName');
        showObject(object);
      });

      function showObject(objectName)
      {
        $('.object').hide();
        $('.resourceDescTr').hide();
        $('.resourcePlayTr').hide();
        $('#midContainerResourceHeader, #midContainerResourcePlayerHeader').hide();

        $('#objectContainer').show();
        $('#'+objectName+'ObjectHeader').show();
        $('#'+objectName+'Object').show();

        $('#midContainerObjectHeader').fadeIn('fast',function(){
            $('.resourceTr').show('slow');
        });
      }

      function apiCallAjax(url,postData,method)
      {
        var t1= new Date().getTime();
        jQuery.ajax({
         type: method,
         url: url,
         cache:false,
         contentType: "application/x-www-form-urlencoded",
         data: postData,
         dataType: "json",
         complete: function (data, status, jqXHR) {
            var t2= new Date().getTime();
            var time=t2-t1;
            $('#Ta'+object+resourceSeq).val(vkbeautify.json(data.responseJSON, 4 ));
            $('#Status'+object+resourceSeq).empty().html('Status : '+data.status);
            $('#Time'+object+resourceSeq).empty().html('Time : '+time);         
         },
     
         error: function (jqXHR, status) {
             // error handler
     
         }     
     });
      }
    </script>

    <script type="text/javascript">
    var Html="";

function objectList(obj)
{
    for(var key in obj)
    {
        if(obj.hasOwnProperty(key))
        {
      if((typeof obj[key])=='object'){
                  parseObject(obj[key]);
              }
        }
    }
}

function parseObject(obj)
{
  if(obj['object']=='list')
    {
      Html+='<table class="table table-bordered table-condensed">'+getHeaders(obj['data'][0]);;

      objectList(obj['data']);
      Html+='</table>';
    }
    if(obj['data']){return;}
   Html+='<tr>';
    for(var key in obj)
    {
        if(obj.hasOwnProperty(key))
        {
          if(key=='object' || key=='data' || obj[key]=='list'){continue;}
              if((typeof obj[key])=='object'){

                 Html+='<td>'; 
                    parseObject(obj[key]);
                 Html+='</td>';
              }
              else
              {
                 Html+='<td>'+obj[key]+'</td>';
              }
        }
    }
    Html+='</tr>';

}

function generate(data)
{
  Html="";
    if((typeof data)=='object' ){
        parseObject(data);
    }
    if(data['object']=='list')
    {
      $('#modalContent').empty().html(Html);
    }
  else
  {
    $('#modalContent').empty().html('<table class="table table-bordered table-condensed">'+getHeaders(data)+Html+'</table>');
  }

}

function getHeaders(obj)
{
  header="<tr>";
  for(var key in obj)
    {
        if(obj.hasOwnProperty(key))
        {
          if (key=='object') {continue;};
          header+='<th>'+key+'</th>';
        }
    }
    header+="</tr>";
    return header;
}

    </script>
  </body>
</html>

