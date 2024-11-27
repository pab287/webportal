<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Upload
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
|
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. For example,
| the database is not connected to automatically since no assumption
| is made regarding whether you intend to use it.  This file lets
| you globally define which systems you would like loaded with every
| request.
|
*/

$config['upload_path'] = './uploads/images/';
$config['allowed_types'] = 'gif|jpg|png|pdf';
$config['max_size']     = '100';
$config['max_width'] = '1024';
$config['max_height'] = '768';