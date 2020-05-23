<?php

namespace App\Classes;

use App\Classes\ResponseAction;

class ResponseCheckout {

  public ResponseAction $response;
  public $checkin;
  public $checkout;
  public $amount;
  public $currency;
  public $minutes;

  function setResponse(ResponseAction $response) {
    $this->response = $response;
  }
  function getResponse() {
    return $this->response;
  }

  function setCheckin($checkin) {
    $this->checkin = $checkin;
  }
  function getCheckin() {
    return $this->checkin;
  }

  function setCheckout($checkout) {
    $this->checkout = $checkout;
  }
  function getCheckout() {
    return $this->checkout;
  }

  function setAmount($amount) {
    $this->amount = $amount;
  }
  function getAmount() {
    return $this->amount;
  }

  function setCurrency($currency) {
    $this->currency = $currency;
  }

  function getCurrency() {
    return $this->currency;
  }

  function setMinutes($minutes) {
    $this->minutes = $minutes;
  }
  function getMinutes() {
    return $this->minutes;
  }
}