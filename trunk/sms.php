<?php
/**
 * SMS component for CakePHP using the Clickatell HTTP API interface.
 *
 * @author Doug Bromley <doug.bromley@gmail.com>
 * @copyright Doug Bromley
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class SmsComponent extends Object {
  /**
  * The username for the Clickatell API
  * @access public
  * @var string
  */
  var $api_user = null;

  /**
  * The password for the Clickatell API
  * @access public
  * @var string
  */
  var $api_pass = null;

  /**
  * Who will be shown as the sender of the text at the receivers handset.
  * @access public
  * @var string
  */
  var $api_from = null;

  /**
  * The API id for this product.
  * @access public
  * @var string
  */
  var $api_id = null;


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
  */
  function postSms($tel, $message) {
    $postdata = http_build_query(
      array(
        'api_id' => $this->api_id,
        'user' => $this->api_user,
        'password' => $this->api_pass,
        'from' => $this->from,
        'to' => $tel,
        'text' => $message
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
}
