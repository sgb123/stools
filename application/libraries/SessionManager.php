<?php
/**
 * Manages session information.
 *
 * PHP version 5
 *
 * Copyright 2011, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsAdWords
 * @subpackage webapp
 * @category   WebServices
 * @copyright  2011, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Eric Koleda <api.ekoleda@gmail.com>
 */

error_reporting(E_STRICT | E_ALL);

require_once 'Google/Api/Ads/AdWords/Lib/AdWordsUser.php';

/**
 * Manages session information.
 */
class SessionManager {
  /**
   * The SessionManager class is not meant to have any instances.
   * @access private
   */
  private function __construct() {}

  /**
   * Sets the AdWordsUser in the session.
   * @param AdWordsUser $user the user to set in the session
   */
  public static function SetAdWordsUser(AdWordsUser $user) {
    session_start();
    $_SESSION['AW_USER'] = $user;
    session_write_close();
    setcookie('AW_SESSION_ACTIVE', 'true');
  }

  /**
   * Gets the AdWordsUser saved in the session.
   * @return AdWordsUser the current user
   */
  public static function GetAdWordsUser() {
    $user = NULL;
    session_start();
    if (isset($_SESSION['AW_USER'])) {
      $user = $_SESSION['AW_USER'];
    }
    session_write_close();
    if (!isset($user)) {
      throw new Exception('Session expired.');
    }
    return $user;
  }

  /**
   * Removes the AdWordsUser from the session.
   */
  public static function RemoveAdWordsUser() {
    session_start();
    $_SESSION['AW_USER'] = NULL;
    session_write_close();
    setcookie('AW_SESSION_ACTIVE', 'false');
  }
}
