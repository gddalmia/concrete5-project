<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$subject = t('New issue :: %s', $subject);
$body = t('Hello %s

there has been a new issue assigned to you:

%s

Click on the link below to view the issue:
%s

Thanks,
%s Team', $recipient, $text, $link, $team);
?>