<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$subject = t('New comment :: %s', $subject);
$body = t('Hello %s

there has been a new message posted to an issue you\'re watching:

%s

Click on the link below to view the issue:
%s

Thanks,
%s Team', $recipient, $text, $link, $team);
?>