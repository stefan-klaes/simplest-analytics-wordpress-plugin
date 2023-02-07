<?php	
/**
 * Admin Dashobord Page
 */

$tab = isset($_GET["tab"]) ? sanitize_text_field($_GET["tab"]) : '';

$today_start = date("Y-m-d");
$today_end = date("Y-m-d");

$yesterday_start = date("Y-m-d", strtotime("-1 day"));
$yesterday_end = date("Y-m-d", strtotime("-1 day"));

$last_7_start = date("Y-m-d", strtotime("-8 day"));
$last_7_end = date("Y-m-d", strtotime("-1 day"));

$last_14_start = date("Y-m-d", strtotime("-15 day"));
$last_14_end = date("Y-m-d", strtotime("-1 day"));

$last_30_start = date("Y-m-d", strtotime("-31 day"));
$last_30_end = date("Y-m-d", strtotime("-1 day"));

$last_month_start = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1));
$last_month_end = date("Y-m-d", mktime(0, 0, 0, date("m"), 0));

$default_ago = date("Y-m-d", strtotime("-30 day"));
$default_to = date("Y-m-d");


$from_date = isset($_GET["from"]) ? sanitize_text_field($_GET["from"]) : $default_ago;
$to_date = isset($_GET["to"]) ? sanitize_text_field($_GET["to"]) : $default_to;

$from_date_db = $from_date . ' 00:00:00';
$to_date_db = $to_date . ' 23:59:59';

global $wpdb;
$table_name = $wpdb->prefix . 'simplest_analytics';
if ( $from_date == "alltime" ) {
	$sql = $wpdb->prepare("SELECT * FROM $table_name");
	$rows = $wpdb->get_results( $sql );
	if ( sizeof($rows) > 0 ) {
		$from_date = strtotime($rows[0]->date_full);
		$from_date = date('Y-m-d', $from_date); 
		$to_date = strtotime(end($rows)->date_full);
		$to_date = date('Y-m-d', $to_date); 
	}
}
else {
	$from_date_db = strtotime($from_date_db);
	$from_date_db =  date("Y-m-d H:i:s", $from_date_db);
	$to_date_db = strtotime($to_date_db);
	$to_date_db =  date("Y-m-d H:i:s", $to_date_db);
	$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE date_full BETWEEN %s AND %s ORDER by id DESC", $from_date_db,$to_date_db);
	$rows = $wpdb->get_results( $sql );
}

	
?>
<form id="daterange_form" style="display:none">
	<input type="text" name="page" value="simplest-analytics"/>
	<input type="text" name="from" id="from_date"/>
	<input type="text" name="to" id="to_date"/>
</form>
<script>
function simplest_analytics_apply_custom_date() {
	var start = document.getElementById("from").value;
	var end = document.getElementById("to").value;
	if ( start == "" || end == "" ) {
		alert("<?php echo esc_html__( 'Start date or end date is empty.', 'simplest-analytics' ) ?>")
	}
	else if ( start > end ) {
		alert("<?php echo esc_html__( 'End date has to be ealier than start date.', 'simplest-analytics' ) ?>")
	}
	else {
		document.getElementById("from_date").value = start;
		document.getElementById("to_date").value = end;
		document.getElementById("daterange_form").submit();
		simplest_analytics_hide_ele('daterange_popup');
	}
	
}
function simplest_analytics_daterange_sel(type) {
	if ( type == "today" ) {
		var start = "<?php echo esc_html( $today_start ) ?>";
		var end = "<?php echo esc_html(  $today_end ) ?>";
	}
	else if ( type == "yesterday" ) {
		var start = "<?php echo esc_html( $yesterday_start ) ?>";
		var end = "<?php echo esc_html( $yesterday_end ) ?>";
	}
	else if ( type == "last7" ) {
		var start = "<?php echo esc_html( $last_7_start ) ?>";
		var end = "<?php echo esc_html( $last_7_end ) ?>";
	}
	else if ( type == "last14" ) {
		var start = "<?php echo esc_html( $last_14_start ) ?>";
		var end = "<?php echo esc_html( $last_14_end ) ?>";
	}
	else if ( type == "last30" ) {
		var start = "<?php echo esc_html( $last_30_start ) ?>";
		var end = "<?php echo esc_html( $last_30_end ) ?>";
	}
	else if ( type == "lastmonth" ) {
		var start = "<?php echo esc_html( $last_month_start ) ?>";
		var end = "<?php echo esc_html( $last_month_end ) ?>";
	}
	else if ( type == "alltime" ) {
		var start = "alltime";
		var end = "alltime";
	}


	document.getElementById("from_date").value = start;
	document.getElementById("to_date").value = end;
	document.getElementById("daterange_form").submit();

	simplest_analytics_hide_ele('daterange_popup');
}
</script>


<div class="wrap">

	<div class="sa_daterange" onclick="simplest_analytics_toggle_daterange()">
		<?php echo esc_html( $from_date ) ?> - <?php echo esc_html( $to_date ) ?>
		<span class="dashicons dashicons-arrow-down-alt2"></span>
	</div>

	<h2>Simplest Analytivs</h2>


	<nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:10px;">
			<a id="dashboard" onclick="simplest_analytics_toggle_tabs_by_id(this)" class="nav-tab nav-tab-active">
				<?php echo esc_html__( 'Dashboard', 'simplest-analytics' ) ?>
			</a>
			<a id="rawdata" onclick="simplest_analytics_toggle_tabs_by_id(this)" class="nav-tab">
				<?php echo esc_html__( 'Raw Data', 'simplest-analytics' ) ?>
			</a>
	</nav>


	<!-- tab: dashboard -->
    <div class="all_tabs" id="tab_dashboard">

		<!-- top cards -->
		<?php
	    $visits = 0;
        $tracked_events = 0;
	    $session_ids = [];
	    $event_data= [];
        $daily_data = [];
        $url_paras = [];
	    $site_data = [];
	    $ref_data = [];
		$home_url = get_home_url();
		$home_url = str_replace("https://", "", $home_url);
		$home_url = str_replace("http://", "", $home_url);
		$home_url = str_replace("www.", "", $home_url);

	    foreach ($rows as $row) {

			if ( $row->parameters !== "" ) {
		        $expl_paras = explode(",", $row->parameters);
				foreach ( $expl_paras as $expl_p ) {
					$expl_p = explode("==", $expl_p);
					if ( sizeof($expl_p) > 2) {
				        $url_paras[$expl_p[0].'='.$expl_p[1]][] = $row;
					}
				}
			}

		    if ($row->track_type == "event") {
				if ( $row->event_action !== "" ) {
					$event_data[$row->event_action][] = $row;
			        $tracked_events++;
				}
		    }
			else if ( $row->track_type == "pageview" ) {
			    $visits++;
			    $session_ids[] = $row->session_id;
				$website = $row->website;
				$website = str_replace("https://", "", $website);
				$website = str_replace("http://", "", $website);
				$website = str_replace("www.", "", $website);
				$website = str_replace($home_url, "", $website);
				if ( strpos($website,"?") > 0 ) {
					$expl_website = explode("?", $website);
					$website = $expl_website[0];
				}
			    $site_data[$website][] = $row;

				$date_clean = strtotime($row->date_full);
				$date_clean = date('Y-m-d', $date_clean);
		        $daily_data[$date_clean][] = $row;

				$ref = $row->referrer;
			    $ref = str_replace("https://", "", $ref);
				$ref = str_replace("http://", "", $ref);
				$ref = str_replace("www.", "", $ref);

				if ( strpos($ref,"?") > 0 ) {
					$expl_referrer = explode("?", $ref);
					$ref = $expl_referrer[0];
				}
				if ( $ref !== "" && strpos($ref,$home_url) !== 0  ) {
				    $ref_data[$ref][] = $row;
				}

			}
		}
	    $unique_visitors = array_unique($session_ids);
	    $unique_visitors = sizeof($unique_visitors);
	    ksort($daily_data);
		?>
		<div class="top_card">
			<span><?php echo esc_html( $visits ) ?></span>
			<label>
				<?php echo esc_html__( 'Visits', 'simplest-analytics') ?>
			</label>
		</div>

		<div class="top_card">
			<span><?php echo esc_html( $unique_visitors ) ?></span>
			<label>
				<?php echo esc_html__( 'Unique Visitors', 'simplest-analytics') ?>
			</label>
		</div>

		<div class="top_card">
			<span><?php echo esc_html( $tracked_events ) ?></span>
			<label>
				<?php echo esc_html__( 'Tracked events', 'simplest-analytics') ?>
			</label>
		</div>
		<!-- END top cards -->


		<!-- daily visits -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(simplest_analytics_drawvisitsChart);

		function simplest_analytics_drawvisitsChart() {
			var data = google.visualization.arrayToDataTable([
				['<?php echo esc_html__( 'Date', 'simplest-analytics' ) ?>', '<?php echo esc_html__( 'Visits', 'simplest-analytics' ) ?>'],
			<?php
			if ( sizeof($daily_data) == 0 ) {
				?>['<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',0],<?php
			}
			else {
				foreach ($daily_data as $key => $val) {
					$visits = 0;
					$session_ids = [];
					foreach ($val as $v) {
						$visits++;
						$session_ids[] = $v->session_id;
					}
					$unique_visitors = array_unique($session_ids);
					$unique_visitors = sizeof($unique_visitors);
					?>['<?php echo esc_html( $key ) ?>',<?php echo esc_html( $visits ) ?>],<?php
				}
			}
			?>
			]);

			var options = {
				title: '<?php echo esc_html__( 'Daily visits', 'simplest-analytics' ) ?>',
				curveType: 'function',
				legend: { position: 'none' },
				vAxis: { 
					format: '#',				
				}
			};

			var chart = new google.visualization.LineChart(document.getElementById('daily_chart'));

			chart.draw(data, options);

		}
		</script>
		<div class="chart_table chart_table_3col" style="height:300px" id="daily_chart"></div>
		<!-- END daily visits -->




		<!-- daily visits -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(simplest_analytics_drawChart_dvisits);

		function simplest_analytics_drawChart_dvisits() {
			var data = google.visualization.arrayToDataTable([
				['<?php echo esc_html__( 'Date', 'simplest-analytics' ) ?>', '<?php echo esc_html__( 'Unique visitors', 'simplest-analytics' ) ?>'],
			<?php
			if ( sizeof($daily_data) == 0 ) {
				?>
				[
				'<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',
				0,
				],
				<?php
			}
			else {
				foreach ($daily_data as $key => $val) {
					$visits = 0;
					$session_ids = [];
					foreach ($val as $v) {
						$visits++;
						$session_ids[] = $v->session_id;
					}
					$unique_visitors = array_unique($session_ids);
					$unique_visitors = sizeof($unique_visitors);
					?>
						[
						'<?php echo esc_html( $key ) ?>',
						<?php echo esc_html( $unique_visitors ) ?>,
						],
						<?php
				}
			}
			?>
			]);

			var options = {
			title: '<?php echo esc_html__( 'Daily unique visitors', 'simplest-analytics' ) ?>',
			curveType: 'function',
			legend: { position: 'none' },
			vAxis: { 
				format: '#',				
			}
			};

			var chart = new google.visualization.LineChart(document.getElementById('daily_uniques'));

			chart.draw(data, options);
		}
		</script>
		<div class="chart_table chart_table_3col" style="height:300px" id="daily_uniques"></div>
		<!-- END daily uniques -->






		<!-- top websites -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(simplest_analytics_drawTable);

		function simplest_analytics_drawTable() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', '<?php echo esc_html__( 'Most visited sites', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Visits', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Unique visitors', 'simplest-analytics' ) ?>');
			data.addRows([
				
			<?php
			if ( sizeof($site_data) == 0 ) {
				?>
				[
				'<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',
				0,
				0,
				],
				<?php
			}
			else {
				foreach ($site_data as $key => $val) {
					$visits = 0;
					$session_ids = [];
					foreach ($val as $v) {
						$visits++;
						$session_ids[] = $v->session_id;
					}
					$unique_visitors = array_unique($session_ids);
					$unique_visitors = sizeof($unique_visitors);
					?>
						[
						'<?php echo esc_html( $key ) ?>',
						<?php echo esc_html( $visits ) ?>,
						<?php echo esc_html( $unique_visitors ) ?>,
						],
						<?php
				}
			}
            ?>
			]);
			

			var table = new google.visualization.Table(document.getElementById('top_website_div'));

			table.draw(data, {showRowNumber: true, sortColumn: 1, sortAscending: false, desc: true, width: '100%', height: '100%'});
		}
		</script>
		<div class="chart_table chart_table_3col" id="top_website_div"></div>
		<!-- END top websites -->




		<!-- referrer -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(simplest_analytics_drawTable_referrer);

		function simplest_analytics_drawTable_referrer() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', '<?php echo esc_html__( 'Top external referrers', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Visits', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Unique visitors', 'simplest-analytics' ) ?>');
			data.addRows([
				
			<?php	
			if ( sizeof($ref_data) == 0 ) {
				?>
				[
				'<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',
				0,
				0,
				],
				<?php
			}
			else {
				foreach ($ref_data as $key => $val) {
					$visits = 0;
					$session_ids = [];
					foreach ($val as $v) {
						$visits++;
						$session_ids[] = $v->session_id;
					}
					$unique_visitors = array_unique($session_ids);
					$unique_visitors = sizeof($unique_visitors);
					?>
					[
					'<?php echo esc_html( $key ) ?>',
					<?php echo esc_html( $visits ) ?>,
					<?php echo esc_html( $unique_visitors ) ?>,
					],
					<?php
				}
	    	}
            ?>
			]);
			

			var table = new google.visualization.Table(document.getElementById('referrer_div'));

			table.draw(data, {showRowNumber: true, sortColumn: 1, sortAscending: false, desc: true, width: '100%', height: '100%'});
		}
		</script>
		<div class="chart_table chart_table_3col" id="referrer_div"></div>
		<!-- END referrer -->


		<!-- url paras -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(simplest_analytics_drawTable_para);

		function simplest_analytics_drawTable_para() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', '<?php echo esc_html__( 'Top URL parameters', 'simplest-analytics' ) ?>');
			data.addColumn('string', '<?php echo esc_html__( 'Value', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Visits', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Unique visitors', 'simplest-analytics' ) ?>');
			data.addRows([
				
			<?php	
			if ( sizeof($url_paras) == 0 ) {
				?>
				[
				'<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',
				'',
				0,
				0,
				],
				<?php
			}
			else {
				foreach ($url_paras as $key => $val) {
					$visits = 0;
					$session_ids = [];
					foreach ($val as $v) {
						$visits++;
						$session_ids[] = $v->session_id;
					}
					$unique_visitors = array_unique($session_ids);
					$unique_visitors = sizeof($unique_visitors);
					$split_key = explode("=", $key);
					$key_one = isset($split_key[0]) ? $split_key[0] : "";
					$key_two = isset($split_key[1]) ? $split_key[1] : "";
					?>
					[
					'<?php echo esc_html( $key_one ) ?>',
					'<?php echo esc_html( $key_two ) ?>',
					<?php echo esc_html( $visits ) ?>,
					<?php echo esc_html( $unique_visitors ) ?>,
					],
					<?php
				}
	    	}
            ?>
			]);
			

			var table = new google.visualization.Table(document.getElementById('urlpara_div'));

			table.draw(data, {showRowNumber: true, sortColumn: 2, sortAscending: false, desc: true, width: '100%', height: '100%'});
		}
		</script>
		<div class="chart_table chart_table_3col" id="urlpara_div"></div>
		<!-- END url paras -->




		<!-- events -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(simplest_analytics_drawTable_events);

		function simplest_analytics_drawTable_events() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', '<?php echo esc_html__( 'Tracked events', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Visits', 'simplest-analytics' ) ?>');
			data.addColumn('number', '<?php echo esc_html__( 'Unique visitors', 'simplest-analytics' ) ?>');
			data.addRows([
				
			<?php	
			if ( sizeof($event_data) == 0 ) {
				?>
				[
				'<?php echo esc_html__( 'no results', 'simplest-analytics' ) ?>',
				0,
				0,
				],
				<?php
			}
			else {
		    foreach ($event_data as $key => $val) {
			    $visits = 0;
			    $session_ids = [];
			    foreach ($val as $v) {
				    $visits++;
				    $session_ids[] = $v->session_id;
			    }
			    $unique_visitors = array_unique($session_ids);
			    $unique_visitors = sizeof($unique_visitors);
            	?>
					[
					'<?php echo esc_html( $key ) ?>',
					<?php echo esc_html( $visits ) ?>,
					<?php echo esc_html( $unique_visitors ) ?>,
					],
					<?php
		    	}
	    	}
            ?>
			]);
			

			var table = new google.visualization.Table(document.getElementById('events_div'));

			table.draw(data, {showRowNumber: true, sortColumn: 1, sortAscending: false, desc: true, width: '100%', height: '100%'});
		}
		</script>
		<div class="chart_table chart_table_3col" id="events_div"></div>
		<!-- END events -->



	</div>
	<!-- END tab: dashboard -->

	<!-- tab: rawdata -->
		<div class="all_tabs" id="tab_rawdata" style="display:none">
		<!-- all data -->
		<script type="text/javascript">
		google.charts.load('current', {'packages':['table']});
		google.charts.setOnLoadCallback(simplest_analytics_drawTable_raw);

		function simplest_analytics_drawTable_raw() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'date');
			data.addColumn('string', 'type');
			data.addColumn('string', 'website');
			data.addColumn('string', 'referrer');
			data.addColumn('string', 'event');
			data.addColumn('string', 'Session');
			data.addRows([
				<?php
                $user_no = 1;
                $session_userid = [];
	    foreach ($rows as $row) {

			$date_clean = strtotime($row->date_full);
			$date_clean = date('Y-m-d H:i', $date_clean); 
			$website = str_replace("https://", "", $row->website);
			$website = str_replace("http://", "", $website);
			$website = str_replace("www.", "", $website);
			$website = str_replace($home_url, "", $website);
			if ( strpos($website,"?") > 0 ) {
				$expl_website = explode("?", $website);
				$website = $expl_website[0];
			}
			if ( isset($session_userid[$row->session_id]) ) {
		        $user_session = $session_userid[$row->session_id];
			}
			else {
				$user_session = "Session/User #" . $user_no;
		                $session_userid[$row->session_id] = $user_session;
				$user_no++;
			}

	        $clean_paras = [];
			if ( $row->parameters !== "" ) {
				$expl_para = explode(",", $row->parameters);
				foreach ( $expl_para as $expl_p ) {
					$expl_p_split = explode("==", $expl_p);
					if ( sizeof($expl_p_split) > 2 ) {
				        $clean_paras[] = $expl_p_split[0] . '=' . $expl_p_split[1];
					}
				}
			}
	        $final_paras = "";
			if ( sizeof($clean_paras) > 0 ) {
				$final_paras = join(",", $clean_paras);
			}
	        
            ?>
			[
			'<?php echo esc_html( $date_clean ) ?>',
			'<?php echo esc_html( $row->track_type ) ?>',
			'<?php echo esc_html( $website ) ?>',
			'<?php echo esc_html( $row->referrer ) ?>',
			'<?php echo esc_html( $row->event_action ) ?>',
			'<?php echo esc_html( $user_session ) ?>',
			],
			<?php
	    }
            ?>
			]);
			

			var table = new google.visualization.Table(document.getElementById('table_div'));

			table.draw(data, {showRowNumber: true, sortColumn: 0, sortAscending: false, width: '100%', height: '100%'});
		}
		</script>
		<div class="chart_table" id="table_div"></div>
		<!-- END all data -->
	</div>
	<!-- END tab rawdata -->

</div>



<!-- popup daterange -->
<div id="sa_bg" style="display:none" onclick="simplest_analytics_close_all_popups()"></div>
<div id="daterange_popup" class="popup_closed closepopup box-shadow-3">

	<div id="select_ranges">
		<div class="daterange_head">
			<?php echo esc_html__( 'choose daterange', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('today')">
			<?php echo esc_html__( 'today', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('yesterday')">
			<?php echo esc_html__( 'yesterday', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('last7')">
			<?php echo esc_html__( 'last 7 days', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('last14')">
			<?php echo esc_html__( 'last 14 days', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('last30')">
			<?php echo esc_html__( 'last 30 days', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('lastmonth')">
			<?php echo esc_html__( 'last month', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_daterange_sel('alltime')">
			<?php echo esc_html__( 'alltime', 'simplest-analytics') ?>
		</div>

		<div class="datechoice" onclick="simplest_analytics_show_ele('select_custom');simplest_analytics_hide_ele('select_ranges')">
			<?php echo esc_html__( 'custom', 'simplest-analytics') ?>
		</div>
	</div>

	<div id="select_custom" style="display:none">
		<div class="daterange_head">
			<span onclick="simplest_analytics_hide_ele('select_custom');simplest_analytics_show_ele('select_ranges')" class="dashicons dashicons-arrow-left-alt"></span>
			<span class="hlnb"><?php echo esc_html__( 'custom daterange', 'simplest-analytics') ?></span>
		</div>
		<div class="select_custom_date">
			<label><?php echo esc_html__( 'from:', 'simplest-analytics') ?></label>
			<input name="from" id="from" type="date" value="<?php echo esc_html( $from_date ) ?>"/>
		</div>
		<div class="select_custom_date">
			<label><?php echo esc_html__( 'to:', 'simplest-analytics') ?></label>
			<input name="to" id="to" type="date" value="<?php echo esc_html( $to_date ) ?>"/>
		</div>
		<div class="btn_select_date button-primary" onclick="simplest_analytics_apply_custom_date()">
			<?php echo esc_html__( 'apply daterange', 'simplest-analytics') ?>
		</div>
	</div>

</div>
<!-- END popup daterange -->