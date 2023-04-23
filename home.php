<?php
	//Include Configuration File
	include('config.php');
	include('mysql.php');

	$_SESSION['redir'] = 'home.php';

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		header('location:login.php');
		die();
	} else {

		include('inchtmlheader.php');
		
		$link = OpenDB();
		
#############  CASH FLOW GRAPH ##############
switch ($_SESSION['prefcashflowview']) {
	case "0": #90 days
		$groupbytext = " GROUP BY CONCAT(YEAR(itemdate), '/', MONTH(itemdate), '/', DAY(itemdate)) ORDER BY itemdate";
		$graphinterval = "INTERVAL 3 MONTH";
		$option0 = " selected";
	break;
	case "1": #1 year
		$groupbytext = " GROUP BY CONCAT(YEAR(itemdate), '/', WEEK(itemdate)) ORDER BY itemdate";
		$graphinterval = "INTERVAL 1 YEAR";
		$option1 = " selected";
	break;
	case "2": #10 years
		$groupbytext = " GROUP BY YEAR(itemdate) ORDER BY itemdate";
		$graphinterval = "INTERVAL 10 YEAR";
		$option2 = " selected";
	break;
	case "3": #20 years
		$groupbytext = " GROUP BY YEAR(itemdate) ORDER BY itemdate";
		$graphinterval = "INTERVAL 20 YEAR";
		$option3= " selected";
	break;
	case "4": #40 years
		$groupbytext = " GROUP BY YEAR(itemdate) ORDER BY itemdate";
		$graphinterval = "INTERVAL 40 YEAR";
		$option4 = " selected";
	break;
}
?>
<div class="card">
	<div class="card-body">
		<div id="divcashflowview" class="float-end" >
			<select class="form-select" id="prefcashflowview" type="text" onchange="switchview(this.value);">
				<option<?=$option0?> value=0>3 months</option>
				<option<?=$option1?> value=1>1 year</option>
				<option<?=$option2?> value=2>10 years</option>
				<option<?=$option3?> value=3>20 years</option>
				<option<?=$option4?> value=4>40 years</option>
			</select>
		</div>
		<h5 class="card-title">Cash Flow - Account Favourites</h5>
		<canvas id="myChart" height="60em"></canvas>
	</div>
</div>
<script>

const options = {
  type: 'line',
  data: {
    datasets: [
<?php
	#Loop through favourite accounts
	$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." AND accountfav=1 AND accounttype IN ('bank') ORDER BY accountname";
	$query2 = mysqli_query($link, $mysqlquery2);
	$accountlist = "";
	$totalaccountbalance = 0;
	while($array2 = mysqli_fetch_array($query2)) {
		echo "{";
		echo "pointStyle: 'circle',";
		echo "pointRadius: 2,";
		echo "pointHoverRadius: 5,";
		echo "label: '" . $array2['accountname'] . "',\n";
		echo "data: [\n";
	
		#Get the summarised by day data
		$mysqlquery = "SELECT DATE(itemdate) day, SUM(IF (itemaccountto = " . $array2['accountid'] . ",`itemamount`,0)) daytotal, SUM(IF (itemaccountfrom = " . $array2['accountid'] . ",-`itemamount`,0)) daytotalminus, itemaccountto, itemaccountfrom FROM items, itemparents WHERE owneruid=".$_SESSION['uid']." AND items.itemparentid = itemparents.itemparentid AND (itemaccountfrom = " . $array2['accountid'] . " OR itemaccountto = " . $array2['accountid'] . ") AND itempaid = 0 AND (itemdate < DATE_ADD(now(), " . $graphinterval . "))" . $groupbytext;
		$query = mysqli_query($link, $mysqlquery);
		$cumtotal = $array2['accountbalance'] ; #Get account balance
		while($array = mysqli_fetch_array($query)) {
			$cumtotal += $array['daytotal'] + $array['daytotalminus'];
			echo "\t{ x: '".$array['day']."', y: ".$cumtotal." },\n";
		}
		echo "\t],\n\tborderColor: '#"  . $array2['accountcolour'] . "',\n\tbackgroundColor: '#" . $array2['accountcolour'] . "',\n";
		echo "},\n";
		$accountlist .= $array2['accountid'] . ",";
		$totalaccountbalance += $array2['accountbalance'];
	}
		$accountlist = rtrim($accountlist, ",");
		echo "{";
		echo "pointStyle: 'circle',";
		echo "pointRadius: 2,";
		echo "pointHoverRadius: 5,";
		echo "label: 'Total',\n";
		echo "data: [\n";
	
		#Get the summarised by day data
		$mysqlquery = "SELECT DATE(itemdate) day, SUM(IF (itemaccountto IN (" . $accountlist . "),`itemamount`,0)) daytotal, SUM(IF (itemaccountfrom IN (" . $accountlist . "),-`itemamount`,0)) daytotalminus, itemaccountto, itemaccountfrom FROM items, itemparents WHERE owneruid=".$_SESSION['uid']." AND items.itemparentid = itemparents.itemparentid AND (itemaccountfrom IN (" . $accountlist . ") OR itemaccountto IN (" . $accountlist . ")) AND itempaid = 0 AND (itemdate < DATE_ADD(now(), " . $graphinterval . "))" . $groupbytext;
		$query = mysqli_query($link, $mysqlquery);
		$cumtotal = $totalaccountbalance;
		while($array = mysqli_fetch_array($query)) {
			$cumtotal += $array['daytotal'] + $array['daytotalminus'];
			echo "\t{ x: '".$array['day']."', y: ".$cumtotal." },\n";
		}
		echo "\t],\n\tborderColor: '#000000',\n\tbackgroundColor: '#000000',\n";
		echo "},\n";
	echo "],\n";
?>	  
  },
  options: {
    animations: {
      radius: {
        duration: 400,
        easing: 'linear',
        loop: (context) => context.active
      }
    },
    scales: {
      x: {
        type: 'time',
		time: {
			unit:'month'
		},
		displayFormats: {
			day: 'YYYY-MM-DD'
		}
      }
    }
  }
}

const ctx = document.getElementById('myChart').getContext('2d');
new Chart(ctx, options);
</script>

<?php
	
	if ($_SESSION['prefShowFavs'] == '1') {
		$favsChecked = " checked";
		$favsCheckedSQL = " accountfav=1 AND";
	} else {
		$favsChecked = "";
		$favsCheckedSQL = "";
	}
	if ($_SESSION['prefShowFavsAssets'] == '1') {
		$favsAssetsChecked = " checked";
		$favsAssetsCheckedSQL = " accountfav=1 AND";
	} else {
		$favsAssetsChecked = "";
		$favsAssetsCheckedSQL = "";
	}
#############  ACCOUNTS ##############

?>
<br>
<div class="row">
<div class="col">
<div class="card">
<div class="card-body">
	<!-- Button for account add modal -->
	<button type="button" class="btn btn-primary btn-light float-end" data-bs-toggle="modal" data-bs-target="#addaccountModal">
	  Add
	</button>



<div class="form-check form-switch float-end">
  <input<?=$favsChecked?> class="form-check-input" type="checkbox" role="switch" id="flexCheckDefault" onchange="updatePrefsShowFavs(this.checked)">
  <label class="form-check-label" for="flexCheckDefault">
    <div class="text-muted">Show favourites only&nbsp;&nbsp;&nbsp;&nbsp;</div>
  </label>
</div> 
<?php
		echo '<h5 class="card-title">Accounts</h5>';
		echo '<table id="mytable1" class="table table-hover"><thead><tr><th scope="col"><img width=20 src="images/favon.png" class="rounded" /></th><th scope="col">Colour</th><th scope="col">Account Name</th><th scope="col">Balance</th></tr></thead><tbody class="table-group-divider">';

		$query = mysqli_query($link, "SELECT * FROM accounts WHERE" . $favsCheckedSQL . " owneruid=".$_SESSION['uid'] . " AND accounttype NOT IN ('expense','income','asset') ORDER BY accountfav DESC, accountname");
		$total = 0.00;
		while($array = mysqli_fetch_array($query)) {
			$total += $array['accountbalance'];
			?>
			<tr>
			<td>
			<div>
			<?php
			if ($array['accountfav'] == 1) {
				$imagefav = "images/favon.png";
				$state = 0;
			} else {
				$imagefav = "images/favoff.png";
				$state = 1;
			}
			?>
			<img width=20 src="<?=$imagefav?>" class="rounded" onclick="sqlaccounttogglefav('<?=$array['accountid']?>','<?=$state?>');" />
			</div>
			</td>
			<td>
			<div>
				<input class="form-control form-control-color" type="color" value="#<?=$array['accountcolour']?>" onchange="sqlaccountupdatecolour('<?=$array['accountid']?>',this.value);">
			</div>
			</td>
			<td>
			<div><input class="form-control-plaintext" type="text" onfocusout="updatefield('accountname<?=$array['accountid']?>','accounts','<?=$array['accountid']?>','accountname',this.value,'accountid')" id="accountname<?=$array['accountid']?>" value="<?=$array['accountname']?>">
			</div>
			</td>
			<td>
			<div><input class="form-control-plaintext" type="text" onfocusout="updatefield('accountid<?=$array['accountid']?>','accounts','<?=$array['accountid']?>','accountbalance',this.value,'accountid')" id="accountid<?=$array['accountid']?>" value="$<?=number_format($array['accountbalance'],2,'.',',')?>">
			</div>
			</td>
			</tr>
			<?php
		} 	
?>
			<tfoot>
			<tr>
			<td>
			<div>
			</div>
			</td>
			<td>
			<div>
				<input class="form-control form-control-color" type="color" value="#000000" >
			</div>
			</td>
			<td><h6>Total</h6>
			</td>
			<td>
			<div><h6>$<?=number_format($total,2,'.',',')?></h6>
			</div>
			</td>
			</tr>
			</tfoot>
<?php
		echo '</tbody></table></div>';
		echo '</div></div>';
#############  ASSETS ##############

?>
<div class="col">
<div class="card">
<div class="card-body">

	<!-- Button for asset add modal -->
	<button type="button" class="btn btn-primary btn-light float-end" data-bs-toggle="modal" data-bs-target="#addassetModal">
	  Add
	</button>

<div class="form-check form-switch float-end">
  <input<?=$favsAssetsChecked?> class="form-check-input" type="checkbox" role="switch" id="flexCheckDefault" onchange="updatePrefsShowFavsAssets(this.checked)">
  <label class="form-check-label" for="flexCheckDefault">
    <div class="text-muted">Show favourites only&nbsp;&nbsp;&nbsp;&nbsp;</div>
  </label>
</div> 

<?php
		echo '<h5 class="card-title">Assets</h5>';
		echo '<table id="mytable1" class="table table-hover"><thead><tr><th scope="col"><img width=20 src="images/favon.png" class="rounded" /></th><th scope="col">Colour</th><th scope="col">Account Name</th><th scope="col">Balance</th></tr></thead><tbody class="table-group-divider">';

		$query = mysqli_query($link, "SELECT * FROM accounts WHERE" . $favsAssetsCheckedSQL . " owneruid=".$_SESSION['uid'] . " AND accounttype IN ('asset') ORDER BY accountfav DESC, accountname");
		$total = 0.00;
		while($array = mysqli_fetch_array($query)) {
			$total += $array['accountbalance'];
			?>
			<tr>
			<td>
			<div>
			<?php
			if ($array['accountfav'] == 1) {
				$imagefav = "images/favon.png";
				$state = 0;
			} else {
				$imagefav = "images/favoff.png";
				$state = 1;
			}
			?>
			<img width=20 src="<?=$imagefav?>" class="rounded" onclick="sqlaccounttogglefav('<?=$array['accountid']?>','<?=$state?>');" />
			</div>
			</td>
			<td>
			<div>
				<input class="form-control form-control-color" type="color" value="#<?=$array['accountcolour']?>" onchange="sqlaccountupdatecolour('<?=$array['accountid']?>',this.value);">
			</div>
			</td>
			<td>
			<div><input class="form-control-plaintext" type="text" onfocusout="updatefield('accountname<?=$array['accountid']?>','accounts','<?=$array['accountid']?>','accountname',this.value,'accountid')" id="accountname<?=$array['accountid']?>" value="<?=$array['accountname']?>">
			</div>
			</td>
			<td>
			<div><input class="form-control-plaintext" type="text" onfocusout="updatefield('accountid<?=$array['accountid']?>','accounts','<?=$array['accountid']?>','accountbalance',this.value,'accountid')" id="accountid<?=$array['accountid']?>" value="$<?=number_format($array['accountbalance'],2,'.',',')?>">
			</div>
			</td>
			</tr>
			<?php
		} 	
?>
			<tfoot>
			<tr>
			<td>
			<div>
			</div>
			</td>
			<td>
			<div>
				<input class="form-control form-control-color" type="color" value="#000000" >
			</div>
			</td>
			<td><h6>Total</h6>
			</td>
			<td>
			<div><h6>$<?=number_format($total,2,'.',',')?></h6>
			</div>
			</td>
			</tr>
			</tfoot>
<?php
		echo '</tbody></table></div>';
		echo '</div></div>';
		echo '</div>';
		echo '</br>';
#############  TRANSACTIONS ##############
		echo '<div class="card">';
		echo '<div class="card-body">';
?>
		<!-- Button for item add modal -->
		<button type="button" class="btn btn-primary btn-light float-end" data-bs-toggle="modal" data-bs-target="#additemModal">
		  Add
		</button>

<?php
		echo '<h5 class="card-title">Transactions - next occurrence</h5>';
		echo '<table id="mytable" class="display table table-hover"><thead><tr><th scope="col">Paid</th><th scope="col">Edit</th><th scope="col">Delete</th><th scope="col">Name</th><th scope="col">Due</th><th scope="col">Amount</th><th scope="col">Account From</th><th scope="col">Account To</th></tr></thead><tbody>';
		#$mysqlquery = "SELECT * FROM items, itemparents WHERE owneruid=".$_SESSION['uid']." AND items.itemparentid = itemparents.itemparentid AND (itemdate < DATE_ADD(now(), INTERVAL 30 DAY)) AND itempaid = 0 ORDER BY itemdate,itemamount DESC";
		
		#iterate through itemparents then grab the first unpaid occurance of each one

		#$mysqlquery = "SELECT * FROM items, itemparents WHERE owneruid=".$_SESSION['uid']." AND items.itemparentid = itemparents.itemparentid AND (itemdate < DATE_ADD(now(), INTERVAL 30 DAY)) AND itempaid = 0 ORDER BY itemdate,itemamount DESC";
		$mysqlquery = "SELECT * FROM items, itemparents WHERE owneruid=".$_SESSION['uid']." AND items.itemparentid = itemparents.itemparentid AND itempaid = 0 GROUP BY itemparents.itemparentid ORDER BY itemdate,itemamount DESC";
		$query = mysqli_query($link, $mysqlquery);
		while($array = mysqli_fetch_array($query)) {
			$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." ORDER BY accountname";
			$query2 = mysqli_query($link, $mysqlquery2);
			$formselect = "";
			$curselectedname = "";
			while($array2 = mysqli_fetch_array($query2)) {
				$curselected = "";
				if ($array2['accountid'] == $array['itemaccountfrom'] ) { 
					$curselected = " selected";
					$curselectedname = $array2['accountname'];
				}
				$formselect .= "<option value=" . $array2['accountid'] . $curselected . ">" . $curselectedname . "</option>";
			}
			$date1 = new DateTime($array['itemdate']);
			$date2 = new DateTime('NOW - 1 day');
			if ($date1 < $date2) {
				$overdue = " text-danger";
			}elseif($curselectedname == "Income") {
				$overdue = " text-success";
			}else{
				$overdue = "";
			}
			?>
			<tr>
			<td>
				<a href="javascript:void(0);">
					<img width=20 src="images/paid.png" onclick="sqlitemupdatepaid('<?=$array['itemid']?>','<?=$array['itemaccountfrom']?>','<?=$array['itemaccountto']?>','<?=$array['itemamount']?>')" />
				</a>
			</td>
			<td>
				<a href="javascript:void(0);">
					<!-- Button for item edit modal -->
					<img width=20 src="images/edit.png" data-bs-toggle="modal" data-bs-target="#itemeditModal" data-bs-itemid="<?=$array['itemid']?>" data-bs-itemparentname="<?=$array['itemparentname']?>" data-bs-itemparentamount="<?=$array['itemparentamount']?>" data-bs-itemaccountfrom="<?=$array['itemaccountfrom']?>" data-bs-itemaccountto="<?=$array['itemaccountto']?>" data-bs-itemparentdate="<?=$array['itemdate']?>" data-bs-itemparentrepeattype="<?=$array['itemparentrepeattype']?>" data-bs-itemparentrepeatfreq="<?=$array['itemparentrepeatfreq']?>" data-bs-itemparentrepeatdate="<?=$array['itemparentrepeatdate']?>" />
				</a>
			</td>
			<td>
				<a href="javascript:void(0);">
					<!-- Button for item delete modal -->
					<img width=20 src="images/delete.png" data-bs-toggle="modal" data-bs-target="#itemdeleteModal" data-bs-itemid="<?=$array['itemid']?>" data-bs-date="<?=$array['itemdate']?>"/>
				</a>
			</td>
			<td>
				<input class="form-control-plaintext<?=$overdue?>" type="text" onfocusout="updatefield('itemname<?=$array['itemid']?>','items','<?=$array['itemid']?>','itemname',this.value,'itemid')" id="itemname<?=$array['itemid']?>" value="<?=$array['itemname']?>">
			</td>
			<td>
				<div>
					<input class="form-control-plaintext<?=$overdue?>" type="text" onfocusout="updatefield('itemdate<?=$array['itemid']?>','items','<?=$array['itemid']?>','itemdate',this.value,'itemid')" id="itemdate<?=$array['itemid']?>" value="<?=$array['itemdate']?>">
				</div>
			</td>
			<td>
				<div>
					<input class="form-control-plaintext<?=$overdue?>" type="text" onfocusout="updatefield('itemamount<?=$array['itemid']?>','items','<?=$array['itemid']?>','itemamount',this.value,'itemid')" id="itemamount<?=$array['itemid']?>" value="$<?=number_format($array['itemamount'],2,'.',',')?>">
				</div>
			</td>
			<td>
				<div>
					<select class="form-select" type="text" onchange="sqlitemupdateaccountfrom('<?=$array['itemparentid']?>',this.value)" id="itemaccountfrom<?=$array['itemid']?>">
					<?php
						echo $formselect;
					?>
					</select>
				</div>
			<td>
				<div>
					<select class="form-select" type="text" onchange="sqlitemupdateaccountto('<?=$array['itemparentid']?>',this.value)" id="itemaccountto<?=$array['itemid']?>">
					<?php
						$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." ORDER BY accountname";
						$query2 = mysqli_query($link, $mysqlquery2);
						while($array2 = mysqli_fetch_array($query2)) {
							$curselected = "";
							if ($array2['accountid'] == $array['itemaccountto'] ) { $curselected = " selected"; }
							echo "<option value=" . $array2['accountid'] . $curselected . ">" . $array2['accountname'] . "</option>";
						}
					?>
					</select>
				</div>
			</td>
			</tr>
			<?php
		}
		echo '</tbody></table></div>';
		echo '</div><br />';

	}
?>
	<!-- Modal for account add-->
	<div class="modal fade" id="addaccountModal" tabindex="-1" aria-labelledby="addaccountModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="addaccountModalLabel">Add Account</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="accountname" placeholder="Account Name">
							<label for="accountname">Account Name</label>
						</div>
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="accountbalance" placeholder="Balance">
							<label for="accountbalance">Balance</label>
						</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="sqlaccountinsert('bank',document.getElementById('accountname').value,document.getElementById('accountbalance').value);">Save changes</button>
				  </div>
				</div>
		  </div>
	</div>
	<!-- End account add modal -->
	
	<!-- Modal for asset add-->
	<div class="modal fade" id="addassetModal" tabindex="-1" aria-labelledby="addassetModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="addassetModalLabel">Add Asset</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="accountnameasset" placeholder="Account Name">
							<label for="accountname">Asset Name</label>
						</div>
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="accountbalanceasset" placeholder="Balance">
							<label for="accountbalance">Balance</label>
						</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="sqlaccountinsert('asset',document.getElementById('accountnameasset').value,document.getElementById('accountbalanceasset').value);">Save changes</button>
				  </div>
				</div>
		  </div>
	</div>
	<!-- End asset add modal -->

	<!-- Modal for item add -->
	<div class="modal fade" id="additemModal" tabindex="-1" aria-labelledby="additemModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="additemModalLabel">Add Transaction</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
				<div class="form-floating mb-3">
					<input type="text" class="form-control" id="itemparentname" placeholder="Description">
					<label for="itemparentname">Description</label>
				</div>
				<div class="form-floating mb-3">
					<input type="text" class="form-control" id="itemparentamount" placeholder="Amount">
					<label for="itemparentamount">Amount</label>
				</div>
				<div class="form-floating mb-3">
					<select class="form-select" id="itemaccountfrom" type="text">
					<?php
						$bankdone = 0;
						$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." AND accounttype NOT IN ('expense','asset') ORDER BY accounttype DESC, accountname";
						$query2 = mysqli_query($link, $mysqlquery2);
						echo "<option>Select Account</option>";
						echo "<option disabled>Income</option>";
						while($array2 = mysqli_fetch_array($query2)) {
							echo "<option value=" . $array2['accountid'] . ">&nbsp;&nbsp;&nbsp;" . $array2['accountname'] . "</option>";
							if ($bankdone == 0 AND $array2['accountype'] = 'bank') {
								$bankdone = 1;
								echo "<option disabled>Accounts</option>";
							}
						}
					?>
					</select>
					<label for="itemaccountfrom">From</label>
				</div>
				<div class="form-floating mb-3">
					<select class="form-select" id="itemaccountto" type="text">
					<?php
						$bankdone = 0;
						$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." AND accounttype NOT IN ('income','asset') ORDER BY accounttype DESC, accountname";
						$query2 = mysqli_query($link, $mysqlquery2);
						echo "<option>Select Account</option>";
						echo "<option disabled>Expense</option>";
						while($array2 = mysqli_fetch_array($query2)) {
							echo "<option value=" . $array2['accountid'] . ">&nbsp;&nbsp;&nbsp;" . $array2['accountname'] . "</option>";
							if ($bankdone == 0 && $array2['accountype'] = 'bank') {
								$bankdone = 1;
								echo "<option disabled>Accounts</option>";
							}
						}
					?>
					</select>
					<label for="itemaccountto">To</label>
				</div>
				<div class="form-floating mb-3">
					<input type="date" class="form-control" id="itemparentdate" placeholder="Due" value="<?php echo date('Y-m-d'); ?>">
					<label for="itemparentdate">Due</label>
				</div>
				<div id="divitemparentfreq" class="form-floating mb-3" >
					<select class="form-select" id="itemparentrepeattype" type="text" onchange="switchrecur(this.value);">
						<option value=0>None</option>
						<option value=2>Weekly</option>
						<option value=3>Monthly</option>
						<option value=4>Annually</option>
					</select>
					<label for="itemparentfreq">Recurring Frequency</label>
				</div>
				<div id="repeattype" style="display: none;">
				<!-- Dynamically populated from js function switcrecur() -->
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="itemparentrepeatfreq" value="1" min="1" max="10" placeholder="Number of weeks">
						<label for="itemparentrepeatfreq">Number of weeks</label>
					</div>
					<div class="form-floating mb-3">
						<input type="date"  class="form-control" id="itemparentrepeatdate" value="2055-08-28" placeholder="Until">
						<label for="itemparentrepeatdate">Until</label>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary" onclick="sqliteminsert();">Save changes</button>
		  </div>
		</div>
	  </div>
	</div>
	<!-- End item add modal -->

	<!-- Modal for item edit -->
	<div class="modal fade" id="itemeditModal" tabindex="-1" aria-labelledby="edititemModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="edititemModalLabel">Edit Transaction</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
				<div class="form-floating mb-3">
					<input type="text" class="form-control" id="eitemparentname" placeholder="Description">
					<label for="eitemparentname">Description</label>
				</div>
				<div class="form-floating mb-3">
					<input type="text" class="form-control" id="eitemparentamount" placeholder="Amount">
					<label for="eitemparentamount">Amount</label>
				</div>
				<div class="form-floating mb-3">
					<select class="form-select" id="eitemaccountfrom" type="text">
					<?php
						$bankdone = 0;
						$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." AND accounttype NOT IN ('expense','asset') ORDER BY accounttype DESC, accountname";
						$query2 = mysqli_query($link, $mysqlquery2);
						echo "<option>Select Account</option>";
						echo "<option disabled>Income</option>";
						while($array2 = mysqli_fetch_array($query2)) {
							echo "<option value=" . $array2['accountid'] . ">&nbsp;&nbsp;&nbsp;" . $array2['accountname'] . "</option>";
							if ($bankdone == 0 AND $array2['accountype'] = 'bank') {
								$bankdone = 1;
								echo "<option disabled>Accounts</option>";
							}
						}
					?>
					</select>
					<label for="eitemaccountfrom">From</label>
				</div>
				<div class="form-floating mb-3">
					<select class="form-select" id="eitemaccountto" type="text">
					<?php
						$bankdone = 0;
						$mysqlquery2 = "SELECT * FROM accounts WHERE owneruid=".$_SESSION['uid']." AND accounttype NOT IN ('income','asset') ORDER BY accounttype DESC, accountname";
						$query2 = mysqli_query($link, $mysqlquery2);
						echo "<option>Select Account</option>";
						echo "<option disabled>Expense</option>";
						while($array2 = mysqli_fetch_array($query2)) {
							echo "<option value=" . $array2['accountid'] . ">&nbsp;&nbsp;&nbsp;" . $array2['accountname'] . "</option>";
							if ($bankdone == 0 && $array2['accountype'] = 'bank') {
								$bankdone = 1;
								echo "<option disabled>Accounts</option>";
							}
						}
					?>
					</select>
					<label for="eitemaccountto">To</label>
				</div>
				<div class="form-floating mb-3">
					<input type="date" class="form-control" id="eitemparentdate" placeholder="Due" value="<?php echo date('Y-m-d'); ?>">
					<label for="eitemparentdate">Due</label>
				</div>
				<div id="divitemparentfreq" class="form-floating mb-3" >
					<select class="form-select" id="eitemparentrepeattype" type="text" onchange="switchrecuredit(this.value);">
						<option value=0>None</option>
						<option value=2>Weekly</option>
						<option value=3>Monthly</option>
						<option value=4>Annually</option>
					</select>
					<label for="eitemparentfreq">Recurring Frequency</label>
				</div>
				<div id="erepeattype" style="display: none;">
				<!-- Dynamically populated from js function switchrecur() -->
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="eitemparentrepeatfreq" value="1" min="1" max="10" placeholder="Number of weeks">
						<label for="eitemparentrepeatfreq">Number of weeks</label>
					</div>
					<div class="form-floating mb-3">
						<input type="date"  class="form-control" id="eitemparentrepeatdate" value="2055-08-28" placeholder="Until">
						<label for="eitemparentrepeatdate">Until</label>
					</div>
				</div>
					<input type="hidden" id="emodalitemid" value="">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary" onclick="sqlitemedit();">Save changes</button>
		  </div>
		</div>
	  </div>
	</div>
	<!-- End item edit modal -->

	<!-- Modal for item delete-->
	<div class="modal fade" id="itemdeleteModal" tabindex="-1" aria-labelledby="itemdeleteModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="itemdeleteModalLabel">Delete Item</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
				  <div class="modal-body">
					<div class="form-check">
					  <input checked class="form-check-input" type="radio" name="deletetypevalue" id="delone" value=1>
					  <label class="form-check-label" for="delone">Just this one</label>
					</div>
					<div class="form-check">
					  <input class="form-check-input" type="radio" name="deletetypevalue" id="delall" value=2 >
					  <label class="form-check-label" for="delall">Delete all of them</label>
					</div>
					<div class="form-check">
					  <input class="form-check-input" type="radio" name="deletetypevalue" id="delforward" value=3>
					  <input class="form-check-input" type="radio" name="deletetypevalue" id="delforward" value=3>
					  <label class="form-check-label" for="delforward"><div id="delforwardtext">Delete from this date forward</div></label>
					</div>
					<input type="hidden" id="modalitemid" value="">
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger" onclick="sqlitemdelete(document.getElementById('modalitemid').value, document.querySelector('input[name=deletetypevalue]:checked').value);">Delete</button>
				  </div>
				</div>
		  </div>
	</div>
	<!-- End item delete modal -->

<?php
	include('inchtmlfooter.php');
	mysqli_close($link);
?>
