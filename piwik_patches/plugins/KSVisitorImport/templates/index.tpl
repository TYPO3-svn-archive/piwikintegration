{assign var=showSitesSelection value=false}
{assign var=showPeriodSelection value=false}
{include file="CoreAdminHome/templates/header.tpl"}

<h2>{'KSVisitorImport_KSVisitorImport'|translate}</h2>
<p>{'KSVisitorImport_PluginDescription'|translate}</p>

<form method="POST" action="{url module=KSVisitorImport action=generate}">
	<table class="adminTable">
		<tr>
		    <td><label for="idSite">{'General_ChooseWebsite'|translate}</label></td>
		    <td><select name="idSite" id="idSite">
		    {foreach from=$sitesList item=site}
		        <option value="{$site.idsite}">{$site.name}</option>
		    {/foreach}
		    </select></td>
		</tr>
		<tr>
		    <td><label for="path">{'KSVisitorImport_path'|translate}</label></td>
		    <td><input type="text" value="/" name="path" id="path" /></td>
		</tr>
		<tr>
		    <td><label for="logfiletype">{'KSVisitorImport_logfiletype'|translate}</label></td>
		    <td><select name="logfiletype" id="logfiletype">
		    {foreach from=$logfilesList item=type key=key}
		        <option value="{$key}">{$type.name}</option>
		    {/foreach}
		    </select></td>
		</tr>
		<tr>
		    <td><label for="keepLogs">{'KSVisitorImport_keepLogs'|translate}</label></td>
		    <td><input type="checkbox" value="1" name="keepLogs" id="keepLogs" /></td>
		</tr>
		<tr>
		    <td><label for="debug">{'KSVisitorImport_debug'|translate}</label></td>
		    <td><input type="checkbox" value="1" name="debug" id="debug" /></td>
		</tr>
		<tr>
		    <td><label for="choice">{'KSVisitorImport_AreYouSure'|translate}</label></td>
		    <td>
				<input type="checkbox" name="choice" id="choice" value="yes" /> <label for="choice">{'KSVisitorImport_ChoiceYes'|translate}</label><br />
				<p>{'KSVisitorImport_Warning'|translate}<br />
				{'KSVisitorImport_NotReversible'|translate:'<b>':'</b>'}</p>
			</td>
		</tr>
	</table>
	<input type="submit" value="{'KSVisitorImport_Submit'|translate}" name="submit" class="submit" />
	<input type="hidden" value="{$token_auth}" name="token_auth" />
	<input type="hidden" value="{$nonce}" name="form_nonce" />
</form>
<table class="adminTable">
	<tr>
	    <td><label>{'KSVisitorImport_basedon'|translate}</label></td>
	    <td>
			<ul>
				<li><a href="http://forge.typo3.org/issues/11791">http://forge.typo3.org/issues/11791</a></li>
				<li><a href="http://jaymz.eu/2010/02/importing-existing-visitor-stats-from-google-analytics-to-piwik/">http://jaymz.eu/2010/02/importing-existing-visitor-stats-from-google-analytics-to-piwik/</a></li>
				<li><a href="http://www.cabag.ch/typo3-extensions/typo3-schnittstellen/piwik-apache-import.html">http://www.cabag.ch/typo3-extensions/typo3-schnittstellen/piwik-apache-import.html</a></li>
			</ul>
		</td>
	</tr>
</table>

{include file="CoreAdminHome/templates/footer.tpl"}
