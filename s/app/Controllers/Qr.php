<?php

namespace App\Controllers;

require_once APPPATH . 'Libraries/phpqrcode/qrlib.php';

class Qr extends BaseController
{
    public function url($data = null)
    {
        $data = site_url($data) ?? site_url();

        header('Content-Type: image/png');
        \QRcode::png($data, false, QR_ECLEVEL_L, 6);
        exit;
    }
}
