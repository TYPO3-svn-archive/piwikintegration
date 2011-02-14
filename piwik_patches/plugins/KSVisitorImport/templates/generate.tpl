{assign var=showSitesSelection value=false}
{assign var=showPeriodSelection value=false}
{include file="CoreAdminHome/templates/header.tpl"}

<h2>{'VisitorGenerator_VisitorGenerator'|translate}</h2>

<table class="admin">
	<tr>
		<td>Filetype</td>
		<td>{$logfilename}<br><small>{$logfiletype}</small></td>
	</tr>
	<tr>
		<td>File</td>
		<td>{$path}</td>
	</tr>
	<tr>
		<td>Rows</td>
		<td>{$rows}</td>
	</tr>
	<tr>
		<td>Time</td>
		<td>{$timer}</td>
	</tr>
</table>

{include file="CoreAdminHome/templates/footer.tpl"}