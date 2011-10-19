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
class MeschProjectCommentBlockController extends BlockController {
	
	protected $btTable = 'btMeschProjectComment';
	protected $btInterfaceWidth = "590";
	protected $btInterfaceHeight = "450";

	public function getBlockTypeDescription() {
		return t("Issue Comment.");
	}
	
	public function getBlockTypeName() {
		return t("Issue Comment");
	}
      
	public function view() {
		$th = Loader::helper('text');
		Loader::library('3rdparty/markdown');
		Loader::library('geshi/geshi', 'mesch_project');
		
		$this->set('author',User::getByUserID($this->uID)->getUserName());
		
		if (defined('MESCH_PROJECT_FORMATTER_MAKENICE') && MESCH_PROJECT_FORMATTER_MAKENICE) {
			$this->text = $th->makenice($this->text, 1);
		}
		
		if (defined('MESCH_PROJECT_FORMATTER_MARKDOWN') && MESCH_PROJECT_FORMATTER_MARKDOWN) {
			$this->text = Markdown($this->text);		
		}
		
		if (defined('MESCH_PROJECT_FORMATTER_AUTOLINK') && MESCH_PROJECT_FORMATTER_AUTOLINK) {
			$this->text = $th->autolink($this->text, 1);
		}		
		
		if (defined('MESCH_PROJECT_FORMATTER_GESHI') && MESCH_PROJECT_FORMATTER_GESHI) {
			$this->text = preg_replace_callback(
			   '/<code.*?>(.*?[<code.*?>.*<\/code>]*)<\/code>/ism',
				create_function(
					'$matches',
					'$geshi = new GeSHi(trim($matches[1]), \'php\'); 
					 $geshi->set_overall_class(\'remo-board-message-code\'); 
					 $geshi->set_link_target("_blank", "");
					 $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS); 
					 return $geshi->parse_code();'
				),
				$this->text);		
		}
		
		$this->set('text', $this->text);
	
	}
	
	public function save($args) {      
      $db = Loader::db();

		$data['uID'] 	      = $args['uID'];
		$data['text'] 		   = $args['text'];
		$data['createdOn'] 	= $args['createdOn'];
      
      $this->getCollectionObject()->setAttribute('mesch_project_update', $args['createdOn']);
		
		parent::save($data);
	}
}
?>