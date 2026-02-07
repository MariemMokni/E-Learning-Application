<?php
class User2{
	private $user_obj;
	private $con;
	private $user;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user=$user;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}
	
	
	

	public function getUnreadNumber() {
		$userLoggedIn = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

	public function getNotifications($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user['username'];
		$return_string = "";

		if($page == 1)
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "You have no notifications!";
			return;
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		while($row = mysqli_fetch_array($query)) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;


			$user_from = $row['user_from'];
			$opened = $row['opended'];
			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);


			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
$start_date = new DateTime($row['datetime']); // Time of post
$end_date = new DateTime($date_time_now); // Current time
$interval = $start_date->diff($end_date); // Difference between dates 

if ($interval->y >= 1) {
    if ($interval->y == 1) {
        $time_message = "1 year ago"; // Exactly 1 year ago
    } else {
        $time_message = $interval->y . " years ago"; // More than 1 year ago
    }
} else if ($interval->m >= 1) {
    $days = "";
    if ($interval->d == 0) {
        $days = " ago";
    } else if ($interval->d == 1) {
        $days = " and 1 day ago";
    } else {
        $days = " and " . $interval->d . " days ago";
    }

    if ($interval->m == 1) {
        $time_message = "1 month" . $days;
    } else {
        $time_message = $interval->m . " months" . $days;
    }
} else if ($interval->d >= 1) {
    if ($interval->d == 1) {
        $time_message = "Yesterday";
    } else {
        $time_message = $interval->d . " days ago";
    }
} else if ($interval->h >= 1) {
    if ($interval->h == 1) {
        $time_message = "1 hour ago";
    } else {
        $time_message = $interval->h . " hours ago";
    }
} else if ($interval->i >= 1) {
    if ($interval->i == 1) {
        $time_message = "1 minute ago";
    } else {
        $time_message = $interval->i . " minutes ago";
    }
} else {
    if ($interval->s < 30) {
        $time_message = "Just now";
    } else {
        $time_message = $interval->s . " seconds ago";
    }
}


			$style = ($opened == 'no') ? "background-color: #DDEDFF;" : "";

			$return_string .= "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
									</div>
								</a>";
		}


		//If posts were loaded
		if($count > $limit) //.= concaténer une chaîne à une autre
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else 
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";

		return $return_string;
	}

	public function insertNotification($post_id, $user_to, $type, $code, $comment_id = "") {

		$userLoggedIn = $this->user['username'];
		$userLoggedInName =$this->user['first_name'] . " " . $this->user['last_name'];

		$date_time = date("Y-m-d H:i:s");


		switch($type) {
			case 'comment':
				$message = $userLoggedInName . " commented on your post";
				break;
			case 'classRoom_post':
				$message = $userLoggedInName . " posted on your class room";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " commented on a post you commented on";
				break;
			case 'classRoom_comment':
				$message = $userLoggedInName . " commented on your class room post";
				break;
		}

		$link = "classRoom.php?classCode=$code#post_id=" . $post_id;
		if(!empty($comment_id) && $comment_id > 0){
			$link .="&comment=".$comment_id;
		}

		$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
	}

}

?>