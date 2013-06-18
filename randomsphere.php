<?php

//
// Sphere sampler v0.4 (16 November 2006)
// This code creates a KML (Google Earth) file of random locations on 
// a sphere. Input is: randomsphere.php?n where n is the number
// of random placemarks desired.
//
     

//
//grabs the user input for number of iterations.
//
$iterations = $_SERVER['QUERY_STRING'];

//if ($iterations > 50000) {
//	echo "Exceeded arbitrary limit assigned by Alan. Modify the code to change this limit.";
//	die;
//}

//
//KML header information
//
header('Content-type: application/vnd.google-earth.kml+xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<kml xmlns=\"http://earth.google.com/kml/2.1\">\n";
echo "<Document>\n";


for ($counter = 1; $counter <= $iterations; $counter += 1) {

//
//This section generates a random longitude. The while loop and
//switch onemeansgoagain are used to allow the possibility of 
//180 to be selected, but it will discard the iteration and 
//try again if it goes beyond the allowed range. Basically, this
//just happens since php won't allow float numbers to be 
// randomly generated.
//

$onemeansgoagain = 1;

while ($onemeansgoagain == 1) {
	$overridelong_int = rand(-180,181);
	if (($overridelong_int == -180) || ($overridelong_int == 180)){
		$overridelong_dec = rand(0,9999999);
		$overridelong_dec = ($overridelong_dec/10000000);
		if ($overridelong_dec != 0) {
			$onemeansgoagain = 1;
		}
		if ($overridelong_int == -180) {
			$onemeansgoagain = 1;	
		}
	} 	else {
			$overridelong_dec = rand(0,9999999);
			$overridelong_dec = ($overridelong_dec/10000000);
			$onemeansgoagain = 0;
		}
}
//
// This part accounts for negative directionality at zero longitude.
// The variable names (override) arise from the fact that I was writing the code
// on top of the old nonspherically corrected code.
//
if ($overridelong_int == 181){
	$overridelong_int = 0;
	//uses 1 in the next line so 0 does not get to double dip the sample
	$overridelong_dec = rand(1,9999999);
	$overridelong_dec = ($overridelong_dec/10000000);
	$newlong = '-'.$overridelong_int.$overridelong_dec;
} else { 
	if ($overridelong_int >= 0) {
		$newlong = $overridelong_int+$overridelong_dec;
	} else {
		$newlong = $overridelong_int-$overridelong_dec;
	}
}


//
// Now that a longitude is generated, the next section creates a 
// latitude.
//

$v_int = rand(0,9999999);
$v_spherical = ($v_int/9999999);

//
// here's the part that adjusts to reduce pole-related bias.
//
$mypi = 3.14159265358979323846264338327950288419716939937510;
$phi = (acos((2*$v_spherical)-1));
$almost_latitude =  $phi * (180/$mypi);
$latitude = ($almost_latitude - 90);
//
//This finishes the loop by writing the KML tags.
//
$response = '<Placemark>';
$response .= '<description></description>';
$response .= '<name></name>';
$response .= '<visibility>1</visibility>';
$response .= '<Point>';
$response .= "<coordinates>$newlong,$latitude,0</coordinates>";
$response .= '</Point>';
$response .= '</Placemark>';
echo $response;
echo "\n";
//
//This ends the main while loop.
//
}

//
// Tags to close the KML.
//
echo "</Document>";
echo "</kml>";
?>