

<html>
<head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'CSV');
            data.addColumn('number', '<? echo Lng::get($this->rows[1][0]); ?>');
            data.addColumn('number', '<? echo Lng::get($this->rows[1][1]); ?>');
            data.addColumn('number', '<? echo Lng::get($this->rows[1][2]); ?>');
            data.addColumn('number', '<? echo Lng::get($this->rows[1][3]); ?>');
            data.addColumn('number', '<? echo Lng::get($this->rows[1][4]); ?>');
            data.addRows([
			<?for($i=2;$i<count($this->rows)-1;$i++){?>
			['<?=round($this->rows[$i][0],2)?>',<?=(float)$this->rows[$i][1]?>,<?=(float)$this->rows[$i][2]?>,<?=(float)$this->rows[$i][3]?>,<?=(float)$this->rows[$i][4]?>,<?=(float)$this->rows[$i][5]?>],
			<? }?>
			]);
			
            var options = {
                title: '<?= Lng::get('visualization-statistic-from-csv-file'); ?>'				
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
        }
    </script>
</head>
<body>
<div id="chart_div" style="height: 500px;"></div>
</body>
</html>