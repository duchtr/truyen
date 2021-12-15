 <script src="https://code.highcharts.com/highcharts.js"></script>

 <script src="https://code.highcharts.com/modules/series-label.js"></script>

 <script src="https://code.highcharts.com/modules/exporting.js"></script>



 <div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">

 	<ul>

 		<li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> Trang chủ</a></li>

 		<li class="SecondLast"><a href="javascript:void(0)">Thống kê</a></li>

 	</ul>

 </div>

 <div id="cph_Main_ContentPane" class="content">

 	<div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

 	<!-- <div id="container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div> -->

 </div>



 <script type="text/javascript">

 	Highcharts.chart('container2', {

 		chart: {

 			type: 'spline'

 		},

 		title: {

 			text: 'Thống kê tổng số lượt đặt hàng'

 		},

 		// subtitle: {

 		// 	text: 'theo tuần, theo tháng trong năm'

 		// },

 		xAxis: {

 			type: 'datetime',

        dateTimeLabelFormats: { // don't display the dummy year

        month: '%m',

        year: '%Y'

    },

    title: {

    	text: 'Ngày'

    }

},

yAxis: {

	title: {

		text: 'Số lượt đặt hàng'

	},

	min: 0

},

tooltip: {

	headerFormat: '<b>{series.name}</b><br>',

	pointFormat: 'Ngày {point.x:%d/%m}: {point.y} khách đặt hàng'

},



plotOptions: {

	spline: {

		marker: {

			enabled: true

		}

	}

},



series: [{

	<?php 

	if(count($total_order)>0) {

		$arr_obj = array();

		$arr_year = array();

		foreach ($total_order as $to) {

			$obj = new stdClass();

			$tg = explode('-', $to['date_from_time']);

			$obj->count = $to['so_luot'];

			if(count($tg)==3) {

				$obj->y = $tg[0];

				$obj->m = $tg[1];

				$obj->d = $tg[2];

				$tmp=json_decode(json_encode($obj), true);

				array_push($arr_obj, $tmp);

				array_push($arr_year, $tg[0]);

			}

		}

		$arr_year = array_unique($arr_year);

		if(count($arr_year)>0) {

			foreach ($arr_year as $v) {

				?>

				name: 'Năm <?= $v ?>',

				data: [

				<?php 

				foreach ($arr_obj as $ob) {

					if($ob['y']==$v) {

						?>

						[Date.UTC(<?= $ob['y'] ?>, <?= $ob['m']-1 ?>, <?= $ob['d'] ?>), <?= $ob['count'] ?>],

						<?php }} ?>

						],

					}, {

						<?php }}} ?>
					}
					]

				});



			</script>