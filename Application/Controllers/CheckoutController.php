<?php

class CheckoutController extends BaseController
{
    protected $productModel;
    protected $cart;
    protected $orderModel;
    protected $orderDetail;
    protected $banners;
    protected $bannerModel;
    protected $couponModel;
    protected $userModel;
    protected $coupon;
    protected $payos;

    public function __construct()
    {
        $this->loadHelper('CartHelper');
        $this->cart = new CartHelper;
        $this->loadModel('ProductModel');
        $this->productModel = new ProductModel;
        $this->loadModel('OrderModel');
        $this->orderModel = new OrderModel;
        $this->loadModel('OrderDetail');
        $this->orderDetail = new OrderDetail;
        $this->loadModel('BannerModel');
        $this->bannerModel = new BannerModel;
        $this->banners = $this->bannerModel->findBannerBySite('Checkout');
        $this->loadModel('CouponModel');
        $this->couponModel = new CouponModel;
        $this->loadModel('UserModel');
        $this->userModel = new UserModel;

        $this->loadHelper('PayosHelper');
        $this->payos = new PayosHelper();
    }

    public function index()
    {
        $user = !empty($_SESSION['user']) ? $this->userModel->findUserById(['*'], $_SESSION['user']['id']) : null;
        $_SESSION['coupon'] = empty($_SESSION['coupon']) ? null : $_SESSION['coupon'];
        return $this->view('site.checkout.checkout', [
            'cart' => $this->cart,
            'banners' => $this->banners,
            'user' => $user
        ]);
    }

    public function process()
    {
        $data = [
            "fname" => $_POST["fname"],
            "lname" => $_POST["lname"],
            "email" => $_POST["email"],
            "phone" => $_POST["phone"],
            "province" => $_POST["province"],
            "address" => $_POST["address"],
            "note" => $_POST["note"],
            "delivery" => $_POST["delivery"],
            "payment" => $_POST["payment"],
            "account_id" => $_SESSION["user"]["id"],
            "coupon" => $_SESSION["coupon"],

            // tinh total từ frontend
            "total" => $_POST["total"]
        ];

        // store order
        $order = null;
        if (!empty($this->cart)) {
            $order = $this->orderModel->store($data);
            // 2. Luu gio hang vao order detail
            $order["items"] = [];
            foreach ($this->cart->items as $item) {
                print_r($item);
                $detail = [
                    'order_id' => $order["id"],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price_sum']
                ];
                array_push($order["items"], [
                    'name' => $item['name'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (int)($item['price_sum']  * 26000)
                ]);
                $this->orderDetail->store($detail);
            }
        }

        $coupon = $this->couponModel->getCouponDetailById($_SESSION["coupon_id"]);
        $status = ($coupon["used_times"] == 1) ? 0 : 1;
        $used_times = $coupon["used_times"] - 1;

        $coupon_data = [
            'status' => $status,
            'used_times' => $used_times,
            'updated_at' => date("Y-m-d", time())
        ];
        $this->couponModel->updateDataAfterCheckout($coupon["id"], $coupon_data);

        // clear cart and session data
        $this->cart->clear();
        $_SESSION["coupon"] = 0;
        $_SESSION["coupon_id"] = "";

        // thanh toán
        if ($data["payment"] == "Banking") {
            $url = $this->createPaymentUrl($order);

            $payment['payment_link'] = $url;
            $this->orderModel->updateData($order['id'], $payment);

            header("HTTP/1.1 303 See Other");
            header("Location: " . $url);
        } else {
            header('location: ./?controller=customer&action=orderDetail&id=' . $order["id"]);
        }
    }

    public function validate($data)
    {
        $isValid = false;
    }


    public function success()
    {
        $id = $_GET['orderCode'];
        $order["payment_status"] = 1;
        $this->orderModel->updateData($id, $order);
        return $this->view('site.checkout.success');
    }

    public function cancel()
    {
        $id = $_GET['orderCode'];
        $order["status"] = 3;
        $this->orderModel->updateData($id, $order);
        return $this->view('site.checkout.cancel');
    }

    public function createPaymentUrl($data)
    {
        return $this->payos->createPaymentLink($data);
    }
}
