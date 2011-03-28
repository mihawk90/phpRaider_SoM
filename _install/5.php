<?php
error_reporting('NONE');

// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// setup output
$output = "<?php\n// This file is generated automatically\n// Do not alter unless instructed to do so!\n\n";
$output .= "global \$pConfig;\n";

foreach($_SESSION as $key=>$value) {
	if (substr($key, 0, 8) == 'pConfig_') {
		$output .= "\$pConfig['".substr($key, 8)."'] = '{$value}';\n";
	}
}

$output .= "?>";

// setup output
$output_html = nl2br(htmlspecialchars($output));

// write configuration file
$fd = fopen(RAIDER_BASE_PATH.'configuration.php', 'w+');

if(!$fd) {
	$p->assign('error',"<div align=center>
							<div class=errorBody>
								Unable to write configuration file. Please follow the instructions
								below to create the file manually.
								<ol>
									<li>
										Create a blank file named <strong>configuration.php</strong>.
									</li>
									<li>
										Copy and paste the following inside the file you just created

										<table width=75% cellpadding=5 cellspacing=0 border=1 style=border:1px solid #ffffff>
											<tr style=background-color:#ffffff>
												<td>".$output_html."</td>
											</tr>
										</table>
									</li>
									<li>
										Save the file.
									</li>
									<li>
										Upload the file to your webserver in the base directory <strong>".RAIDER_BASE_PATH."</strong>
									</li>
								</ol>
								Aftewards, click <a href=install.php?option={$next_option}>here</a> to continue.
							</div>
						</div>");
} else {
	$p->assign('next_option',$next_option);
	$p->assign('error','<div align="left" style="color:lime">
							Configuration file written successfully!
						</div>
						<div style="text-align:left">
							<a href="install.php?option='.$next_option.'">Continue</a>
						</div>');
	fwrite($fd, $output);
	fclose($fd);
}

$p->display($option.'.tpl');
?>