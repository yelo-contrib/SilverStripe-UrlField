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
		'Reference'
	);

	static $searchable_fields = array(
		'Text',
		'URL',
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

	public function getReference(){
		if($this->Type==='SocialNetwork'){
			return $this->SocialNetwork;
		}
		return $this->Type;
	}

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
		$fields = $this->_getFields($params);
		foreach($fields as $tab=>$tabset){
			$fieldSet->addFieldToTab('Root.Content',new Tab($tab));
			foreach($tabset as $name=>$field){
				$fieldSet->addFieldToTab('Root.Content.'.$tab,$field);
			}
		}
        return $fieldSet;
	}

	protected function _getFields(){
		return array(
			'Main'		=>	array(
				'Text'		=>	new TextField('Text','Url Text'),
				'URL'		=>	new UrlField('URL', 'Url Link'),
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
