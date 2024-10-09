<?php 
  require ("fpdf/fpdf.php");
  require ("word.php");
  require "config.php"; 

  //customer and invoice details
  $info=[
    "customer"=>"",
    "address"=>",",
    "city"=>"",
    "invoice_no"=>"",
    "invoice_date"=>"",
    "payment"=>"",
    "total_amt"=>"",
    "words"=>"",
  ];
  
  //Select Invoice Details From Database
  $sql="select * from invoice where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  $row=$res->fetch_assoc();
	  
	  $obj=new IndianCurrency($row["GRAND_TOTAL"]);
	 

	  $info=[
		"customer"=>$row["CNAME"],
		"address"=>$row["CADDRESS"],
		"city"=>$row["CCITY"],
		"invoice_no"=>$row["INVOICE_NO"],
		"invoice_date"=>date("d-m-Y",strtotime($row["INVOICE_DATE"])),
    "payment"=>$row["PAYMENT"],
		"total_amt"=>$row["GRAND_TOTAL"],
		"words"=> $obj->get_words(),
	  ];
  }
  
  //invoice Products
  $products_info=[];
  
  //Select Invoice Product Details From Database
  $sql="select * from invoice_products where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  while($row=$res->fetch_assoc()){
		   $products_info[]=[
			"name"=>$row["PNAME"],
			"price"=>$row["PRICE"],
      "payment"=>$row["PAYMENT"],
			"qty"=>$row["QTY"],
			"total"=>$row["TOTAL"],
		   ];
	  }
  }
  
  class PDF extends FPDF
  {
    function Header(){
      
      //Display Company Info
      $this->Image('IG LOGO.png',80,-8,50);
      $this->SetFont('Arial','B',12);
      $this->SetY(16);
      $this->SetX(10);
      $this->Cell(20,4,"Sales Invoice",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(20,4,"Republic of the Philippines",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(20,4,"Villasis, Santiago City",0,1);
      
      //Display Horizontal line
      $this->Line(0,30,220,30);

     
      //Display Company Info
      $this->Image('IG LOGO.png',80,162,50);
      $this->SetFont('Arial','B',12);
      $this->SetY(186);
      $this->SetX(10);
      $this->Cell(-120,4,"Sales Invoice",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(20,4,"Republic of the Philippines",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(20,4,"Villasis, Santiago City",0,1,);
      
      //Display Horizontal line
      $this->Line(0,200,400,200);
      $this->Line(0,160,400,160);
    }
    
    function body($info,$products_info){

      $this->SetY(222);
      $this->SetX(10);
      $this->SetFont('Arial','B',10);
      $this->Cell(80,5,"Particulars",1,0,"C");
      $this->Cell(40,5,"Amount",1,0,"C");
      $this->Cell(40,5,"Payment",1,0,"C");
      $this->Cell(40,5,"",2,1,"C");

      foreach($products_info as $row){
        $this->Cell(80,5,$row["name"],"LR",0,"C");
        $this->Cell(40,5,$row["price"],"R",0,"C");
        $this->Cell(40,5,$row["payment"],"R",0,"C");
        $this->Cell(400,5,$row["total"],"R",1,"C");
      }

      for($i=0;$i<12-count($products_info);$i++)
      {
        $this->Cell(80,5,"","LR",0);
        $this->Cell(40,5,"","R",0,"R");
        $this->Cell(40,5,"","R",0,"R");
        $this->Cell(100,5,"","R",1,"R");
      }
      
      //Billing Details
      $this->SetY(30);
      $this->SetX(10);
      $this->SetFont('Arial','',10);
      $this->Cell(-120,7,"OR Type / Particulars : ".$info["address"],0,1);
      $this->Cell(30,7,"Project Details : ".$info["city"],0,1);
      $this->Cell(30,7,"Payor : ".$info["customer"],0,1);
      $this->Cell(195,-10,"".$info["payment"],0,1,"R");
      $this->Cell(195,-15,"Date of Receipt : ".$info["invoice_date"],0,1,"R");
      $this->Cell(200,-15,"OR Number : ".$info["invoice_no"],0,1,"R");

       //Billing Details
       $this->SetY(200);
       $this->SetX(10);
       $this->SetFont('Arial','',10);
       $this->Cell(-120,7,"OR Type / Particulars : ".$info["address"],0,1);
       $this->Cell(30,7,"Project Details : ".$info["city"],0,1);
       $this->Cell(30,7,"Payor : ".$info["customer"],0,1);
       $this->Cell(195,-10,"".$info["payment"],0,1,"R");
      $this->Cell(195,-15,"Date of Receipt : ".$info["invoice_date"],0,1,"R");
       $this->Cell(195,-15,"OR Number : ".$info["invoice_no"],0,1,"R");

      //Display Table headings
      $this->SetY(52);
      $this->SetX(10);
      $this->SetFont('Arial','B',10);
      $this->Cell(80,5,"Particulars",1,0,"C");
      $this->Cell(40,5,"Amount",1,0,"C");
      $this->Cell(40,5,"Payment",1,0,"C");
      $this->Cell(40,5,"",2,1,"C");
      
      
      
      //Display table product rows
      foreach($products_info as $row){
        $this->Cell(80,6,$row["name"],"LR",0,"C");
        $this->Cell(40,6,$row["price"],"R",0,"C");
        $this->Cell(40,5,$row["payment"],"R",0,"C");
        $this->Cell(400,6,$row["total"],"R",1,"C");
      }
      //Display table empty rows
      for($i=0;$i<12-count($products_info);$i++)
      {
        $this->Cell(80,5,"","LR",0);
        $this->Cell(40,5,"","R",0,"R");
        $this->Cell(40,5,"","R",0,"R");
        $this->Cell(100,5,"","R",1,"R");
      }
      //Display table total row
      $this->SetFont('Arial','B',10);
      $this->Cell(120,9,"Total Amount",1,0,"C");
      $this->Cell(40,9,$info["total_amt"],1,1,"C");
      
      //Display amount in words
      $this->SetY(127);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,9,"Amount in Words ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(0,9,$info["words"],0,1);

       //Display table1 total row
       $this->SetY(287);
      $this->SetX(10);
       $this->SetFont('Arial','B',10);
       $this->Cell(120,9,"Total Amount",1,0,"C");
       $this->Cell(40,9,$info["total_amt"],1,1,"C");
       
       //Display amount1 in words
      $this->SetY(297);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,9,"Amount in Words ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(0,9,$info["words"],0,1);
      
    }
    function Footer(){
      
      //set footer position
      $this->SetY(-240);
      $this->Ln(15);
      $this->SetFont('Arial','',10);
      $this->Cell(150,9,"By :",0,1,"R");
      $this->Cell(207,-3,"______________________________",0,1,"R");
      $this->Cell(0,12,"Authorized Signature",0,1,"R");
      $this->SetFont('Arial','',10);

      //set footer1 position
      $this->SetY(286);
      $this->Ln(15);
      $this->SetFont('Arial','',10);
      $this->Cell(150,9,"By :",0,1,"R");
      $this->Cell(207,-3,"______________________________",0,1,"R");
      $this->Cell(0,12,"Authorized Signature",0,1,"R");
      $this->SetFont('Arial','',10);
      
    }
  }
  //Create A4 Page with Portrait 
  $pdf=new PDF("P","mm","Legal");
  $pdf->AddPage();
  $pdf->body($info,$products_info);
  $pdf->Output();
?>

