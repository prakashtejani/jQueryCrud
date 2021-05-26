<html>
 <head>
  <title>Add/Edit/Delete Data</title>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" />
  <link rel="stylesheet" type="text/css" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>

<script type="text/javascript" src="//editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
 
  <style>
  body
  {
   margin:0;
   padding:0;
   background-color:#f1f1f1;
  }
  .box
  {
   width:1270px;
   padding:20px;
   background-color:#fff;
   border:1px solid #ccc;
   border-radius:5px;
   margin-top:25px;
   box-sizing:border-box;
  }
  </style>
 </head>
 <body>
 <?php 
  require_once('dbconnection.php');
  if(empty($_SESSION['email'])){header('Location: login.php');}
  $_SESSION['tableName']='';
  $connect = $dbconn;
  $fetchqry="SELECT `TABLE_NAME` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='$dbname' AND TABLE_NAME<>'users'";
  $tables=[];
  $tableOptions='<option value="">Select Table</option>';
  if ($qryresult = mysqli_query($connect,$fetchqry))
  {
      while ($row = mysqli_fetch_assoc($qryresult)) {
          $tables[]=$row['TABLE_NAME'];
          $tableOptions.='<option value="'.$row['TABLE_NAME'].'">'.$row['TABLE_NAME'].'</option>';
      }
      mysqli_free_result($qryresult);
      
  }
?>
  <div class="container box">
  <div><button onclick="logoutUser()" name="logout">Logout</button>
   <h1 align="center">Add/Edit/Delete Data</h1>
   <br />
   <div class="table-responsive">
   <br />
    <div align="right">
     <label>Select Table</label><select id="selecttable" onchange="javascript:setTableName(this.value)"><?php echo $tableOptions; ?></select>
     <?php if($_SESSION['role_name']=='admin'){?><button type="button" name="add" id="add" class="btn btn-info">Add</button><?php } ?>
    </div>
    <br />
    <div id="alert_message"></div>
    <?php if(!isset($_SESSION['tableName']) || empty($_SESSION['tableName'])){?><div id="div_sel_table">Please select table for data</div><?php } ?>
    <table id="user_data" class="table table-bordered table-striped">
     <thead>
      <tr>
       
      </tr>
     </thead>
    </table>
   </div>
  </div>
 </body>
</html>

<script type="text/javascript" language="javascript" >
var editor;

  //fetch_data();
  
  function logoutUser()
  {
    $.post("fetch.php", {"logoutUser": 1}, function() {
      window.location.href = 'login.php';
  });
  }
  setTableName($('#selecttable').val());
  function setTableName(objval)
{
  if(objval!='')
  {
    $('#div_sel_table').hide();
    if($('#user_data').find('tr').length > 1)
    {
      $('#user_data').DataTable().clear();
      $('#user_data').DataTable().destroy();
      $('#user_data thead').empty();
      $('#user_data tbody').empty();
      //$('#user_data').find('thead').remove();
      //$('#user_data').find('tbody').remove();
      
    }
    selectedVal=objval;
    $.post("fetch.php", {"tableName": selectedVal}, function() {
      
      fetch_data();
  });
}
  
}
  
  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
  function fetch_data()
  {
   /*var dataTable = $('#user_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "order" : [],
    "ajax" : {
        complete: function (data) {
                        console.log(data['responseJSON']);
                    },
     url:"fetch.php",
     type:"POST"
    }
   });*/

   $.ajax({
      url: "fetch.php",
      success: function (data) {
        columns=[];
        parseddata = JSON.parse(data);
        console.log("PAARSED");
        console.log(parseddata);
        if(parseddata){
        console.log(parseddata.columns);
        //data = JSON.parse(data);
        columnNames = parseddata.columns;
        for (var i in columnNames) {
          columns.push({data: columnNames[i], 
                    title: capitalizeFirstLetter(columnNames[i])});
        }
        console.log("FETCH");
        console.log(columns);
        
	    initDatatable(columns, parseddata.data);
    }
      }
     
   });
   //initDatatable();
  }
  function initDatatable(columns, data)
  {
    console.log("INIT");
    console.log(columns);
   /* if ($.fn.dataTable.isDataTable('#user_data')) 
      $('#user_data').DataTable().ajax.reload();
    else{*/
      
    $('#user_data').DataTable( {
		    processing: true,
		    serverSide: true,
        searching: false,
        ordering: false,
		    ajax: "fetch.php",
        destroy:true,
        retrieve: true, 
		    columns: columns
            //data: data
	    } );
    //}
      /*editor = new $.fn.dataTable.Editor( {
        table: "#user_data",
        fields: [ {
                label: "Data_cumpararii:",
                name: "Data_cumpararii",
                type: "datetime"
            }, {
                label: "Nr_loce:",
                name: "Nr_loc"
            }, {
                label: "Pret:",
                name: "Pret"
            }, {
                label: "Tip_bilet:",
                name: "Tip_bilet"
            } 
        ]
    } );
    $('#user_data').on( 'click', 'tbody td:not(:first-child)', function (e) {
        editor.inline( this );
    } );*/
  }
  
  function update_data(id, column_name, value)
  {
   $.ajax({
    url:"update.php",
    method:"POST",
    data:{id:id, column_name:column_name, value:value},
    success:function(data)
    {
     $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
     $('#user_data').DataTable().destroy();
     fetch_data();
    }
   });
   setInterval(function(){
    $('#alert_message').html('');
   }, 5000);
  }

  $(document).on('blur', '.update', function(){
   var id = $(this).data("id");
   var column_name = $(this).data("column");
   var value = $(this).text();
   update_data(id, column_name, value);
  });
  
  $('#add').click(function(){
    
    columnNames=[];
    $.ajax({
      url: "fetch.php",
      success: function (data) {
        columns=[];
        parseddata = JSON.parse(data);
        console.log(parseddata.columns);
        //data = JSON.parse(data);
        columnNames = parseddata.columns;
        for (var i in columnNames) {
          columns.push({data: columnNames[i], 
                    title: capitalizeFirstLetter(columnNames[i])});
        }}});
        //console.log('HHH');
        //console.log(columns.length);
   var html = '<tr>';
   for (var i in columns) {
   if(i<(columns.length-1))    
   html += '<td contenteditable id="data'+i+'" name="'+columns[i]['data']+'"></td>';
   }
   //html += '<td contenteditable id="data2"></td>';
   html += '<td><button type="button" name="insert" id="insert" class="btn btn-success btn-xs">Insert</button></td>';
   html += '</tr>';
   $('#user_data tbody').prepend(html);
  });
  
  $(document).on('click', '#insert', function(){
    var dataobj = [];
    let postdata = {};
    $('td[id^="data"]').each(function() {
        //console.log($(this).text());
        datakey= $(this).attr('name');
        console.log('Datakey : '+datakey)
        dataval= $(this).text();
        
            postdata[datakey]=dataval;
            //dataobj+="'"+ datakey+"':'"+ dataval+"',";
        //}

        //dataobj[datakey]=$(this).text();
        //console.log( datakey + ' :::: ' + $(this).text() );
    });
    //dataobj=dataobj.slice(0,-1);
    //dataobj="{"+dataobj+"}";
    //dataobj=jQuery.parseJSON(dataobj);
    console.log("POST DATA");
    console.log(postdata);
    
    //console.log(JSON.parse(dataobj));
  //var first_name = $('#data1').text();
   //var last_name = $('#data2').text();
   if(Object.keys(postdata).length)
   {console.log(postdata);
    $.ajax({
     url:"insert.php",
     method:"POST",
     data: postdata,
     success:function(data)
     {
      $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
      $('#user_data').DataTable().destroy();
      fetch_data();
     }
    });
    setInterval(function(){
     $('#alert_message').html('');
    }, 5000);
   }
   else
   {
    alert("Please enter data for all Fields");
   }
  });
  
  $(document).on('click', '.delete', function(){
   var id = $(this).attr("id");
   if(confirm("Are you sure you want to remove this?"))
   {
    $.ajax({
     url:"delete.php",
     method:"POST",
     data:{id:id},
     success:function(data){
      $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
      $('#user_data').DataTable().destroy();
      fetch_data();
     }
    });
    setInterval(function(){
     $('#alert_message').html('');
    }, 5000);
   }
  });
 //});
</script>