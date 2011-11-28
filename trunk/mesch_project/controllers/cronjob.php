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

class CronjobController extends Controller {
   
   public function view() {
      $jobs = array();
      $crontab = exec('crontab -l', $jobs);
      
      $cronjobs = array();
      //$jobs = preg_split('/$\R?^/m', $crontab);
      foreach ($jobs as $job) {
         $jobParts = preg_split('[ ]', $job);

         for ($i = 6; $i <= count($jobParts); $i++) {
            $jobParts[5] .= ' ' .$jobParts[$i];
         }         
         array_splice($jobParts, 6);
         
         if (count($jobParts) == 6) {
            $cronjobs[] = $jobParts;
         }
      }
      
      $this->set('cronjobs', $cronjobs);
      $this->set('crontab',  $crontab);
   }
   
   public function add_cronjob() {
      exec('crontab -l', $jobs);
      $cronjobs = trim(join("\n", $jobs), "\n");
      
      
      $cronjobs .= "\n{$_POST['minute']} {$_POST['hour']} {$_POST['day']} {$_POST['month']} {$_POST['weekday']} {$_POST['command']}\n ";
      
      $tempFileName = tempnam(ini_get('upload_tmp_dir'), 'meschprojectcron');

      file_put_contents($tempFileName, $cronjobs);
      
      $result = exec("crontab " . $tempFileName);
      
      $this->set('result', $result);
      $this->view();
   }
   
}
?>