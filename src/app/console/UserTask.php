<?php

// declare(strict_types=1);

namespace App\Console;

use Firebase\JWT\JWT;
use Orders;
use Phalcon\Cli\Task;
use Products;
use Settings;

class UserTask extends Task
{
    public function userAction()
    {
        echo 'This is the User task and the default action' . PHP_EOL;
    }
    public function removeLogAction()
    {
        $b = scandir(APP_PATH . "/logs", 1);

        foreach ($b as $key => $value) {
            if ($value != ".." && $value != ".") {

                unlink(APP_PATH . "/logs/$value");
            }
        }
        echo "All Log Files Deleted";
    }

    public function createTokenAction($role)
    {
        if ($role == 'admin') {
            $key = "example_key";
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "role" => $role
            );
            $jwt = JWT::encode($payload, $key, 'HS256');

            echo $jwt;
        }
    }

    public function defaultSettingsAction($price, $stock)
    {
        $settingsObj =  Settings::findFirst(1);
        // print_r($settingsObj->default_price);die;
        $settingsObj->default_price = $price;
        $settingsObj->default_stock = $stock;
        $success = $settingsObj->update();
        echo "Settings Updated";
    }

    public function countProductsAction()
    {
        $productCount =  Products::count([
            'conditions' => 'stock < :stock:',
            'bind' => [
                'stock' => 10,
            ]
        ]);
        echo "count = " . $productCount;
    }

    public function removeACLAction()
    {
        unlink(APP_PATH . "/security/acl.cache");

        echo "ACL Cache File Deleted";
    }

    public function fetchOrdersAction() {
        $current_date = Date('y-m-d');
        $order =  Orders::findFirst([
            'conditions'=>'date=:current_date:',
            'bind'=>[
                'current_date'=>$current_date
            ],
            'order' => 'date DESC'
        ]);
        
        echo "Order ID = ".$order->id;
        echo "Order Customer Name =".$order->customer_name;
        echo "Zipcode =".$order->zipcode;
        echo "Product = ".$order->product;
        echo "Order Quantity =".$order->quantity;
    }
}
