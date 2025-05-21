<?php
if (!empty($_SESSION['user'])) 
{

    $user_session = $_SESSION['user']; ?>
    <span class='welcome'>Xin chào! <?= $user_session['fname'] ?? '' ?> <?= $user_session['lname'] ?? '' ?></span>

    <?php if ($user_session['role'] == 'admin') : ?>
        <a href='?module=admin&controller=customer&controller=dashboard'>
            <button class='login-modal' style='width:auto'><i class="fas fa-user-cog" style='font-size: 14px'></i>Tới Trang Admin</button>
        </a>
    <?php endif; ?>



    <a href='?controller=customer&action=viewProfile'> 
        <button class='login-modal' style='width:auto'><i class='fas fa-user-edit' style='font-size: 14px'></i>Profile</button>
    </a>
    <a href='?controller=customer&action=listOrders'>
        <button class='login-modal' style='width:auto'><i class='fas fa-receipt' style='font-size: 14px'></i>Lịch Sử Đơn Hàng</button>
    </a>
    <a href='?controller=verify&action=logout'>
        <button class='login-modal' style='width:auto'><i class='fas fa-sign-out-alt' style='font-size: 14px'></i>Đăng Xuất</button>
    </a>
<?php } else { ?>
    <button class='login-modal' onclick="document.getElementById('id01').style.display='block'" style='width:auto'><i class='fas fa-sign-in-alt' style='font-size: 14px'></i>Đăng Nhập</button>
<?php } ?>

<div id="id01" class="modal">

    <form action="?controller=verify&action=login" class="modal-content animate" method="post" name="loginForm" onsubmit="return validateLoginForm();">

        <div class="row">

            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                <div class="img-container">
                    <img src="./public/site/img/login-signup/login-pic.png" alt="Avatar" class="avatar">
                </div>
            </div>


            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                <div class="main-form-content">
                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>

                    <h5 class="text-center">Đăng Nhập </h5>

                    <label for="email"></label>
                    <input type="text" placeholder="Email address *" name="email" id="li-email" onkeyup="validateEmail(this)">
                    <small id="li-email-err"></small>


                    <label for="psw"></label>
                    <input type="password" placeholder="Password *" name="password" id="li-psw" onkeyup="validateNotEmpty(this, 'Password')">
                    <small id="li-psw-err"></small>

                    <div class="remember-forgot-row">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Nhớ mật khẩu</label>
                        </div>
                        <div class="forgot-password">
                            <a href="?controller=verify&action=forgotPassword">Quên mật khẩu?</a>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">Tiếp tục</button>

                    <div class="signup-suggest">
                        <span>Chưa có tài khoản?. </span>
                        <span onclick="document.getElementById('id01').style.display='none'; document.getElementById('id02').style.display='block'">
                            Đăng ký ngay</span>
                    </div>
                </div>
            </div>

        </div>





    </form>
</div>

<script>
    var modal = document.getElementById('id01');
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
