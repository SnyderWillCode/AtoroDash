<?php
use MythicalSystems\Api\Api as api;
use MythicalSystems\Api\ResponseHandler as rsp;

api::init();
$header = api::getAuthorizationHeader();

if (!$header == null) {

} else {
    rsp::Unauthorized('No client api key given!');
}