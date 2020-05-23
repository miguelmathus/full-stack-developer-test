<?php

namespace App\Classes;

class ResponseAction {

  public $status;
  public $message;

  function __construct($status,$message) {
    $this->status = $status;
    $this->message = $message;
  }

  function setStatus($status) {
    $this->status = $status;
  }
  function getStatus() {
    return $this->status;
  }

  function setMessage($message) {
    $this->message = $message;
  }
  function getMessage() {
    return $this->message;
  }


}