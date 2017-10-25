<?php

class staticFunc {

	public static function getAllUsers($pdo) {
		$sql = "SELECT matches.payer, users.* FROM matches inner join users ON matches.payer = users.id and matches.status > 0 order by deadline asc";
		$allUsers = $pdo->query($sql)->fetchAll();
		if (count($allUsers) == 0) {
			echo 'No Eligible User To Match Yet!';			
		} else {
			echo '<select name="match_to_pay" class="form-control">';
			foreach ( $allUsers as $user ) {
				echo '<option value="'. $user['payer'] . '">'. $user['name'] . '</option>';			
			}
			echo '</select>';
			echo '<button type="submit" name="submit_match" class="btn btn-info margin-left-sm">Match Now</button>';
		}
	}
	
	public static function checkEmail($pdo, $email) {
		$sql = 'select * FROM users where email = :email';
		$stmt = $pdo->prepare($sql);
		$stmt->execute(['email' => $email]);
		if ($stmt->rowCount() > 0 ) {
			return true;
		}
		return false;
	}
	
	public static function act_code($pdo) {
		do {
			$code = mt_rand(100000, 999999);
			$sql = 'select ref_code FROM users where ref_code = :ref_code';
			$stmt = $pdo->prepare($sql);
			$stmt->execute(['ref_code' => $code]);
		} while($stmt->rowCount() > 0);
		return $code;
	}
	
	public static function generateRefCode($pdo) {
		do {
			$length = 20;
			$act_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			$sql = 'select activation_code FROM users where activation_code= :act_code';
			$stmt = $pdo->prepare($sql);
			$stmt->execute(['act_code' => $act_code]);
		} while($stmt->rowCount() > 0);
		return $act_code;
	}
	
	public static function getReferrer($pdo, $ref_code) {
		$sql = 'select ref_code FROM users where ref_code= :ref_code LIMIT 1';
		$stmt = $pdo->prepare($sql);
		$stmt->execute(['ref_code' => $ref_code]);
		if ($stmt->rowCount() > 0) {
			return $stmt->fetchColumn();
		} else {	
			return null;
		}
	}
	
	public static function shortenID ( $ID ) {
		//Shortening Id to be saved in Database for Reminders
		return substr($ID, 0, 10);
	}

	public static function compareShortenedID ( $list, $all, $user, $pdo, $display = NULL ) {
		$splitList = explode(',', $list);
		foreach ($all as $key => $value) {
			$allAdmin[$key + 1] = $value['staff_id'];
			$adminIds[$key + 1] = self::shortenID($value['staff_id']);
		}
		for ($i = 0; $i < count($splitList); $i++ ) {
			$check = array_search($splitList[$i], $adminIds);
			if ($check) {
				if (isset($display)) {
					$getAdminDetails[] = $allAdmin[$check];
				} else {
					$getAdminDetails[] = $user->getData (get_class($user), $allAdmin[$check], $pdo);
				}
			}
		}
		return $getAdminDetails;
	}

	public static function maskURLParam ( $value ) {
		return urlencode(base64_encode($value));
	}

	public static function unmaskURLParam ( $value ) {
		return base64_decode(urldecode($value));
	}

	public static function dateValidator ( $value ) {
		$date = DateTime::createFromFormat("Y-m-d", $value);
		$check = DateTime::getLastErrors();
		if (!empty($check['errors']) || !empty($check['warnings'])) {
			return NULL;
		} else {
			return $date->format('Y-m-d');
		}
	}

	private static function confirmPreviousPass ( $pdo, $newPassword ) {
		//Get Current Password
		$sql = "SELECT hash_pass FROM login_tbl WHERE user_id = :userId";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':userId' => $_SESSION['confirmedResetId'] ]);
		$currentPassword = $stmt->fetchColumn();
		//Confirm if New Password is same as current password
		if ($confirmCurrent = password_verify($newPassword,$currentPassword)) {
			return 'exist';
		}
		//Get Record of All Previous Passwords
		$sql = "SELECT passwords FROM passwords_tbl WHERE user_id = :userId";
		$stmt = $pdo->prepare($sql);
		$userId = (isset($_SESSION['confirmedResetId'])) ? $_SESSION['confirmedResetId'] : $_SESSION['userId'];
		$stmt->execute([':userId' => $userId ]);
		$confirmResult = $stmt->fetchColumn();
		if ($confirmResult) {
			$retrievedPasswords = explode(' , ', $confirmResult, 5);
			foreach ($retrievedPasswords as $value) {
				$confirmPass = password_verify($newPassword, $value);
				if ($confirmPass) {
					$exist = 1;
				}
			}
			if (isset($exist)) {
				return 'exist';
			} else {
				$newPasswordList = $confirmResult .' , '. $currentPassword;
				$sql = "UPDATE passwords_tbl SET passwords = :newList WHERE user_id = :userId";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([':newList' => $newPasswordList, ':userId' => $_SESSION['confirmedResetId'] ]);
				if ($stmt->rowCount()) {
					return NULL;
				}
			}
		} else {
			$sql = "INSERT into passwords_tbl VALUES (:userId, :currentPassword)";
			$stmt= $pdo->prepare($sql);
			$stmt->execute([':userId' => $_SESSION['confirmedResetId'], ':currentPassword' => $currentPassword]);
			if ($stmt->rowCount()) {
				return NULL;
			}
		}
	}
	
	public static function formatDateTime ( $dateTime ) {
		//$dateTime in format: YYYY-MM-DD HH:MM:SS
		$seperateAll = explode(' ', $dateTime, 2);
		$seperateDate = explode('-', $seperateAll[0]);
		$dateFormat = self::formatDate($seperateDate[2]).' '.self::formatMonth($seperateDate[1]).', '. $seperateDate[0];
		$timeFormat = self::formatTime($seperateAll[1]);
		return $dateFormat.' <b>|</b> '.$timeFormat;
	}

	public static function formatDate ( $value ) {
		if ($value == 1 || $value == 21 || $value == 31) {
			return $value.'st';
		} elseif ( $value == 2 || $value == 22) {
			return $value.'nd';
		} elseif ( $value == 3 || $value == 23) {
			return $value.'rd';
		} else {
			return $value.'th';
		}
	}
	
	public static function formatMonth ( $value ) {
		if ($value == 1) {
			return 'January';
		} elseif ($value == 2) {
			return 'February';
		} elseif ($value == 3) {
			return 'March';
		} elseif ($value == 4) {
			return 'April';
		} elseif ($value == 5) {
			return 'May';
		} elseif ($value == 6) {
			return 'June';
		} elseif ($value == 7) {
			return 'July';
		} elseif ($value == 8) {
			return 'August';
		} elseif ($value == 9) {
			return 'September';
		} elseif ($value == 10) {
			return 'October';
		} elseif ($value == 11) {
			return 'November';
		} elseif ($value == 12) {
			return 'December';			
		}
	}

	public static function formatTime ( $timeValue ) {
		//timeValue is sent in format HH:MM:SS
		$seperate = explode(':', $timeValue, 3);
		return self::timeAMPM($seperate[0]);
	}

	public static function timeAMPM ( $number ) {
		if ( $number >= 12 ) {
			$time = ($number == 12) ? $number : $number - 12;
			$amOrPm = 'PM';
			if ($number == 24) {
				$amOrPm = 'MIDNIGHT';
			} elseif ($number == 12) {
				$amOrPm = 'NOON';
			}
		} else {
			$time = $number; $amOrPm = 'AM';
		}
		return $time.' '.$amOrPm;	
	}

	private static function confirmEmail ( $pdo, $email, $type ) {
		if ($type == 1) {
			$dbTbl = 'staff_tbl';
			$dbId = 'staff_id';
		} elseif ($type == 2) {
			$dbTbl = 'cust_tbl';
			$dbId = 'cust_id';
		} elseif ($type == 3) {
			$dbTbl = 'student_tbl';
			$dbId = 'student_id';
		}
		$sql = "SELECT email, security_question, $dbId as userId FROM $dbTbl WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $email]);
		return $stmt->fetch();
	}
	
	private static function confirmResetDetails ( $pdo, $username, $phone, $birthday, $question ) {
		$type = $_SESSION['setUserType'];
		$getUserId = $_SESSION['getUserId'];
		if ($type == 1) {
			$dbTbl = 'staff_tbl';
			$dbId = 'staff_id';
		} elseif ($type == 2) {
			$dbTbl = 'cust_tbl';
			$dbId = 'cust_id';
		} elseif ($type == 3) {
			$dbTbl = 'student_tbl';
			$dbId = 'student_id';
		}
		$sql = "SELECT login_tbl.username, login_tbl.user_id as returnedId FROM login_tbl INNER JOIN $dbTbl ON login_tbl.username = :username AND login_tbl.user_id = $dbTbl.$dbId AND login_tbl.user_id = :userId AND $dbTbl.phone = :phone AND $dbTbl.birthday = :birthday AND $dbTbl.security_answer = :answer";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':username' => $username, ':userId' => $getUserId, ':phone' => $phone, ':birthday' => $birthday, ':answer' => $question ]);
		return $stmt->fetch();
	}
	
	public static function returnAccountType ( $value ) {
		if ($value == 1) {
			return 'Admin';
		} elseif ($value == 2) {
			return 'Customer';
		} elseif ($value == 3) {
			return 'Student';
		} else {
			return 'Account Error!';
		}
	}
	
	public static function checkLoggedIn() {
		if (! isset($_SESSION['user_id'])) {
			self::redirect('../signin.php');
		}
	}
	
	public static function checkAdminRole() {
		if (! isset($_SESSION['role'])) {
			if ($_SESSION['role'] > 2) {
				self::redirect('../signin.php');
			}
		}
	}
	
	public static function errorPage ( $type ) {
		if ( $type == 'restricted' ) {
			$type = 'error';
			$msg = 'You Do Not Have The Permissions Required To View This Page';
			$link = 'index.php';
			$linkValue = 'Back To Home';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$link = basename($_SERVER['HTTP_REFERER']);
				$linkValue = 'Go Back';
			}
			self::alertDisplay ( $type, $msg, $link, $linkValue, 1 );
		} else {
			$type = 'error';
			$msg = 'The Page You Are Trying to Access Doesn\'t Exist';
			$link = 'index.php';
			$linkValue = 'Back To Home';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$link = basename($_SERVER['HTTP_REFERER']);
				$linkValue = 'Go Back';
			}
			self::alertDisplay ( $type, $msg, $link, $linkValue, 1 );
		}
	}
	/**
	*	Method redirect redirects to a particular page
	*	@param location url to which redirect targets
	*	@return void
	*/
	public static function redirect( $location = NULL ) {		
		if ($location != NULL) {
			$_POST = array();
			$_FILES = array();
			header("Location: {$location}", TRUE, 302);
			exit;
		}
	}
	
	public static function setMessage($key, $value) {
		if (! is_null($key) && ! is_null($value)) {
			$_SESSION['message'][$key] = $value;
		}
		return true;
	}
	
	public static function checkMessage($key) {
		if (! is_null($key) && isset($_SESSION['message'])) {
			if (array_key_exists($key, $_SESSION['message'])) {
				return true;
			}
			return false;
		}
		return false;
	}
	
	public static function getMessage($key) {
		if (! is_null($key)) {
			if (array_key_exists($key, $_SESSION['message'])) {
				if (count($_SESSION['message'][$key]) == 1) {
					$value = $_SESSION['message'][$key];
				} else {
					$value = '';
					foreach ($_SESSION['message'][$key] as $message) {
						$value .= $message . '<br />';
					}
				}
				unset($_SESSION['message'][$key]);
				return $value;
			}
		}
	}
	
	public static function endSession ( $pdo, $loginFail = NULL ) {
		//Find Session
		if (!isset( $_SESSION )) {
			session_start();
		}
		//Unset all the session variables
		$_SESSION = array();
		
		//Destroy the session cookie
		if(isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-100000, '/');
		}
		
		if ($loginFail !== NULL ) {
			//Destroy the session; Cannot Destroy uninitialized session
			session_destroy();
		}
		
		//unset the connection
		$pdo = "NULL";
	}
	
	public static function gradeColorCode ( $gradeValue ) {
		if ( $gradeValue >= 70 ) {
			return "text-success";
		} elseif ( $gradeValue < 70 && $gradeValue >= 60 ) {
			return "text-info";
		} elseif ( $gradeValue < 60 && $gradeValue >= 50 ) {
			return "text-normal";
		} elseif ( $gradeValue < 50 && $gradeValue >= 45 ) {
			return "text-warn";
		} elseif ( $gradeValue < 45 && $gradeValue >= 40 ) {
			return "text-warn";
		} elseif ( $gradeValue < 40 ) {
			return "text-danger";		
		}
	}
	
	public static function displayAlert($type, $msg) {
		echo '<div class="row">';
		echo "<div class=\"col-sm-offset-2 col-sm-8 text-center\" >";
		if ($type == 'error') {
			echo "<div class=\"alert alert-danger bold margin-bottom-md\" role=\"alert\">";
		} else {
			echo "<div class=\"alert alert-success bold\" role=\"alert\">";
		}
		echo '<b>'.$msg.'</b>';
		echo "</div></div></div>";
	}
	
	public static function compareDates ( $date1 ) {
		$deadline = date_create( $date1 );
		$today = date_create(Date('Y-m-d H:i:s'));
		$dateDifference = date_diff($today, $deadline);
		$setDate = $dateDifference->format("%h Hours, %i Minutes, %s Seconds Left");
		return $setDate;
	}
	
	public static function determinePeriod ( $getDaysAway, $getHoursAway = NULL, $pastDate = NULL ) {
		$AwayOrAgo = (isset($pastDate)) ? 'Ago' : 'Away';
		$initOpen = (!isset($pastDate)) ? '<br />(' : '';
		$initClose = (!isset($pastDate)) ? ')' : '';
		if ( $getDaysAway > 1 ) {
			if ( $getDaysAway > 7 ) {
				$getWeeksAway = $getDaysAway / 7;
				settype($getWeeksAway, 'integer');
				$weekAway = ( $getWeeksAway > 1 ) ? $getWeeksAway ." Weeks, " : $getWeeksAway ." Week, " ;
				$setDaysAway = (( $getDaysAway % 7 ) > 1) ? $getDaysAway % 7 ." Days " : $getDaysAway % 7 ." Day ";
				return "<b>$initOpen$weekAway $setDaysAway $AwayOrAgo$initClose</b>";
			} elseif ( $getDaysAway == 7 ) {
				return '<b>'.$initOpen."1 Week $AwayOrAgo$initClose</b>";
			} else {
				return "<b>$initOpen$getDaysAway Days $AwayOrAgo$initClose</b>";
			}
		} elseif ( $getDaysAway == 1 ) {
			return $daysAway = "<b>$initOpen$getDaysAway Day $AwayOrAgo$initClose</b>";
		} else {
			if (!isset($getHoursAway)) {
				if (isset($pastDate))
				return '<b>'.$initOpen .'Today'. $initClose .'</b>';
			} else {
				$getHoursAway = explode(':', $getHours, 3);
				if ( $getHoursAway[0] > 1 ) {
					return "$initOpen$getHoursAway[0] Hours $getHoursAway[1] Minutes $AwayOrAgo$initClose";
				} elseif ( $getHoursAway[0] == 1 ) {
					return "$initOpen$getHoursAway[0] Hour $getHoursAway[1] Minutes $AwayOrAgo$initClose";
				} else {
					if ( $getHoursAway[1] > 1 ) {
						return $initOpen . (int)$getHoursAway[1] ." Minutes $AwayOrAgo$initClose";
					} elseif ( $getHoursAway[1] == 1 ) {
						return $initOpen . (int) $getHoursAway[1] ." Minute $AwayOrAgo$initClose";	
					} else {
						if ( $getHoursAway[2] > 1 ) {
							return $initOpen . (int)$getHoursAway[2] ." Seconds $AwayOrAgo$initClose";
						} else {
							return $initOpen . (int) $getHoursAway[2] ." Second $AwayOrAgo$initClose";				
						}
					}
				}
			}
		}
	}
	
}