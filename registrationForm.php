<?php
	require_once("includes/db.php");
	
	if(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
	
		//return JSON 
		$firstName = 		isset( $_POST["firstName"] ) ? $_POST["firstName"] : "";
		$lastName =  		isset( $_POST["lastName"] ) ? $_POST["lastName"] : "";
		$email =			isset( $_POST["email"] ) ? $_POST["email"] : "";
		$phone =			isset( $_POST["telephone"] ) ? preg_replace("/[^0-9]/","", $_POST["telephone"]) : "";
		$twitter =			isset( $_POST["twitter"] ) ? $_POST["twitter"] : "";
		$other_reason = 	isset( $_POST["other"] ) ? $_POST["other"] : "";
		$offer =  			isset( $_POST["offer"] ) ? $_POST["offer"] : "";
		$projectID = 		isset( $_POST["projectID"] ) ? $_POST["projectID"] : 0;
		$visitorID = 		isset( $_POST["visitorID"] ) ? $_POST["visitorID"] : 0;
		$silentSave = 		isset( $_POST["silentSave"] ) ? $_POST["silentSave"] : 0;
		$errors = array();
		$success = 0;
		$entryID = "";
		
		if( $firstName == "" || $lastName == "" || strlen($phone) < 7 || (!filter_var($email, FILTER_VALIDATE_EMAIL) ) ){
			$success = 0;
			
		}else{
		
			$conn = getConnection();
			$query = $conn->prepare("INSERT INTO `form_entry` (`entryID`, `projectID`, `visitorID`, `firstName`, `lastName`, `email`, `telephone`, `twitter`, `other`, `other_reason`, `timestamp`) VALUES (NULL, :projectID, :visitorID, :firstName, :lastName, :email, :phone, :twitter, :offer, :other, CURRENT_TIMESTAMP);");
			$query->bindParam(':projectID', $projectID);
			$query->bindParam(':visitorID', $visitorID);
			$query->bindParam(':firstName', $firstName);
			$query->bindParam(':lastName', $lastName);
			$query->bindParam(':email', $email);
			$query->bindParam(':phone', $phone);
			$query->bindParam(':twitter', $twitter);
			$query->bindParam(':offer', $offer);
			$query->bindParam(':other', $other_reason);
			
			
			if( $query->execute() ){
				$entryID = $conn->lastInsertId();
				$success = 1;
			}else{
				$errors[] = "Could not save - please contact an administrator";
			}
		}
		
		
		
		if( $silentSave ){
			if( $success ){
				echo json_encode( array("Saved" => 1 , "entryID" => $entryID, "values"=> array($firstName, $lastName, $email, $phone, $twitter, $offer, $other_reason, $projectID, $visitorID, $silentSave) ) );
				
				//check if there is a visitor ID, if so link to a corresponding value
				//log it in the analytic visitors
				
				if( $projectID != 0 && $visitorID !=0 && $entryID != ""){
					$query = $conn->prepare("UPDATE `analytics_visitors` SET `filled_out_entry` = 1, `entryID` = :entryID, `end_time` = CURRENT_TIMESTAMP WHERE `visitor_id` = :visitorID AND `project_id` = :projectID;");
					$query->bindParam(':entryID', $entryID);
					$query->bindParam(':visitorID', $visitorID);
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					
					
					
				}
				
				
			}else{
				echo json_encode( array("Saved" => 0 , "errors" => $errors ) );
			}
		}else{
				
		
		}
		
		
	
	}else{
?>
	<style type="text/css">
		#other{
			display: none;
		}
		
		#registrationForm{
			width: 480px;
			margin: 0 auto;
			display: block;
			padding-top: 50px;
		}
		
		#registrationForm *{
			font-family: Verdana, Geneva, sans-serif;
		}
		
		.row{ width: 100%; padding: 5px 0px; }
		.col{ width: 100%; }
		
		.col > input,
		.col > select{
			height: 50px;
			line-height: 50px;
			font-size: 24px;
			width: 90%;
			padding-left: 5%;
			padding-top: 5px;
			padding-bottom: 5px;
		}
		
		.col > select{
			width: 90%;
			padding-left: 0px;
			font-size: 18px;
		}
		
		.col label{
			font-size: 24px;
			display: inline-block;
			padding: 5px 0px;
		}
		
		#submitRegistration{
			display: inline-block;
			text-align: center;
			
			min-height: 50px;
			-webkit-border-radius: 20px;
			-moz-border-radius: 20px;
			border-radius: 20px;
			width: 90%;
			margin: 20px auto;
			cursor: pointer;
			text-align: center;
			
			line-height: 50px;
			
			background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #3d94f6), color-stop(1, #1e62d0) );
			background:-moz-linear-gradient( center top, #3d94f6 5%, #1e62d0 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#3d94f6', endColorstr='#1e62d0');
			background-color:#3d94f6;
			-webkit-border-top-left-radius:20px;
			-moz-border-radius-topleft:20px;
			border-top-left-radius:20px;
			-webkit-border-top-right-radius:20px;
			-moz-border-radius-topright:20px;
			border-top-right-radius:20px;
			-webkit-border-bottom-right-radius:20px;
			-moz-border-radius-bottomright:20px;
			border-bottom-right-radius:20px;
			-webkit-border-bottom-left-radius:20px;
			-moz-border-radius-bottomleft:20px;
			border-bottom-left-radius:20px;
			text-indent:0;
			border:1px solid #337fed;
			display:inline-block;
			color:#ffffff;
			font-family:Comic Sans MS;
			font-size:15px;
			font-weight:bold;
			font-style:normal;
			height:50px;
			line-height:50px;
			text-decoration:none;
			text-align:center;
			text-shadow:1px 1px 0px #1570cd;
			padding-top: 0px;
			padding-bottom: 0px;
			border: 0;
		}
		
		#submitRegistration:hover{
			background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #1e62d0), color-stop(1, #3d94f6) );
			background:-moz-linear-gradient( center top, #1e62d0 5%, #3d94f6 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e62d0', endColorstr='#3d94f6');
			background-color:#1e62d0;
		}
		
		#other{
			width: 100%;
			min-height: 50px;
		}
		
	</style>
	
	<script type="text/javascript">
		$(function(){
			$('#offer').on('change', function(){
				if( this.value == 4 ){
					$('#other').show();
				}else{
					$('#other').hide();
				}
			});
		});
	</script>
	
	<form name="registrationForm" id="registrationForm" action="registrationForm.php" method="post">
		<div class="row">
			<div class="col col50 labelCol">
				<label for="firstName">First Name *</label>
			</div>
			<div class="col col50">
				<input type="text" name="firstName" id="firstName" required="required" placeholder="Enter your first name"/>
			</div>
		</div>

		<div class="row">
			<div class="col col50 labelCol">
				<label for="lastName">Last Name *</label>
			</div>
			<div class="col col50">
				<input type="text" name="lastName" id="lastName" required="required" placeholder="Enter your last name"/>
			</div>
		</div>
		
		<div class="row">
			<div class="col col50 labelCol">
				<label for="email">Email *</label>
			</div>
			<div class="col col50">
				<input type="email" name="email" id="email" required="required" placeholder="Enter your email"/>
			</div>
		</div>
		
		<div class="row">
			<div class="col col50 labelCol">
				<label for="telephone">Telephone *</label>
			</div>
			<div class="col col50">
				<input id="telephone" name="telephone" type='tel' pattern='[\+]{0,1}?\d{0,1}?[\(]{0,1}?\d{3}[\)]{0,1}?\d{3}[\-]{0,1}?\d{4}' required="required" title='Phone Number (Format: +1(519)999-9999)' placeholder="Enter your phone number 1(519)xxx-xxxx"> 
			</div>
		</div>
		
		<div class="row">
			<div class="col col50 labelCol">
				<label for="twitter">Twitter Name</label>
			</div>
			
			<div class="col col50">
				<input type="text" name="twitter" id="twitter" placeholder="@yourTwitterName"/>
			</div>
		</div>

		<div class="row">
			<div class="col col50 labelCol">
				<label for="offer">I've received an Offer of Admission and...</label>
			</div>
			
			<div class="col col50">
				<select name="offer" id="offer">
					<option value="1">I've accepted the offer. Consider me a Golden Hawk!</option>
					<option value="2">I'm still deciding between colleges/universities. I'm on the fence!</option>
					<option value="3">I'm waiting to hear back from another college/university.</option>
					<option value="4">Other</option>
				</select><br />
				<textarea name="other" id="other" placeholder="Enter your other reason"></textarea>
			</div>
		</div>
		
		<div class="row">
			<div class="col col50 labelCol">
			<label name="agree">I agree to the contest <a target="_blank" href="http://www.wlu.ca/docsnpubs_detail.php?grp_id=12769&doc_id=58075">rules & regulations</a>: <input type="checkbox" name="agree" value="agree" required="required"/></label>
			<p style="font-size: 12px;">Please note, only students who confirm their acceptance for September 2014 will be eligible to win.</p>
			</div>
		</div>
		
		
		<p style="text-align: center"><input id="submitRegistration" type="submit" value="Submit" /></p>

		
	
	</form>
<?php
	}
?>