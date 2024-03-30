<?php 
 use MythicalSystems\Api\Api as api;
use MythicalSystems\Api\ResponseHandler as rsp;
api::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['auth']) && !$_POST['auth'] == null) {
        if (isset($_POST['password']) && !$_POST['password'] == null) {
          
        } else {
            rsp::Unauthorized('Please provide a password!');
        }
    } else {
        rsp::Unauthorized('Please provide an auth type (email/username)');
    }
} else {
    rsp::MethodNotAllowed('Please use a post request for this endpoint!');
}