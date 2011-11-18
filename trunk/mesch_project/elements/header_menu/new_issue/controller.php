<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class NewIssueConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController {
	public function displayItem() {
      global $c;
      return $c->getCollectionTypeHandle() == 'issue' ||  $c->getCollectionTypeHandle() == 'project';
	}
}
?>