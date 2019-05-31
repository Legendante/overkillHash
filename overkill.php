<?php
// In a completely overkill fashion, this function takes the username/password combination 
// and returns a hash string.
function overkillHash($Usr, $Pass)
{
	$Usr = strtolower($Usr);																// Username to lowercase because users rarely remember what case combo they used.
	$Pepper1 = "AABBCCDDEEAllTheWayToAHundredPlusCharacters";								// Pepper String. Change this to something else on every system
	$Pepper2 = "~!@#$%^&*(AllTheWayToAHundredPlusCharacters";								// Pepper String. Change this to something else on every system
	$SimpleSalt = "ABCDE12345";																// Pepper String for initial salting. Change this to something else on every system
	/* If you want to make this even more overkill, you could change these two pepper strings above to be read from a file on the system somewhere (outside of the document root). //*/
	$cat = $Usr . $Pass . $SimpleSalt;														// Lightly salt the password with the username
	$md5_1 = md5($cat);																		// MD5 the salted password
	$numStr = filter_var($md5_1, FILTER_SANITIZE_NUMBER_INT);								// Strip the numbers out of MD5 value
	$IniPos = 0;																			// Current string position in the pepper value
	$MD5Pepp = "0987654321";																// Initialize the pepper string with crazy salt (in case the MD5 had no numbers in it). Change this to something else on every system
	for($i = 0; $i < strlen($numStr); $i++)													// Go through the list of MD5 numbers
	{
		$StartPos = $numStr[$i];															// Grab the current position number as a start position offset
		$SubLen = (isset($numStr[$i + 1])) ? $numStr[$i + 1] : $numStr[0];					// Grab the next number as a length offset. Default to the first number if we run out of string.
		$NewPos = $IniPos + $StartPos;														// Add the position to the current pepper string position
		$IniPos = ($NewPos >= strlen($Pepper1)) ? $NewPos - strlen($Pepper1) : $NewPos;		// If the position value is greater than the lenght of the string, substract the lenght off
		$MD5Pepp .= substr($Pepper1, $IniPos, $SubLen);										// Grab from the pepper string position for the lenght offset from above
		$IniPos += $SubLen;																	// Add the length offset to the pepper string position.
	}
	$sha_1 = hash("sha512", $cat);															// SHA the salted password
	$numStr = filter_var($sha_1, FILTER_SANITIZE_NUMBER_INT);								// Strip the numbers out of SHA value
	$IniPos = 0;																			// Current string position in the pepper value
	$SHAPepp = "1234567890";																// Initialize the pepper string with crazy salt (in case the SHA512 had no numbers in it). Change this to something else on every system
	for($i = 0; $i < strlen($numStr); $i++)													// Go through the list of SHA numbers
	{
		$StartPos = $numStr[$i];															// Grab the current position number as a start position offset
		$SubLen = (isset($numStr[$i + 1])) ? $numStr[$i + 1] : $numStr[0];					// Grab the next number as a length offset. Default to the first number if we run out of string.
		$NewPos = $IniPos + $StartPos;														// Add the position to the current pepper string position
		$IniPos = ($NewPos >= strlen($Pepper1)) ? $NewPos - strlen($Pepper1) : $NewPos;		// If the position value is greater than the lenght of the string, substract the lenght off
		$SHAPepp .= substr($Pepper2, $IniPos, $SubLen);										// Grab from the pepper string position for the lenght offset from above
		$IniPos += $SubLen;																	// Add the length offset to the pepper string position.
	}
	$md5_2 = md5($SHAPepp . $MD5Pepp);														// MD5 the two peppers together
	$sha_2 = hash("sha512", $md5_2);														// SHA512 the MD5'd peppers
	return $sha_2;																			// Return the SHA value for further hashing
}

$options = array("cost" => 14);																// Set the options for BCrypt
$Username = "Username";
$Password = "Password";
$sha = overkillHash($Username, $Password);													// Hash the username/password combo
$hash = password_hash($sha, PASSWORD_BCRYPT, $options);										// BCrypt hash the returned hash
// Here you would store $hash as the password in the database.
// Echo values for fun and profit
echo $sha . " - " . $hash . " Result : " . password_verify($sha, $hash) . "<br><br>";
?>