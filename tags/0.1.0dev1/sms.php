<?php
/**
 * SMS component for CakePHP using the Clickatell HTTP API interface.
 * @author Doug Bromley <doug.bromley@gmail.com>
 * @copyright Doug Bromley
 * @link http://www.cakephp.org CakePHP
 * @link http://www.clickatell.com Clickatell
 *
 ***
 * Copyright (c) 2008, Doug Bromley <doug.bromley@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *  - Redistributions of source code must retain the above copyright notice, 
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright 
 *    notice, this list of conditions and the following disclaimer in 
 *    the documentation and/or other materials provided with the distribution.
 *  - Neither the name of the <ORGANIZATION> nor the names of its 
 *    contributors may be used to endorse or promote products 
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***
 */

/**
 * As with all Cake components we extend the object class.
 */
class SmsComponent extends Object {
  /**
  * The username for the Clickatell API
  * @access public
  * @var string
  */
  public $api_user = null;

  /**
  * The password for the Clickatell API
  * @access public
  * @var string
  */
  public $api_pass = null;

  /**
  * Who will be shown as the sender of the text at the receivers handset.
  * @access public
  * @var string
  */
  public $api_from = null;

  /**
  * The API id for this product.
  * @access public
  * @var string
  */
  public $api_id = null;

  /**
   * The delay in minutes before the message is sent to the reciever.
   * This doesn't affect the speed of the script execution - its a variable 
   * used at the Clickatell end to delay message sending.
   * @access public
   * @var integer
   */
  public $delivery_delay = null;

  /**
   * If the text is delayed then switching this on "1"
   * will cause it to be escalated to and alternative.
   * Could cost more to send the message!
   * @access public
   * @var integer
   */
  public $escalate = 0;

  /**
  * The Clickatell XML API url
  */
  const API_XML_URL = 'http://api.clickatell.com/xml/xml';

  /**
  * The Clickatell HTTP API url for sending GET or POST requests too.
  */
  const API_HTTP_URL = 'http://api.clickatell.com/http/';


  /**
  * Post a message to the Clickatell servers for the number provided
  * @param string $tel The telephone number in international format.  Not inclduing a leading "+" or "00".
  * @param string $message The text message to send to the handset.
  * @return string
  * @see SmsComponent::api_id
  * @see SmsComponent::api_user
  * @see SmsComponent::api_pass
  * @see SmsComponent::api_from
  */
  function postSms($tel, $message) {
    $postdata = http_build_query(
      array(
        'api_id' => $this->api_id,
        'user' => $this->api_user,
        'password' => $this->api_pass,
        'from' => $this->from,
        'to' => $tel,
        'text' => $message,
        'deliv_time' => $this->delivery_delay,
        'escalate' => $this->escalate
      )
    );

    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context  = stream_context_create($opts);
    $response = file_get_contents(self::API_HTTP_URL.'sendmsg', false, $context);
    return $response;
  }

  /**
  * Get the balance of your Clickatell account.
  * @return float
  * @see SmsComponent::api_id
  * @see SmsComponent::api_user
  * @see SmsComponent::api_pass
  * @see SmsComponent::api_from
  */
  function queryBalance() {
    $postdata = http_build_query(
      array(
        'api_id' => $this->api_id,
        'user' => $this->api_user,
        'password' => $this->api_pass
      )
    );

    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context  = stream_context_create($opts);
    $response = file_get_contents(self::API_HTTP_URL.'getbalance', false, $context);
    return $response;
  }

  /**
   * By providing a phone number you can check if that number is covered and a message can be sent
   * to this network/prefix/number.
   * @param string $number The telephone number to check for coverage
   * @return string
   */
  function queryCoverage($number) {
    $postdata = http_build_query(
      array(
        'api_id' => $this->api_id,
        'user' => $this->api_user,
        'password' => $this->api_pass,
        'msisdn' => $number
      )
    );

    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context  = stream_context_create($opts);
    $response = file_get_contents(self::API_HTTP_URL.'routeCoverage.php', false, $context);
    return $response;
  }
}
