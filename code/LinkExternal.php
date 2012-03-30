<?php
class LinkExternal extends DataObject{

   	static $db = array (
		'Text'			=>	'Varchar',
		'URL'			=>	'Varchar',
		'Type'			=>	'Enum(\'Website, Blog, SocialNetwork\',\'Website\')',
		'SocialNetwork'	=>	'Varchar'
	);

	static $summary_fields = array(
		'Text',
		'Url',
		'Type',
		'SocialNetwork'
	);

	static $searchable_fields = array(
		'Text',
		'Url',
		'Type',
		'SocialNetwork'
	);

	static $_socialNetworksPatterns = array(
		'Facebook'	=>	'facebook',
		'Twitter'	=>	'twitter',
		'Blogger'	=>	'blogger',
		'Wordpress'	=>	'wordpress',
		'LinkedIn'	=>	'linkedin'
	);

	protected function onBeforeWrite() {
		$url = $this->Url;
		$type = $this->Type;
		if($type=='SocialNetwork'){
			$patterns = self::$_socialNetworksPatterns;
			foreach($patterns as $n=>$p){
				if(preg_match('/'.$p.'/i', $url)){
					$this->SocialNetwork = $n;
					break;
				}
			}
		}
		if(!$this->Text){$this->Text = $this->Url;}
		parent::onBeforeWrite();
	}

	public function  getCMSFields($params = null) {
		$fieldSet = new FieldSet();
		$fieldSet->push(new TabSet('Root','Root',new TabSet('Content')),'Root');
		$fields = new FieldSet();
		$_fields = $this->_getFields();
		foreach($_fields as $f){$fields->push($f);}
		$fieldSet->addFieldsToTab('Root.Content.Main', $fields);
        return $fieldSet;
	}

	protected function _getFields(){
		return array(
			'Main'		=>	array(
				'Text'		=>	new TextField('Text','Url Text'),
				'Url'		=>	new UrlField('Url', 'Url Link'),
				'Type'		=>	new DropdownField('Type', 'Url Type', $this->dbObject('Type')->enumValues(),null,null,'Please Choose an URL type'),
			),
		);
	}

	public function HTMLClasses(){
		return 'ExternalLink '.$this->Type.($this->SocialNetwork? ' '.$this->SocialNetwork : '');
	}

	public function Link(){
		return '<a href="'.$this->Url.'" classes="'.$this->HTMLClasses().'">'.$this->Text.'</a>';
	}

	public function LinkURL(){
		return '<a href="'.$this->Url.'" classes="'.$this->HTMLClasses().'">'.str_replace(array('http://','https://'),'',$this->Url).'</a>';
	}

}
