<?php /* Smarty version 2.6.25, created on 2009-07-04 10:26:13
         compiled from CoreHome/templates/piwik_tag.tpl */ ?>
<?php if (ereg ( 'http://127.0.0.1|http://localhost|http://piwik.org' , $this->_tpl_vars['url'] )): ?>
<div style="clear:both"></div>
<?php echo '
<!-- Piwik -->
<script language="javascript" src="piwik.js" type="text/javascript"></script>
<script type="text/javascript">
try {
 var piwikTracker = Piwik.getTracker("piwik.php", 1);
 piwikTracker.setCustomData({ \'video_play\':1, \'video_finished\':0 });
 piwikTracker.trackPageView();
 piwikTracker.enableLinkTracking();
} catch(err) {}
</script>
<!-- End Piwik Tag -->
'; ?>

<?php endif; ?>