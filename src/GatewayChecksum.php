<?php

////////////////////////////////////////////////////////////////////////////////
//
//	GatewayChecksum() - Static class for checksum and version.
//
////////////////////////////////////////////////////////////////////////////////
//
class GatewayChecksum {
  public static $checksum = "";
  public static $baseChecksum = "54b8d2e6fb2fd909b4c2e93b886b1705";
  public static $versionNo = "P6.15";  //

//////////////////////////////////////////////////////////////////////
//
//	Set the client version number.
//
//////////////////////////////////////////////////////////////////////
//
  static function SetVersion()
  {
    $dirName = dirname(__FILE__);
    $baseString = md5_file($dirName . "/GatewayService.php") .
		  md5_file($dirName . "/GatewayRequest.php") .
		  md5_file($dirName . "/GatewayResponse.php") .
		  md5_file($dirName . "/GatewayParameterList.php") .
		  md5_file($dirName . "/GatewayCodes.php");
    GatewayChecksum::$checksum = md5($baseString);
    if (GatewayChecksum::$checksum != GatewayChecksum::$baseChecksum)
      GatewayChecksum::$versionNo = "P6.15m";
  }
}

?>
