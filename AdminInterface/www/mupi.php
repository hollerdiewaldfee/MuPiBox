<?php
 $change=0;
 $CHANGE_TXT="<div id='lbinfo'><ul id='lbinfo'>";
 include ('includes/header.php');

 if( $_POST['brightness'])
  {
	$command="sudo su - -c 'echo \"" . $_POST['brightness'] . "\" > /sys/class/backlight/rpi_backlight/brightness'";
	$set_brightness = exec($command, $output );
  }

 if( $data["mupibox"]["physicalDevice"]!=$_POST['audio'] && $_POST['audio'])
  {
  $data["mupibox"]["physicalDevice"]=$_POST['audio'];
  $command = "sudo /boot/dietpi/func/dietpi-set_hardware soundcard '" . $_POST['audio'] . "'";
  $change_soundcard = exec($command, $output, $change_soundcard );
  $CHANGE_TXT=$CHANGE_TXT."<li>Soundcard changed to  ".$data["mupibox"]["physicalDevice"]." [reboot is necessary]</li>";
  $change=1;
  }
 if( $data["mupibox"]["host"]!=$_POST['hostname'] && $_POST['hostname'])
  {
  $data["mupibox"]["host"]=$_POST['hostname'];
  $command = "sudo /boot/dietpi/func/change_hostname " . $_POST['hostname'];
  $change_hostname = exec($command, $output, $change_hostname );
  $CHANGE_TXT=$CHANGE_TXT."<li>Hostname changed to  ".$data["mupibox"]["host"]." [reboot is necessary]</li>";
  $change=1;
  }
 if( $_POST['theme'] != $data["mupibox"]["theme"] && $_POST['theme'] )
  {
  $data["mupibox"]["theme"]=$_POST['theme'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New Theme  ".$data["mupibox"]["theme"]."  is active</li>";
  $change=1;
  }
 if( $_POST['tts'] != $data["mupibox"]["ttsLanguage"] && $_POST['tts'] )
  {
  $data["mupibox"]["ttsLanguage"]=$_POST['tts'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New TTS Language  ".$data["mupibox"]["ttsLanguage"]."  - Please restart box or services</li>";
  $command = "sudo rm /home/dietpi/MuPiBox/tts_files/*.mp3";
  exec($command, $output, $result );
  $change=1;
  }

 if( $data["mupibox"]["maxVolume"]!=$_POST['maxVolume'] && $_POST['maxVolume'] )
  {
  $data["mupibox"]["maxVolume"]=$_POST['maxVolume'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Max Volume is set to ".$data["mupibox"]["maxVolume"]."</li>";
  }

 if( $data["mupibox"]["startVolume"]!=$_POST['volume'] && $_POST['volume'] )
  {
  $data["mupibox"]["startVolume"]=$_POST['volume'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Start Volume is set to ".$data["mupibox"]["startVolume"]."</li>";
  $change=1;
  }
 if(isset($_POST['idlePiShutdown']) && $_POST['idlePiShutdown'] >= 0)
  {
  $data["timeout"]["idlePiShutdown"]=$_POST['idlePiShutdown'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Idle Shutdown Time is set to ".$data["timeout"]["idlePiShutdown"]."</li>";
  $change=1;
  }
 if(isset($_POST['idleDisplayOff']) && $_POST['idleDisplayOff'] >= 0)
  {
  $data["timeout"]["idleDisplayOff"]=$_POST['idleDisplayOff'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Idle Time for Display is set to ".$data["timeout"]["idleDisplayOff"]."</li>";
  $change=1;
  }
 if( $data["timeout"]["pressDelay"]!=$_POST['pressDelay'] && $_POST['pressDelay'] )
  {
  $data["timeout"]["pressDelay"]=$_POST['pressDelay'];
  $change=1;
  }
 if( $data["shim"]["ledPin"]!=$_POST['ledPin'] && $_POST['ledPin'])
  {
  $data["shim"]["ledPin"]=$_POST['ledPin'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New GPIO for Power-LED set to ".$data["shim"]["ledPin"]."</li>";
  $change=1;
  }
 if( $data["chromium"]["resX"]!=$_POST['resX'] && $_POST['resX'])
  {
  $data["chromium"]["resX"]=$_POST['resX'];
  $CHANGE_TXT=$CHANGE_TXT."<li>X-Resolution set to ".$data["chromium"]["resX"]."</li>";
  $change=1;
  }
 if( $data["chromium"]["resY"]!=$_POST['resY'] && $_POST['resY'])
  {
  $data["chromium"]["resY"]=$_POST['resY'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Y-Resolution set to ".$data["chromium"]["resY"]."</li>";
  $change=1;
  }
  if( $data["mupibox"]["maxVolume"] < $data["mupibox"]["startVolume"] )
	{
	$data["mupibox"]["startVolume"]=$data["mupibox"]["maxVolume"];
	$CHANGE_TXT=$CHANGE_TXT."<li>Start Volume is set to ".$data["mupibox"]["maxVolume"]." because of Max Volume</li>";
	$change=1;
	}
 if( $change )
  {
   $json_object = json_encode($data);
   $save_rc = file_put_contents('/tmp/mupiboxconfig.json', $json_object);
   exec("sudo mv /tmp/mupiboxconfig.json /etc/mupibox/mupiboxconfig.json");
   exec("sudo /usr/local/bin/mupibox/./setting_update.sh");
   exec("sudo -i -u dietpi /usr/local/bin/mupibox/./restart_kiosk.sh");
  }
$CHANGE_TXT=$CHANGE_TXT."</ul></div>";
?>
<script>$(function()
{
$('.slider').on('input change', function(){
          $(this).next($('.slider_label')).html(this.value);
        });
      $('.slider_label').each(function(){
          var value = $(this).prev().attr('value');
          $(this).html(value);
        });  
  
  
})</script>
<form class="appnitro" name="mupi" method="post" action="mupi.php" id="form">
<div class="description">
<h2>MupiBox settings</h2>
<p>This is the central configuration of your MuPiBox...</p>
</div>
<ul >

<li id="li_1" >
	<label class="description" for="hostname">Hostname </label>
	<div>
	<input id="hostname" name="hostname" class="element text medium" type="text" maxlength="255" value="<?php
	print $data["mupibox"]["host"];
	?>"/><span  class="slider_label"></span>
	</div><p class="guidelines" id="guide_1"><small>Please insert the hostname of the MuPiBox. Don't use Spaces or other special charachters! Default: MuPiBox</small></p>
</li>
<li id="li_1" >
	<label class="description" for="theme">Brightness</label>
	<div>
	<input name="brightness" type="range" min="0" max="255" step="1.0" value="<?php 
		$command = "cat /sys/class/backlight/rpi_backlight/brightness";
		$thisbrightness = exec($command, $boutput);
		echo $boutput[0];
	?>">
	</div>

</li>
<li id="li_1" >
	<label class="description" for="theme">Theme </label>
	<div>
	<select id="theme" name="theme" class="element text medium" onchange="switchImage();">
	<?php
	$Themes = $data["mupibox"]["installedThemes"];
	foreach($Themes as $key) {
	if( $key == $data["mupibox"]["theme"] )
	{
	$selected = " selected=\"selected\"";
	}
	else
	{
	$selected = "";
	}
	print "<option value=\"". $key . "\"" . $selected  . ">" . $key . "</option>";
	}
	?>
	"</select>
	</div>
	<div class="themePrev"><img src="images/<?php print $data["mupibox"]["theme"]; ?>.png" width="250" height="150" name="selectedTheme" /></div>

</li>
<li id="li_1" >
	<label class="description" for="theme">TTS Language </label>
	<div>
	<select id="tts" name="tts" class="element text medium">
	<?php
	$language = $data["mupibox"]["googlettslanguages"];
	foreach($language as $key) {
	if( $key['iso639-1'] == $data["mupibox"]["ttsLanguage"] )
	{
	$selected = " selected=\"selected\"";
	}
	else
	{
	$selected = "";
	}
	print "<option value=\"". $key['iso639-1'] . "\"" . $selected  . ">" . $key['Language'] . "</option>";
	}
	?>
	"</select>
	</div>
</li>
<li id="li_1" >
	<label class="description" for="theme">Audio Device / Soundcard </label>
	<div>
	<select id="audio" name="audio" class="element text medium">
	<?php
	$audio = $data["mupibox"]["AudioDevices"];
	foreach($audio as $key) {
	if( $key['tname'] == $data["mupibox"]["physicalDevice"] )
	{
	$selected = " selected=\"selected\"";
	}
	else
	{
	$selected = "";
	}
	print "<option value=\"". $key['tname'] . "\"" . $selected  . ">" . $key['ufname'] . "</option>";
	}
	?>
	"</select>
	</div>
</li>
<li id="li_1" >
	<label class="description" for="volume">Volume after power on </label>
	<div>
	<select id="volume" name="volume" class="element text medium">
	<?php
	$volume = $data["mupibox"]["startVolume"];
	for($i=0; $i <= 100; $i=$i+10) {
	if( $i == $data["mupibox"]["startVolume"] )
	{
	$selected = " selected=\"selected\"";
	}
	else
	{
	$selected = "";
	}
	print "<option value=\"". $i . "\"" . $selected  . ">" . $i . "</option>";
	}
	?>
	"</select>
	</div><p class="guidelines" id="guide_1"><small>Set the volume after booting...</small></p>
</li>

<li id="li_1" >
	<label class="description" for="volume">Set max volume</label>
	<div>
	<select id="maxVolume" name="maxVolume" class="element text medium">
	<?php
	$volume = $data["mupibox"]["maxVolume"];
	for($i=0; $i <= 100; $i=$i+10) {
	if( $i == $data["mupibox"]["maxVolume"] )
	{
	$selected = " selected=\"selected\"";
	}
	else
	{
	$selected = "";
	}
	print "<option value=\"". $i . "\"" . $selected  . ">" . $i . "</option>";
	}
	?>
	"</select>
	</div><p class="guidelines" id="guide_1"><small>Set the maximum volume...</small></p>
</li>


<li id="li_1" >
	<label class="description" for="idlePiShutdown">Idle Time to Shutdown </label>
	<div>
	<input id="idlePiShutdown" name="idlePiShutdown" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["timeout"]["idlePiShutdown"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Set the idle time (idle = nothing played) to shutdown.</small></p>
</li>


<li id="li_1" >
	<label class="description" for="idleDisplayOff">Idle Display Off Timeout </label>
	<div>
	<input id="idleDisplayOff" name="idleDisplayOff" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["timeout"]["idleDisplayOff"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Set the idle time to standby the display (powersaving).</small></p>
</li>

<li id="li_1" >
	<label class="description" for="pressDelay">Power Off Button Delay </label>
	<div>
	<input id="pressDelay" name="pressDelay" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["timeout"]["pressDelay"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Currently UNUSED!</small></p>
</li>

<li id="li_1" >
	<label class="description" for="ledPin">LED GPIO OnOffShim </label>
	<div>
	<input id="ledPin" name="ledPin" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["shim"]["ledPin"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Please insert the GPIO Number (not PIN!!!) of the connect LED. Default: 25</small></p>
</li>

<li id="li_1" >
	<label class="description" for="resX">Display Resolution X </label>
	<div>
	<input id="resX" name="resX" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["chromium"]["resX"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Set the X-width (horizontal) in px. Please just enter Numbers.</small></p>
</li>

<li id="li_1" >
	<label class="description" for="resY">Display Resolution Y </label>
	<div>
	<input id="resY" name="resY" class="element text medium" type="number" maxlength="255" value="<?php
	print $data["chromium"]["resY"];
	?>"/>
	</div><p class="guidelines" id="guide_1"><small>Set the y-width (vertical) in px. Please just enter Numbers.</small></p>
</li>
<li class="buttons">
	<input type="hidden" name="form_id" value="37271" />

	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</li>


</ul>
</form><p>


<?php
 include ('includes/footer.php');
?>
