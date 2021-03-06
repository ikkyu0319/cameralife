<?php

$features=array('database','theme','security');
require '../../../main.inc';
$cameralife->base_url = dirname(dirname(dirname($cameralife->base_url)));

require 'lightopenid/openid.php';

try {
    # Mewp told me specifically not to use SERVER_NAME.
    # Change 'localhost' to your domain name.

    $openid = new LightOpenID($_SERVER['SERVER_NAME']);
    if (!$openid->mode) {
        if (isset($_POST['openid_identifier'])) {
            $openid->identity = $_POST['openid_identifier'];
            # The following two lines request email, full name, and a nickname
            # from the provider. Remove them if you don't need that data.
            $openid->required = array('contact/email');
            $openid->optional = array('namePerson', 'namePerson/friendly');
            header('Location: ' . $openid->authUrl());
        }
    } elseif ($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        $id = "";
        $email = "";
        if ($openid->validate()) {
            $id = $openid->identity;
            $attr = $openid->getAttributes();
            $email = $attr['contact/email'];
            if (strlen($email)) {
                $cameralife->Security->Login($id, $email);
                header ('Location: '.$cameralife->base_url.'/index.php');
            } else {
                die ('Enough detail (email address) was not provided to process your login.');
            }
        } else {
            die ('Provider did not validate your login');
        }
    }
} catch (ErrorException $e) {
    echo $e->getMessage();
}
