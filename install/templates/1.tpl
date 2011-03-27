<div class="contentHeader">phpRaider Installation - Installation Check</div>
<p>Before installating or upgrading, please verify that the following have write permissions:<br>
{if !empty($phpraider)}{$phpraider}<br>
{/if}{if !empty($templates)}{$templates}<br>
{/if}{if !empty($cache)}{$cache}<br>
{/if}{if !empty($configuration)}{$configuration}<br>
{/if}<div class="contentHeader">phpRaider Installation - php setup Check</div>
{if !empty($php_error_reporting)}{$php_error_reporting}<br>
{/if}{if !empty($session_cookie)}{$session_cookie}<br>
{/if}{if !empty($session_trans_sid)}{$session_trans_sid}<br>
{/if}{if !empty($session_path)}{$session_path}<br>
{/if}{if !empty($register_globals)}{$register_globals}<br>
{/if}<div class="contentHeader">phpRaider Installation - About phpRaider</div><br>
<h3>What is phpRaider? </h3>
<p>phpRaider is an online raid management and organization utility for massively multiplayer online roleplaying games (MMORPG). phpRaider is the successor to phpRaid which was the worlds first raid management utility developed exclusively for <a href="http://www.worldofwarcraft.com/">World of Warcraft</a>, an MMORPG by <a href="http://www.blizzard.com/">Blizzard Entertainment</a>. </p>
<h3>Features</h3>
<ul>
  <li>Support for any number of classes, races, and class/race combinations.</li>
  <li>Support for any MMORPG game. </li>
  <li>Support for any number of attributes assigned to character profiles such as resistances, damage abilities, talents, and more!</li>
  <li>Raids are arranged in an easy to view calendar format showing only pertinent information. When clicked, a detailed list of class signups and raid information become available.</li>
  <li>Post announcements for your members to see.</li>
  <li>Create any number of characters for each user profile.</li>
  <li>Extensive permission settings allow you to customize who has access to what features of phpRaider.</li>
  <li>Ability to signup for multiple raids and create recurring raids. </li>
  <li>Completely free to use!</li>
  <li>Much, much more...</li>
</ul>
<h3>Requirements</h3>
<p>phpRaider requires a web server (IIS, Apache, etc), a MySQL database (4.1 or higher), PHP (4.0 or higher), and a minimal knowledge of software installation</p>
<p><a href="install.php?option={$next_option}">Click here to continue setup</a> </p>
