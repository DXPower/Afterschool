<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', ON);

function getUsersGroups($mysqli, $userId) {
	if (doesUserExist($mysqli, $userId)) {
		if ($stmt = $mysqli->prepare("SELECT group_id FROM members_groups WHERE member_id = ?")) {
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($groupId);
			$result = array();
			
			while ($stmt->fetch()) {
				$group = getGroup($mysqli, $groupId);
				
				if (is_array($group)) {
					array_push($result, $group);
				}
			}
			
			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "User does not exist";
	}
}

function getUsersSchools($mysqli, $userId) {
	if (doesUserExist($mysqli, $userId)) {
		if ($stmt = $mysqli->prepare("SELECT school_id FROM members_schools WHERE member_id = ?")) {
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($schoolId);
			$result = array();
			
			while ($stmt->fetch()) {
				$school = getSchool($mysqli, $schoolId);
				
				if (is_array($school)) {
					array_push($result, $school);
				}
			}
			
			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "User does not exist";
	}
}

function getSchool($mysqli, $schoolId) {
	if (doesSchoolExist($mysqli, $schoolId)) {
		if ($stmt = $mysqli->prepare("SELECT id, name, location FROM schools WHERE id = ? LIMIT 1")) {
			$stmt->bind_param("i", $schoolId);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $location);
			$stmt->fetch();
			$result = array("id" => $id,
							"name" => $name,
							"location" => $location);
							
			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "School does not exist";
	}
}

function leaveGroup($mysqli, $userId, $groupId) {
    if (doesGroupExist($mysqli, $groupId)) {
        if (isUserInGroup($mysqli, $userId, $groupId)) {
            if ($stmt = $mysqli->prepare("DELETE FROM members_groups WHERE member_id = ? AND group_id = ? LIMIT 1")) {
                $stmt->bind_param("ii", $userId, $groupId);
                $stmt->execute();
                
                return true;
            } else {
                return $mysqli->error;
            }
        } else {
            return "User already in group";
        }
    } else {
        return "Group does not exist";
    }
}

function isUserInGroup($mysqli, $userId, $groupId) {
    if (doesGroupExist($mysqli, $groupId)) {
        if ($stmt = $mysqli->prepare("SELECT id FROM members_groups WHERE member_id = ? LIMIT 1")) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($result);
            $stmt->fetch();
            
            if (!$result) {
                return false;
            } else {
                return true;
            }
        } else {
            return "Faulty MYSQLI Statement";
        }
    } else {
        return "Group does not exist";
    }
}

function getGroup($mysqli, $groupId) {
    if (doesGroupExist($mysqli, $groupId)) {
        if ($stmt = $mysqli->prepare("SELECT name, school_id, creator_id FROM groups WHERE id = ? LIMIT 1")) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name, $schoolId, $creatorId);
            $stmt->fetch();
            
            return array("id" => $groupId,
                        "name" => $name,
                        "school" => getSchool($mysqli, $schoolId),
                        "creator" => getMember($mysqli, $creatorId));
        }
    }
}

function getMembersInGroup($mysqli, $groupId) {
    if (doesGroupExist($mysqli, $groupId)) {
        if ($stmt = $mysqli->prepare("SELECT member_id FROM members_groups WHERE group_id = ? LIMIT 10")) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($memberId);
            $result = array();
            
            while ($stmt->fetch()) {
                array_push($result, getMember($mysqli, $memberId));
            }
            
            return $result;
        } else {
            return "Faulty MYSQLI Statement";
        }
    } else {
        return "Group does not exist";
    }
}

function doesGroupExist($mysqli, $groupId) {
    if ($stmt = $mysqli->prepare("SELECT id FROM groups WHERE id = ? LIMIT 1")) {
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($result);
        $stmt->fetch();
        
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
}

function joinGroup($mysqli, $userId, $groupId) {
    if (doesGroupExist($mysqli, $groupId)) {
        if (!isUserInGroup($mysqli, $userId, $groupId)) {
            if ($stmt = $mysqli->prepare("INSERT INTO members_groups SET member_id = ?, group_id = ?")) {
                $stmt->bind_param("ii", $userId, $groupId);
                $stmt->execute();
                
                return true;
            } else {
                return "Faulty MYSQLI Statement";
            }
        } else {
            return "User already in group";
        }
    }
}

function getGroups($mysqli, $schoolId) {
	if (doesSchoolExist($mysqli, $schoolId)) {
		if ($stmt = $mysqli -> prepare("SELECT id, name FROM groups WHERE school_id = ? LIMIT 10")) {
			$stmt -> bind_param("i", $schoolId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($id, $name);
			$result = array();

			while ($stmt -> fetch()) {
				array_push($result, array("id" => $id, "name" => $name));
			}

			return $result;
		}
	}
}

function createGroup($mysqli, $userId, $name, $schoolId) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesSchoolExist($mysqli, $schoolId)) {
			if ($stmt = $mysqli -> prepare("INSERT INTO groups SET name = ?, school_id = ?, creator_id = ?")) {
				$stmt -> bind_param("sii", $name, $schoolId, $userId);
				$stmt -> execute();

                $groupInsertId = $mysqli->insert_id;
                joinGroup($mysqli, $userId, $groupInsertId);

				return $groupInsertId;
			} else {
				return "Faulty MYSQLI Statement";
			}
		} else {
			return "School does not exist";
		}
	} else {
		return "User does not exist";
	}
}

function getBoardMessageReplies($mysqli, $mId) {
	if (doesMessageExist($mysqli, $mId)) {
		if ($stmt = $mysqli -> prepare("SELECT * FROM board_replies WHERE message_id = ? ORDER BY dt ASC LIMIT 10")) {
			$stmt -> bind_param("i", $mId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($id, $messageId, $body, $authorId, $dt);
			$result = array();

			while ($stmt -> fetch()) {
				array_push($result, array("id" => $id, "messageId" => $messageId, "body" => $body, "author" => getMember($mysqli, $authorId), "dt" => date("l, F jS", strtotime($dt))));
			}

			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "Message does not exist";
	}
}

function doesBoardReplyExist($mysqli, $replyId) {
    if ($stmt = $mysqli->prepare("SELECT id FROM board_replies WHERE id = ? LIMIT 10")) {
        $stmt->bind_param("i", $replyId);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($result);
        $stmt->fetch();
        
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
}

function getBoardMessageReply($mysqli, $replyId) {
    if (doesBoardReplyExist($mysqli, $replyId)) {
        if ($stmt = $mysqli->prepare("SELECT id, body, author_id, dt FROM board_replies WHERE id = ? LIMIT 1")) {
            $stmt->bind_param("i", $replyId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $body, $authorId, $dt);
            $stmt->fetch();
            
            return array("id" => $id,
                        "body" => $body,
                        "author" => getMember($mysqli, $authorId),
                        "dt" => date("l, F jS", strtotime($dt)));
        }
    }
}

function createBoardMessageReply($mysqli, $userId, $messageId, $body) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesMessageExist($mysqli, $messageId)) {
			if ($stmt = $mysqli -> prepare("INSERT INTO board_replies SET author_id = ?, message_id = ?, body = ?")) {
				$stmt -> bind_param("iis", $userId, $messageId, $body);
				$stmt -> execute();

				return $mysqli->insert_id;
			} else {
				return "Faulty MYSQLI Statement";
			}
		} else {
			return "Message does not exist";
		}
	} else {
		return "User does not exist";
	}
}

function doesMessageExist($mysqli, $messageId) {
	if ($stmt = $mysqli -> prepare("SELECT id FROM board WHERE id = ?")) {
		$stmt -> bind_param("i", $messageId);
		$stmt -> execute();
		$stmt -> store_result();
		$stmt -> bind_result($result);
		$stmt -> fetch();

		if (!$result) {
			return false;
		} else {
			return true;
		}
	}
}

function getBoardMessagesWithGraduationYear($mysqli, $sId, $graduationYear, $messageType) {
	if (doesSchoolExist($mysqli, $sId)) {
		if ($stmt = $mysqli -> prepare("SELECT id, school_id, title, body, author_id, dt 
				FROM board b 
				WHERE EXISTS(SELECT * FROM members m WHERE m.graduation_year = ? AND m.id = b.author_id) 
					AND b.school_id = ?
					AND b.message_type = ? 
				ORDER BY dt DESC LIMIT 10")) {
			$stmt -> bind_param("iis", $graduationYear, $sId, $messageType);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($id, $schoolId, $title, $body, $userId, $dt);
			$result = array();

			while ($stmt -> fetch()) {
				array_push($result, array("id" => $id, "schoolId" => $schoolId, "title" => $title, "body" => $body, "author" => getMember($mysqli, $userId), "dt" => date("l, F jS", strtotime($dt))));
			}

			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "School does not exist";
	}
}

function getBoardMessages($mysqli, $sId, $messageType) {
	if (doesSchoolExist($mysqli, $sId)) {
		if ($stmt = $mysqli -> prepare("SELECT id, school_id, title, body, author_id, dt FROM board WHERE school_id = ? AND message_type = ? ORDER BY dt DESC LIMIT 10")) {
			$stmt -> bind_param("is", $sId, $messageType);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($id, $schoolId, $title, $body, $userId, $dt);
			$result = array();

			while ($stmt -> fetch()) {
				array_push($result, array("id" => $id, "schoolId" => $schoolId, "title" => $title, "body" => $body, "author" => getMember($mysqli, $userId), "dt" => date("l, F jS", strtotime($dt))));
			}

			return $result;
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "School does not exist";
	}
}

function createBoardMessage($mysqli, $userId, $schoolId, $title, $body, $messageType) {
	if (doesUserExist($mysqli, $userId)) {
		if ((doesSchoolExist($mysqli, $schoolId) && $messageType == 'school') || ($messageType == 'group' && doesGroupExist($mysqli, $schoolId))) {
			if (($messageType == 'school' && isUserInSchool($mysqli, $userId, $schoolId)) || ($messageType == 'group' && isUserInGroup($mysqli, $userId, $schoolId))) {
				if ($stmt = $mysqli -> prepare("INSERT INTO board SET author_id = ?, school_id = ?, title = ?, body = ?, message_type = ?")) {
					$stmt -> bind_param("iisss", $userId, $schoolId, $title, $body, $messageType);
					$stmt -> execute();

					return true;
				} else {
					return "Faulty MYSQLI Statement";
				}
			} else {
				return "User is not in school";
			}
		} else {
			return "School does not exist";
		}
	} else {
		return "User does not exist";
	}
}

function setupUser($mysqli, $userId, $image, $graduationYear) {
	if (doesUserExist($mysqli, $userId)) {
		if ($sua = setUserAvatar($mysqli, $userId, $image)) {
			if ($stmt = $mysqli -> prepare("UPDATE members SET graduation_year = ?, setup = TRUE WHERE id = ? LIMIT 1")) {
				$stmt -> bind_param("ii", $graduationYear, $userId);
				$stmt -> execute();

				return true;
			} else {
				return "Faulty MYSQLI Statement #2";
			}
		} else {
			return $sua;
		}
	} else {
		return "User does not exist #2";
	}
}

function getUserAvatar($mysqli, $userId) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesUserHaveAvatar($mysqli, $userId)) {
			if ($stmt = $mysqli -> prepare("SELECT image FROM members_images WHERE member_id = ?")) {
				$stmt -> bind_param("i", $userId);
				$stmt -> execute();
				$stmt -> store_result();
				$stmt -> bind_result($image);
				$stmt -> fetch();

				// Will return the directory of the image.
				return $image;
			} else {
				return "hi";
				return $mysqli -> error;
			}
		} else {
			return "User does not have avatar";
		}
	} else {
		return "User does not exist";
	}
}

function setUserAvatar($mysqli, $userId, $image) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesUserHaveAvatar($mysqli, $userId)) {
			if ($stmt = $mysqli -> prepare("UPDATE members_images SET image = ? WHERE member_id = ? LIMIT 1")) {
				$fileDir = "img/user_images/";
				$fileName = $fileDir . basename($image) . ".jpg";

				$img = getUserAvatar($mysqli, $userId);
				if ($img != false) {
					if (move_uploaded_file($image, "../../" . $img)) {
						return true;
					} else {
						return "Error uploading file";
					}
				}

				$stmt -> bind_param("si", $fileName, $userId);
				$stmt -> execute();

				if (move_uploaded_file($image, "../../" . $fileName)) {
					return true;
				} else {
					return "Error uploading file";
				}
			} else {
				return "Faulty MYSQLI Statement #1";
			}
		} else {
			if ($stmt = $mysqli -> prepare("INSERT INTO members_images SET image = ?, member_id = ?")) {
				$fileDir = "img/user_images/";
				$fileName = $fileDir . basename($image) . ".jpg";

				$stmt -> bind_param("si", $fileName, $userId);
				$stmt -> execute();

				if (move_uploaded_file($image, "../../" . $fileName)) {
					return true;
				} else {
					return "Error uploading file";
				}
			} else {
				return "Faulty MYSQLI Statement #2";
			}
		}
	} else {
		return "User does not exist";
	}
}

function doesUserHaveAvatar($mysqli, $userId) {
	if (doesUserExist($mysqli, $userId)) {
		if ($stmt = $mysqli -> prepare("SELECT id FROM members_images WHERE member_id = ? LIMIT 1")) {
			$stmt -> bind_param("i", $userId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($result);
			$stmt -> fetch();

			if (!$result) {
				return false;
			} else {
				return true;
			}
		} else {
			return "Faulty MYSQLI Statement";
		}
	} else {
		return "User does not exist";
	}
}

function leaveSchool($mysqli, $userId, $schoolId) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesSchoolExist($mysqli, $schoolId)) {
			if (isUserInSchool($mysqli, $userId, $schoolId)) {
				if ($stmt = $mysqli -> prepare("DELETE FROM members_schools WHERE member_id = ? AND school_id = ? LIMIT 1")) {
					$stmt -> bind_param("ii", $userId, $schoolId);
					$stmt -> execute();

					return true;
				} else {
					return "Faulty MYSQL Statement";
				}
			} else {
				return "User is not in school";
			}
		} else {
			return "School does not exist";
		}
	} else {
		return "User does not exist";
	}
}

function getMembersInSchool($mysqli, $schoolId) {
	if (doesSchoolExist($mysqli, $schoolId)) {
		if ($stmt = $mysqli -> prepare("SELECT member_id FROM members_schools WHERE school_id = ?")) {
			$stmt -> bind_param("i", $schoolId);
			$stmt -> execute();
			$stmt -> store_result();

			$memberId = null;
			$stmt -> bind_result($memberId);

			$result = array();

			while ($stmt -> fetch()) {
				array_push($result, getMember($mysqli, $memberId));
			}

			return $result;
		} else {
			return "Faulty MYSQL Statement";
		}
	} else {
		return "School does not exist";
	}
}

function getMember($mysqli, $userId) {
	if (doesuserExist($mysqli, $userId)) {
		if ($stmt = $mysqli -> prepare("SELECT id, username, email, firstName, lastName, dt, graduation_year FROM members WHERE id = ? LIMIT 1")) {
			$stmt -> bind_param("i", $userId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($id, $username, $email, $firstName, $lastName, $date, $graduationYear);
			$stmt -> fetch();

			return array("id" => $id, 
						"username" => $username, 
						"email" => $email, 
						"firstName" => $firstName, 
						"lastName" => $lastName, 
						"date" => date("l, F jS", strtotime($date)), 
						"image" => getUserAvatar($mysqli, $userId),
						"graduationYear" => $graduationYear);
		} else {
			return "Faulty MYSQL Statement";
		}
	} else {
		return "User does not exist";
	}
}

function getSchoolLocation($mysqli, $schoolId) {
	if (doesSchoolExist($mysqli, $schoolId)) {
		if ($stmt = $mysqli -> prepare("SELECT location FROM schools WHERE id = ? LIMIT 1")) {
			$stmt -> bind_param("i", $schoolId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($result);
			$stmt -> fetch();

			return $result;
		} else {
			return "Faulty MYSQL Statement";
		}
	} else {
		return "School does not exist";
	}
}

function getSchoolName($mysqli, $schoolId) {
	if (doesSchoolExist($mysqli, $schoolId)) {
		if ($stmt = $mysqli -> prepare("SELECT name FROM schools WHERE id = ? LIMIT 1")) {
			$stmt -> bind_param("i", $schoolId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($result);
			$stmt -> fetch();

			return $result;
		}
	} else {
		return "School does not exist";
	}
}

function searchSchools($mysqli, $schoolName, $schoolLocation) {
	if ($stmt = $mysqli -> prepare("SELECT * FROM schools WHERE location LIKE ? AND name LIKE ?")) {
		$newName = "%$schoolName%";
		$newLocation = "%$schoolLocation%";
		$stmt -> bind_param("ss", $newLocation, $newName);
		$stmt -> execute();
		$stmt -> store_result();

		$id = null;
		$name = null;
		$location = null;
		$stmt -> bind_result($id, $name, $location);

		$result = array();

		while ($stmt -> fetch()) {
			array_push($result, array("id" => $id, "name" => $name, "location" => $location));
		}

		return $result;
	} else {
		return "Faulty MYSQL Statement";
	}
}

function createSchool($mysqli, $schoolName, $location) {
	if ($stmt = $mysqli -> prepare("INSERT INTO schools SET name = ?, location = ?")) {
		$stmt -> bind_param("ss", $schoolName, $location);
		$stmt -> execute();

		return $mysqli -> insert_id;
	} else {
		return "Faulty MYSQL Statement";
	}
}

function doesSchoolExist($mysqli, $schoolId) {
	if ($stmt = $mysqli -> prepare("SELECT id FROM schools WHERE id = ? LIMIT 1")) {
		$stmt -> bind_param("i", $schoolId);
		$stmt -> execute();
		$stmt -> store_result();
		$stmt -> bind_result($result);
		$stmt -> fetch();

		if (is_null($result)) {// Odd bug made me use is_null() instead...
			return false;
		} else {
			return true;
		}
	} else {
		return "Faulty MYSQLI Statement";
	}
}

function joinSchool($mysqli, $userId, $schoolId) {
	if (doesUserExist($mysqli, $userId)) {
		if (doesSchoolExist($mysqli, $schoolId)) {
			if (!isUserInSchool($mysqli, $userId, $schoolId)) {
				if ($stmt = $mysqli -> prepare("INSERT INTO members_schools SET member_id = ?, school_id = ?")) {
					$stmt -> bind_param("ii", $userId, $schoolId);
					$stmt -> execute();

					return true;
				} else {
					return "Faulty MYSQL Statement";
				}
			} else {
				return "User is already in school";
			}
		} else {
			return "School does not exist";
		}
	} else {
		return "User does not exist";
	}
}

function isUserInSchool($mysqli, $userId, $schoolId) {
	if ($stmt = $mysqli -> prepare("SELECT id FROM members_schools WHERE school_id = ? AND member_id = ? LIMIT 1")) {
		$stmt -> bind_param("ii", $schoolId, $userId);
		$stmt -> execute();
		$stmt -> store_result();
		$stmt -> bind_result($result);

		// Uses a different method to check if the user is in school because of a weird bug.
		if (!$stmt -> fetch()) {
			return false;
		} else {
			return true;
		}
	} else {
		return "Faulty MYSQLI Statement";
	}
}

function isMemberSetup($mysqli, $userId) {
	if (doesUserExist($mysqli, $userId)) {
		if ($stmt = $mysqli -> prepare("SELECT setup FROM members WHERE id = ? LIMIT 1")) {
			$stmt -> bind_param("i", $userId);
			$stmt -> execute();
			$stmt -> store_result();
			$stmt -> bind_result($result);
			$stmt -> fetch();

			// Checks if member is setup
			if ($result == 0) {
				return false;
			} else if ($result == 1) {
				return true;
			}
		} else {
			return "Failed MYSQL Query";
		}
	} else {
		return "User does not exist";
	}
}

function doesUserExist($mysqli, $userId) {
	if ($stmt = $mysqli -> prepare("SELECT username FROM members WHERE id = ? LIMIT 1")) {
		$stmt -> bind_param('i', $userId);
		$stmt -> execute();
		$result = $stmt -> fetch();

		// Checks if the user exists
		if (!$result) {
			return false;
		} else {
			return true;
		}
	}
}

function sec_session_start() {
	$session_name = 'sec_session_id';
	// Set a custom session name
	$secure = SECURE;
	// This stops JavaScript being able to access the session id.
	$httponly = true;
	// Forces sessions to only use cookies.
	if (ini_set('session.use_only_cookies', 1) === FALSE) {
		header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
		exit();
	}
	// Gets current cookies params.
	$cookieParams = session_get_cookie_params();
	session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
	// Sets the session name to the one set above.
	session_name($session_name);
	session_start();
	// Start the PHP session
	//	session_regenerate_id(true);    // regenerated the session, delete the old one.
}

function login($email, $password, $mysqli) {
	// Using prepared statements means that SQL injection is not possible.
	if ($stmt = $mysqli -> prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) {
		$stmt -> bind_param('s', $email);
		// Bind "$email" to parameter.
		$stmt -> execute();
		// Execute the prepared query.
		$stmt -> store_result();

		// get variables from result.
		$stmt -> bind_result($user_id, $username, $db_password, $salt);
		$stmt -> fetch();

		// hash the password with the unique salt.
		$password = hash('sha512', $password . $salt);

		if ($stmt -> num_rows == 1) {
			if ($db_password == $password) {
				// Get the user-agent string of the user.
				$user_browser = $_SERVER['HTTP_USER_AGENT'];
				// XSS protection as we might print this value
				$user_id = preg_replace("/[^0-9]+/", "", $user_id);
				$_SESSION['user_id'] = $user_id;
				// XSS protection as we might print this value
				$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
				$_SESSION['username'] = $username;
				$_SESSION['login_string'] = hash('sha512', $password . $user_browser);
				// Login successful.
				return true;
			}
		} else {
			// No user exists.
			return false;
		}
	}
}

function checkbrute($user_id, $mysqli) {
	// Get timestamp of current time
	$now = time();

	// All login attempts are counted from the past 2 hours.
	$valid_attempts = $now - (2 * 60 * 60);

	if ($stmt = $mysqli -> prepare("SELECT time 
							 FROM login_attempts 
							 WHERE user_id = ? 
							AND time > '$valid_attempts'")) {
		$stmt -> bind_param('i', $user_id);

		// Execute the prepared query.
		$stmt -> execute();
		$stmt -> store_result();

		// If there have been more than 5 failed logins
		if ($stmt -> num_rows > 5) {
			return true;
		} else {
			return false;
		}
	}
}

function login_check($mysqli) {
	// Check if all session variables are set
	if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
		$user_id = $_SESSION['user_id'];
		$login_string = $_SESSION['login_string'];
		$username = $_SESSION['username'];

		// Get the user-agent string of the user.
		$user_browser = $_SERVER['HTTP_USER_AGENT'];

		if ($stmt = $mysqli -> prepare("SELECT password 
									  FROM members 
									  WHERE id = ? LIMIT 1")) {
			// Bind "$user_id" to parameter.
			$stmt -> bind_param('i', $user_id);
			$stmt -> execute();
			// Execute the prepared query.
			$stmt -> store_result();

			if ($stmt -> num_rows == 1) {
				// If the user exists get variables from result.
				$stmt -> bind_result($password);
				$stmt -> fetch();
				$login_check = hash('sha512', $password . $user_browser);

				if ($login_check == $login_string) {
					// Logged In!!!!
					return true;
				} else {
					// Not logged in
					return false;
				}
			} else {
				// Not logged in
				return false;
			}
		} else {
			// Not logged in
			return false;
		}
	} else {
		// Not logged in
		return false;
	}

	return false;
}

function esc_url($url) {

	if ('' == $url) {
		return $url;
	}

	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = (string)$url;

	$count = 1;
	while ($count) {
		$url = str_replace($strip, '', $url, $count);
	}

	$url = str_replace(';//', '://', $url);

	$url = htmlentities($url);

	$url = str_replace('&amp;', '&#038;', $url);
	$url = str_replace("'", '&#039;', $url);

	if ($url[0] !== '/') {
		// We're only interested in relative links from $_SERVER['PHP_SELF']
		return '';
	} else {
		return $url;
	}
}
?>