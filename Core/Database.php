<?php

// mục tiêu của File này là để sử lý database thực hiện các công việc như connect
class Database
{

    //deploy to  heroku app
    // const HOST = 'remotemysql.com';

    // const USERNAME = 'BCVZPDJiJg';

    // const PASSWORD = 'nvQkXgeqke';

    // const DB_NAME = 'BCVZPDJiJg';

    const HOST = 'yamanote.proxy.rlwy.net:25773';

    const USERNAME = 'root';

    const PASSWORD = 'CBGkKPKcismyCESauKwVLwdmSNCysdSw';

    const DB_NAME = 'railway';


    public function connect()
    {
        $connect = mysqli_connect(self::HOST, self::USERNAME, self::PASSWORD, self::DB_NAME);

        mysqli_set_charset($connect, "utf8");

        if (mysqli_connect_errno() === 0) {
            return $connect;
        }
        return false;
    }
}
