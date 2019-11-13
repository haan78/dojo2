<?php
require_once  __DIR__ . '/abstracts/AAjax.php';
class ajax extends AAjax {
    public function pass($old,$new) {
        if (!User::setPassword($old,$new)) {
            throw new \WebMethod\WebMethodException(__METHOD__,"Password changing has been failed ");
        }
    }
}