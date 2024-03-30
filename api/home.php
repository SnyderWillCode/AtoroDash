<?php
use MythicalSystems\Api\Api as api;
use MythicalSystems\Api\ResponseHandler as rsp;

api::init();
rsp::OK("Welcome to the main api route");