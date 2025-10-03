<?php
	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
	require_once "includes/classes/admin-class.php";
    $admins	= new Admins($dbh);
    $id = isset($_GET[ 'customer' ])?$_GET[ 'customer' ]:''; 
    
function getToText($num) {
	$count = 0;
	global $ones, $tens, $triplets;
	$ones = array(
	  '',
	  ' One',
	  ' Two',
	  ' Three',
	  ' Four',
	  ' Five',
	  ' Six',
	  ' Seven',
	  ' Eight',
	  ' Nine',
	  ' Ten',
	  ' Eleven',
	  ' Twelve',
	  ' Thirteen',
	  ' Fourteen',
	  ' Fifteen',
	  ' Sixteen',
	  ' Seventeen',
	  ' Eighteen',
	  ' Nineteen'
	);
	$tens = array(
	  '',
	  '',
	  ' Twenty',
	  ' Thirty',
	  ' Forty',
	  ' Fifty',
	  ' Sixty',
	  ' Seventy',
	  ' Eighty',
	  ' Ninety'
	);
  
	$triplets = array(
	  '',
	  ' Thousand',
	  ' Million',
	  ' Billion',
	  ' Trillion',
	  ' Quadrillion',
	  ' Quintillion',
	  ' Sextillion',
	  ' Septillion',
	  ' Octillion',
	  ' Nonillion'
	);
	return convertNum($num);
  }
  
  /**
   * Function to dislay tens and ones
   */
  function commonloop($val, $str1 = '', $str2 = '') {
	global $ones, $tens;
	$string = '';
	if ($val == 0)
	  $string .= $ones[$val];
	else if ($val < 20)
	  $string .= $str1.$ones[$val] . $str2;  
	else
	  $string .= $str1 . $tens[(int) ($val / 10)] . $ones[$val % 10] . $str2;
	return $string;
  }
  
  /**
   * returns the number as an anglicized string
   */
  function convertNum($num) {
	$num = (int) $num;    // make sure it's an integer
  
	if ($num < 0)
	  return 'negative' . convertTri(-$num, 0);
  
	if ($num == 0)
	  return 'Zero';
	return convertTri($num, 0);
  }
  
  /**
   * recursive fn, converts numbers to words
   */
  function convertTri($num, $tri) {
	global $ones, $tens, $triplets, $count;
	$test = $num;
	$count++;
	// chunk the number, ...rxyy
	// init the output string
	$str = '';
	// to display hundred & digits
	if ($count == 1) {
	  $r = (int) ($num / 1000);
	  $x = ($num / 100) % 10;
	  $y = $num % 100;
	  // do hundreds
	  if ($x > 0) {
		$str = $ones[$x] . ' Hundred';
		// do ones and tens
		$str .= commonloop($y, ' and ', '');
	  }
	  else if ($r > 0) {
		// do ones and tens
		$str .= commonloop($y, ' and ', '');
	  }
	  else {
		// do ones and tens
		$str .= commonloop($y);
	  }
	}
	// To display lakh and thousands
	else if($count == 2) {
	  $r = (int) ($num / 10000);
	  $x = ($num / 100) % 100;
	  $y = $num % 100;
	  $str .= commonloop($x, '', ' Lakh ');
	  $str .= commonloop($y);
	  if ($str != '')
		$str .= $triplets[$tri];
	}
	// to display till hundred crore
	else if($count == 3) {
	  $r = (int) ($num / 1000);
	  $x = ($num / 100) % 10;
	  $y = $num % 100;
	  // do hundreds
	  if ($x > 0) {
		$str = $ones[$x] . ' Hundred';
		// do ones and tens
		$str .= commonloop($y,' and ',' Crore ');
	  }
	  else if ($r > 0) {
		// do ones and tens
		$str .= commonloop($y,' and ',' Crore ');
	  }
	  else {
		// do ones and tens
		$str .= commonloop($y);
	  }
	}
	else {
	  $r = (int) ($num / 1000);
	}
	// add triplet modifier only if there
	// is some output to be modified...
	// continue recursing?
	if ($r > 0)
	  return convertTri($r, $tri+1) . $str;
	else
	  return $str;
  }

    ?>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset=" utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/invoice.css"> <!-- CSS bootstrap -->    
    <link rel="stylesheet" href="component/css/print.css" media="print"> <!-- CSS Print -->
	<script src="component/js/modernizr.js"></script> <!-- Modernizr -->
	<title>Invoice</title>
</head>
<body>
<div class="container">
        <?php
            $info = $admins->getCustomerInfo($id); 
            if (isset($info) && sizeof($info) > 0) {
            $package_id = $info->package_id;
            $packageInfo = $admins->getPackageInfo($package_id);
        ?>
    <div class="row">
        <div class="brand"><img src="component/img/cs.png" alt=""></div>
        <div class="pull-right">Date: <?=date("jS F y")?></div><br>
        <div class="em"><b>Name   : </b> <em><?=$info->full_name?></em></div>
        <div class="em"><b>Address:</b> <em><?=$info->address ?></em></div>
        <div class="em"><b>Contact :</b> <em><?=$info->contact ?></em> </div>
        <div class="em"><b>Package:</b> <em><?=$packageInfo->name?></em> </div>
        <div class="em"><b>IP address:</b> <em><?=$info->ip_address?></em></div>
        <span class="message pull-right">Due Date : <?=date("jS F y",strtotime("+7 day"))?></span>
    </div>
        <?php } ?>
    <div class="row">
        <table class="table table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th>Billing Period</th>
                    <th>Package</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $bills = $admins->fetchindIvidualBill($id);
                $total = 0;
                if (isset($bills) && sizeof($bills) > 0){
                    foreach ($bills as $bill){
                        $total += $bill->amount;
                        ?>
                    <tr>
                         <td><?=date("F Y", strtotime($bill->r_month))?></td>
                        <td><?=$packageInfo->name?></td>
                        <td>₱<?=number_format($bill->amount, 2)?></td>
                    </tr>
                <?php   } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">In Words</td>
                    <th colspan="1"><?=getToText($total)?> Pesos.</th>
                </tr>
                 <tr>
                    <th colspan="2">Total Amount Due</td>
                    <th colspan="1">₱<?=number_format($total, 2)?></th>
                </tr>
            </tfoot>
            <?php 
                } ?>
        </table>

   <h2><strong>We appreciate your prompt payment and value as a customer.</strong></h2>
    <p><strong>Contact us</strong><br>
    FB Page | Customer Service: 0951-6651142 | Billing Department: 0985-3429675</p>
    <p><strong>CORNERSTONE INNOVATE TECH SOL</strong></p>
  </div>
  <div class="printbutton hide-on-small-only pull-left"><a href="#" onClick="javascript:window.print()">Print</a></div>
</body>
<?php include 'includes/footer.php'; ?>
