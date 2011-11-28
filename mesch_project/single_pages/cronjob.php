<?php
/*
mesch.ch project management

Copyright 2011 mesch web consulting & design GmbH, 
all portions of this codebase are copyrighted to the people 
listed in contributors.txt.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<table id="mesch-project-cronjob" class="mesch-project-table" style="width:auto ! important;">
   <thead>
      <tr>
         <th><?php echo t('Minute')?></th>
         <th><?php echo t('Hour')?></th>
			<th><?php echo t('Day')?></th>
			<th><?php echo t('Month')?></th>
			<th><?php echo t('Weekday')?></th>
			<th><?php echo t('Command')?></th>
			<th></th>
		</tr>
	</thead>
   <tbody>
   <?php
   foreach ($cronjobs as $cronjob) {
      echo "<tr>
            <td>{$cronjob[0]}</td>
            <td>{$cronjob[1]}</td>
            <td>{$cronjob[2]}</td>
            <td>{$cronjob[3]}</td>
            <td>{$cronjob[4]}</td>
            <td>{$cronjob[5]}</td>
            <td>delete</td>
         </tr>";
   }
   ?>
   </tbody>
   <tfoot>
      <form method="post" action="<?php echo $this->action('add_cronjob') ?>">
         <tr>
            <td><input type="text" size="4" name="minute" value="0"/></td>
            <td><input type="text" size="4" name="hour" value="0"/></td>
            <td><input type="text" size="4" name="day" value="*"/></td>
            <td><input type="text" size="4" name="month" value="*"/></td>
            <td><input type="text" size="4" name="weekday" value="*"/></td>
            <td><input type="text" size="60" name="command" value=""/></td>
            <td><input type="submit" value="<?php echo('Add')?>"/></td>
         </tr>
      </form>
   </tfoot>
</table>

<p>
Examples
<pre>
30    4     *    *    *    : 04:30 every day
30    4     *    *    *    : 04:30 every day
*/5   *     *    *    *    : every 5 minutes
0     1,13  *    *    *    : 01:00 and 13:00 every day
15    8     *    *    1-5  : Monday till Friday at 8:15
0     23    24   12   *    : Every 24th of December at 23:00

When running a concrete5 job make sure you only execute those you really need.
Every job has a unique number in the first column, add it to the end like this:
 &jID=2
which will only run the job with the number 2
</pre>


   