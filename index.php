<?php require "config.php"; ?>
<html>
	<head>
		<title>Law Office Receipt</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		
		<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
		<script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js" integrity="sha256-R6eRO29lbCyPGfninb/kjIXeRjMOqY3VWPVk6gMhREk=" crossorigin="anonymous"></script>
		
	</head>
	<body>
		<div class='container pt-5'>
			<h1 class='text-center text-primary'>Law Office Receipt</h1><hr>
			<?php
				if(isset($_POST["submit"])){
					$invoice_no=$_POST["invoice_no"];
					$invoice_date=date("Y-m-d",strtotime($_POST["invoice_date"]));
					$cname=mysqli_real_escape_string($con,$_POST["cname"]);
					$caddress=mysqli_real_escape_string($con,$_POST["caddress"]);
					$ccity=mysqli_real_escape_string($con,$_POST["ccity"]);
					$grand_total=mysqli_real_escape_string($con,$_POST["grand_total"]);
					
					$sql="insert into invoice (INVOICE_NO,INVOICE_DATE,CNAME,CADDRESS,CCITY,GRAND_TOTAL) values ('{$invoice_no}','{$invoice_date}','{$cname}','{$caddress}','{$ccity}','{$grand_total}') ";
					if($con->query($sql)){
						$sid=$con->insert_id;
						
						$sql2="insert into invoice_products (SID,PNAME,PRICE,PAYMENT,TOTAL) values ";
						$rows=[];
						for($i=0;$i<count($_POST["pname"]);$i++)
						{
							$pname=mysqli_real_escape_string($con,$_POST["pname"][$i]);
							$price=mysqli_real_escape_string($con,$_POST["price"][$i]);
							$payment=mysqli_real_escape_string($con,$_POST["payment"][$i]);
							$total=mysqli_real_escape_string($con,$_POST["total"][$i]);
							$rows[]="('{$sid}','{$pname}','{$price}','{$payment}','{$total}')";
						}
						$sql2.=implode(",",$rows);
						if($con->query($sql2)){
							echo "<div class='alert alert-success'>Invoice Added. <a href='print.php?id={$sid}' target='_BLANK'>Click</a> here to Print Invoice</div>";
						}else{
							echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
						}
					}else{
						echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
					}
				}
				
			?>
			<form method='post' action='index.php' autocomplete='off'>
				<div class='row'>
					<div class='col-md-4'>
						<h5 class='text-success'>Receipt Details</h5>
						<div class='form-group'>
							<label>OR Number</label>
							<input type='text' name='invoice_no' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>Date of Receipt</label>
							<input type='text' name='invoice_date' id='date' required class='form-control'>
						</div>
					</div>
					<div class='col-md-8'>
						<h5 class='text-success'>Customer Details</h5>
						<div class='form-group'>
							<label>Payor</label>
							<input type='text' name='cname' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>OR Type</label>
							<input type='text' name='caddress' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>Account Details</label>
							<input type='text' name='ccity' required class='form-control'>
						</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12'>
						<table class='table table-bordered'>
							<thead>
								<tr>
									<th>Particulars</th>
									<th>Amount</th>
									<th>Full/Partial</th>
									<th>Total</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id='product_tbody'>
								<tr>
									<td><input type='text' required name='pname[]' class='form-control'></td>
									<td><input type='text' required name='price[]' class='form-control price'></td>
									<td><input type='text' required name='payment[]' class='form-control payment'></td>
									<td><input type='text' required name='total[]' class='form-control total'></td>
									<td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td><input type='button' value='+ Add Row' class='btn btn-primary btn-sm' id='btn-add-row'></td>
									<td colspan='2' class='text-right'>Total</td>
									<td><input type='text' name='grand_total' id='grand_total' class='form-control' required></td>
								</tr>
							</tfoot>
						</table>
						<input type='submit' name='submit' value='Save Receipt' class='btn btn-success float-right'>
					</div>
				</div>
			</form>
		</div>
		<script>
			$(document).ready(function(){
				$("#date").datepicker({
					dateFormat:"dd-mm-yy"
				});
				
				$("#btn-add-row").click(function(){
					var row="<tr> <td><input type='text' required name='pname[]' class='form-control'></td> <td><input type='text' required name='price[]' class='form-control price'></td> <td><input type='text' required name='payment[]' class='form-control payment'></td> <td><input type='text' required name='total[]' class='form-control total'></td> <td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td> </tr>";
					$("#product_tbody").append(row);
				});
				
				$("body").on("click",".btn-row-remove",function(){
					if(confirm("Are You Sure?")){
						$(this).closest("tr").remove();
						grand_total();
					}
				});

				$("body").on("keyup",".price",function(){
					var price=Number($(this).val());
					$(this).closest("tr").find(".total").val(price);
					grand_total();
				});
				
				$("body").on("keyup",".price",function(){
					var qty=Number($(this).val());
					var price=Number($(this).closest("tr").find(".price").val());
					$(this).closest("tr").find(".total").val(price);
					grand_total();
				});			
				
				function grand_total(){
					var tot=0;
					$(".total").each(function(){
						tot+=Number($(this).val());
					});
					$("#grand_total").val(tot);
				}
			});
		</script>
	</body>
</html>