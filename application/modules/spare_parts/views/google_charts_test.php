<h1 style="">Dashboard <small>Spare Parts</small></h1>
<hr/>

<div> 
  <div id="dashboard-figures">  
  </div>  
</div>

<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
     var data;
     var chart;

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create our data table.
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['Mushrooms', 3],
          ['Onions', 1],
          ['Olives', 1],
          ['Zucchini', 1],
          ['Pepperoni', 2]
        ]);

        // Set chart options
        var options = {'title':'SPARE PARTS',
                       'width':400,
                       'height':300};

        // Instantiate and draw our chart, passing in some options.
        chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        google.visualization.events.addListener(chart, 'select', selectHandler);
        chart.draw(data, options);
      }

      function selectHandler() {
        var selectedItem = chart.getSelection()[0];
        var value = data.getValue(selectedItem.row, 0);
        alert('The user selected ' + value);
      }


    </script>
  </head>
  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div" style="width:400; height:300"></div>
  </body>
</html>


<div class='span6'>
  <ol class="breadcrumb"><h3>Charts</h3></ol>
  <div id="pie-chart">      
  </div>  
</div>  
<div class="clearfix;"></div>

<div class='span6'>
  <ol class="breadcrumb"><h3>Warehouse Critical Inventory</h3></ol>
  <div id="warehouse-critical-inventory"> 
  </div>  
</div>

<div class='span6'>
  <ol class="breadcrumb"><h3>Pending Reservation</h3></ol>
  <div id="pending-warehouse-reservation">  
  </div>  
</div>

<div class='span6'>
  <ol class="breadcrumb"><h3>Pending Requests for Approval</h3></ol>
  <div id="pending-approvals">  
  </div>  
</div>


