<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

$reportdata["title"] = "Clients from Greece";

$reportdata["description"] = "This report shows all the clients from Greece";

$reportdata["tableheadings"] = array("Client ID", "Client Name");

$results = Capsule::table('tblclients')
    ->select('id', 'firstname', 'lastname')
    ->where('country', 'GR') // Filter for clients from Greece
    ->get();

$reportdata["tablevalues"] = [];

foreach ($results as $result) {
    $reportdata["tablevalues"][] = [
        $result->id,
        $result->firstname . ' ' . $result->lastname,
    ];
}

$chartdata = [];
$chartdata['cols'][] = array('label' => 'Client', 'type' => 'string');
$chartdata['cols'][] = array('label' => 'Count', 'type' => 'number');

foreach ($results as $result) {
    $chartdata['rows'][] = [
        'c' => [
            ['v' => $result->firstname . ' ' . $result->lastname],
            ['v' => 1],
        ],
    ];
}

$args = array();
$args['legendpos'] = 'right';

$chart_html = '<div id="chart_div" style="width: 500px; height: 300px;"></div>';

$reportdata["headertext"] = '
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load("current", {"packages":["corechart"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
  var data = new google.visualization.DataTable();
  ' . json_encode($chartdata['cols']) . '

  data.addRows([
    ' . json_encode($chartdata['rows']) . '
  ]);

  var options = {
    legend: {
      position: "' . $args['legendpos'] . '"
    }
  };

  var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
  chart.draw(data, options);
}
</script>';

$reportdata["footertext"] = $chart->drawChart('Pie', $chartdata, $args, '300px');

echo $chart_html;
echo $reportdata["headertext"];