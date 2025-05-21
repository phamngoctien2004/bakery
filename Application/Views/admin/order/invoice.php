<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $order['id'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #f5f5f5;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .customer-info, .order-info {
            flex: 1;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th, .invoice-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
        }
        .totals table {
            width: 300px;
        }
        .totals table td {
            padding: 5px;
        }
        .totals table tr.grand-total {
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .print-button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        @media print {
            .print-button, .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="print-button" onclick="window.print()">In hóa đơn</button>
        <a href="./?module=admin&controller=order&action=detail&id=<?= $order['id'] ?>" style="margin-left: 10px; text-decoration: none;">Quay lại</a>
    </div>
    
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">HÓA ĐƠN</div>
            <div>
                <div style="font-weight: bold; font-size: 18px;">SHOP BÁNH</div>
                <div>Địa chỉ: 123 Đường Nguyễn Văn Linh, Quận 1, TP.HCM</div>
                <div>Điện thoại: (084) 123-4567</div>
                <div>Email: info@shopbanh.com</div>
            </div>
        </div>
        
        <div class="invoice-details">
            <div class="customer-info">
                <h3>Thông tin khách hàng:</h3>
                <div><strong>Tên khách hàng:</strong> <?= $order['fname'] . ' ' . $order['lname'] ?></div>
                <div><strong>Địa chỉ:</strong> <?= $order['address'] ?>, <?= $order['province'] ?></div>
                <div><strong>Email:</strong> <?= $order['email'] ?></div>
                <div><strong>Điện thoại:</strong> <?= $order['phone'] ?></div>
            </div>
            
            <div class="order-info">
                <h3>Thông tin đơn hàng:</h3>
                <div><strong>Mã đơn hàng:</strong> #<?= $order['id'] ?></div>
                <div><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                <div><strong>Phương thức thanh toán:</strong> <?= $order['payment'] ?></div>
                <div><strong>Phương thức vận chuyển:</strong> <?= $order['delivery'] ?></div>
                <div><strong>Trạng thái:</strong> 
                    <?php
                    if ($order['status'] == 0) echo 'Đã giao hàng';
                    else if ($order['status'] == 1) echo 'Đang chờ xử lý';
                    else if ($order['status'] == 2) echo 'Đang giao hàng';
                    else echo 'Đã hủy';
                    ?>
                </div>
            </div>
        </div>
        
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products_in_order as $item): ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>$<?= number_format($item['price_sum'] / $item['quantity'], 2, '.', '') ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price_sum'], 2, '.', '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="totals">
            <table>
                <tr>
                    <td>Tổng tiền hàng:</td>
                    <td>$<?= number_format($order['total'], 2, '.', '') ?></td>
                </tr>
                <?php if ($order['coupon'] != 0): ?>
                <tr>
                    <td>Giảm giá:</td>
                    <td>- $<?= number_format($order['total'] * ($order['coupon']), 2, '.', '') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Phí vận chuyển:</td>
                    <td>$2.00</td>
                </tr>
                <tr class="grand-total">
                    <td>Tổng thanh toán:</td>
                    <td>$<?= number_format($order['total'] * (1 - $order['coupon']) + 2, 2, '.', '') ?></td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p>Cảm ơn bạn đã mua hàng tại Shop Bánh!</p>
            <p>Mọi thắc mắc vui lòng liên hệ: info@shopbanh.com hoặc hotline: (084) 123-4567</p>
        </div>
    </div>
</body>
</html>
