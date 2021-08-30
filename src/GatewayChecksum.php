<?php

////////////////////////////////////////////////////////////////////////////////
//
//	GatewayChecksum() - Static class for checksum and version.
//
////////////////////////////////////////////////////////////////////////////////
//
class GatewayChecksum {
  public static $checksum = "";
  public static $baseChecksum = "e3e3e5c9869b0a0922dbc66f6c8e8a0b";
  public static $versionNo = "P6.16";  //

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
      GatewayChecksum::$versionNo = "P6.16m";
  }
}

?>
