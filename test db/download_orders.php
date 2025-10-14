<?php
session_start();
include "db.php"; 
require('fpdf/fpdf.php'); // Make sure you have FPDF library in fpdf/ folder

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: myorders.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

// Fetch order with user info and product info (FIXED QUERY)
$sql = "SELECT 
            p.*, 
            u.name AS user_name, 
            u.email AS user_email,
            pr.name AS product_name, 
            pr.price AS item_unit_price
        FROM payments p 
        JOIN users u ON p.user_id = u.id
        JOIN products pr ON p.product_id = pr.id -- Product details இணைக்கப்படுகிறது
        WHERE p.user_id='$user_id' AND p.order_id='$order_id' LIMIT 1";
        
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    die("Order not found or access denied.");
}

$order = mysqli_fetch_assoc($result);

// --- PDF Generation ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Header
$pdf->Cell(0,10,'Softlogic Invoice',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,'Order ID: '.$order['order_id'],0,1,'R');
$pdf->Cell(0,6,'Date: '.date('d-m-Y', strtotime($order['order_date'] ?? 'now')),0,1,'R'); // Uses order date if available
$pdf->Ln(10);

// User Info
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,6,'Customer Details:',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,6,'User ID:',0,0);
$pdf->Cell(0,6,$order['user_id'],0,1);
$pdf->Cell(50,6,'Name:',0,0);
$pdf->Cell(0,6,htmlspecialchars($order['user_name']),0,1);
$pdf->Cell(50,6,'Email:',0,0);
$pdf->Cell(0,6,htmlspecialchars($order['user_email']),0,1);
$pdf->Ln(10);

// Product Table Header
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,8,'Product',1,0,'C');
$pdf->Cell(30,8,'Quantity',1,0,'C');
$pdf->Cell(40,8,'Unit Price',1,0,'C');
$pdf->Cell(40,8,'Total',1,1,'C');

// Product Row
$pdf->SetFont('Arial','',12);
// Assuming quantity is 1 since no order_items table is used
$quantity = 1; 
$unit_price = $order['item_unit_price'] ?? $order['total_amount']; // Use dedicated unit price or fall back to total amount

$pdf->Cell(60,8,htmlspecialchars($order['product_name']),1,0);
$pdf->Cell(30,8,$quantity,1,0,'C');
$pdf->Cell(40,8,'₹'.number_format($unit_price, 2),1,0,'R');
$pdf->Cell(40,8,'₹'.number_format($order['total_amount'],2),1,1,'R');

$pdf->Ln(5);

// Payment Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,6,'Payment Method:',0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,htmlspecialchars($order['payment_method']),0,1);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,6,'Status:',0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,htmlspecialchars($order['status']),0,1);
$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Thank you for your order!',0,1,'C');

// Output PDF (D: Force Download)
$pdf->Output('D','Order_'.$order['order_id'].'.pdf');
?>