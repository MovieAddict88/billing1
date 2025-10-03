<?php
	// Start from getting the hader which contains some settings we need
	require_once 'includes/header.php';

	// Redirect visitor to the login page if he is trying to access
	// this page without being logged in
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}

    if (!isset($_GET['id'])) {
        $commons->redirectTo(SITE_PATH.'customers.php');
    }

	require_once "includes/classes/admin-class.php";
	$admins = new Admins($dbh);

    $customerId = $_GET['id'];
    $customerDetails = $admins->fetchCustomerDetails($customerId);
    $customerInfo = $customerDetails['info'];
    $unpaidBills = $customerDetails['unpaid_bills'];
    $paidBills = $customerDetails['paid_bills'];
    $transactions = $customerDetails['transactions'];
    $employer = null;
    if ($customerInfo) {
        $employer = $admins->getEmployerByLocation($customerInfo->conn_location);
    }
?>

<div class="dashboard">
	<div class="col-md-12 col-sm-12" id="customer_details">
		<div class="panel panel-default">
			<div class="panel-heading">
			    <h4>Customer Details</h4>
			</div>
			<div class="panel-body">
                <?php if ($customerInfo): ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <td><?= $customerInfo->full_name ?></td>
                        </tr>
                        <tr>
                            <th>Employer's Name</th>
                            <td><?= $employer ? $employer->full_name : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>NID</th>
                            <td><?= $customerInfo->nid ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?= $customerInfo->address ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= $customerInfo->email ?></td>
                        </tr>
                        <tr>
                            <th>Contact</th>
                            <td><?= $customerInfo->contact ?></td>
                        </tr>
                        <tr>
                            <th>Connection Location</th>
                            <td><?= $customerInfo->conn_location ?></td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td><?= $customerInfo->ip_address ?></td>
                        </tr>
                        <tr>
                            <th>Connection Type</th>
                            <td><?= $customerInfo->conn_type ?></td>
                        </tr>
                    </table>

                    <h3>Billing History</h3>
                    <?php
                        $allBills = array_merge($unpaidBills, $paidBills);
                        usort($allBills, function($a, $b) {
                            return strtotime($b->g_date) - strtotime($a->g_date);
                        });
                        $packageInfo = $admins->getPackageInfo($customerInfo->package_id);
                        $packageName = $packageInfo ? $packageInfo->name : 'N/A';
                    ?>
                    <?php if ($allBills && count($allBills) > 0): ?>
                        <table class="table table-striped">
                            <thead style="background-color: #008080; color: white;">
                                <tr>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Month</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allBills as $bill): ?>
                                    <tr>
                                        <td><?= $packageName ?></td>
                                        <td><?= $bill->amount ?></td>
                                        <td><?= $bill->r_month ?></td>
                                        <td><?= $bill->paid ? 'Yes' : 'No' ?></td>
                                        <td><?= $bill->paid ? '0.00' : $bill->amount ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No billing history found.</p>
                    <?php endif; ?>


                    <h3>Transaction History</h3>
                    <?php if ($transactions && count($transactions) > 0): ?>
                        <table class="table table-striped">
                            <thead class="thead-inverse">
                                <tr class="info">
                                    <th>Months</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Paid On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= $transaction->bill_month ?></td>
                                        <td><?= $transaction->bill_amount ?></td>
                                        <td><?= $transaction->Discount ?></td>
                                        <td><?= $transaction->paid_on ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>

                <?php else: ?>
                    <p>Customer not found.</p>
                <?php endif; ?>
			</div>
        </div>
    </div>
</div>

<?php
	include 'includes/footer.php';
?>