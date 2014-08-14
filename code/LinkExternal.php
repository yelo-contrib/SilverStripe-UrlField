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
		'URL',
		'Reference',
		'SocialNetwork'
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
		'LinkedIn'	=>	'linkedin',
		'Twitter'	=>	'twitter'
	);

	public function getReference(){
		if($this->Type==='SocialNetwork'){
			return $this->SocialNetwork;
		}
		return $this->Type;
	}

  protected function _providePermissionsArray($c=null){
    if(!$c){$c = $this->class;};
    $perms = array();
    $titles = array(
      'CREATE'=> 'Create'
    ,	'VIEW'	=> 'View'
    ,	'EDIT'=> 'Edit'
    ,	'DELETE'=> 'Delete'
    ,	'PUBLISH'=> 'Publish'
    );
    foreach ($titles as $key => $value) {
      $name = $c.'_'.$key;
      $niceName = ucfirst($c);
      $perms[$name] = array(
        'name' => _t(
          'Permission.'.$name,
          $value.' '.$niceName
        )
      ,	'category' => _t(
          'Permission.CATEGORY_'.$c,
          $niceName
        )
      ,	'help' => _t(
          'Permission.'.$name.'_HELP',
          'Allows the user to '.$value.' '.$niceName
        )
      ,	'sort' => 100
      );
    }
    return $perms;
  }

	protected function onBeforeWrite() {
		$url = $this->URL;
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
		if(!$this->Text){$this->Text = $this->URL;}
		parent::onBeforeWrite();
	}

	public function getCMSFields_forPopup($params = null){
		$fieldSet = new FieldSet();
		$fieldSet->push(new TabSet('Content'));
		$fields = $this->_getFields($params);
		foreach($fields as $tab=>$tabset){
			$fieldSet->addFieldToTab('Content',new Tab($tab));
			foreach($tabset as $name=>$field){
				$fieldSet->addFieldToTab('Content.'.$tab,$field);
			}
		}
        return $fieldSet;
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
				'Type'		=>	new DropdownField('Type', 'Url Type', $this->dbObject('Type')->enumValues(),null,null,'Please Choose an Url type'),
			),
		);
	}

	public function HTMLClasses(){
		return 'ExternalLink '.strtolower($this->Type).($this->SocialNetwork? ' '.strtolower($this->SocialNetwork) : '');
	}

	public function Link(){
		return '<a href="'.$this->URL.'" class="'.$this->HTMLClasses().'" target="_blank" title="'.$this->Text.'">'.$this->Text.'</a>';
	}

	public function LinkURL(){
		return '<a href="'.$this->URL.'" class="'.$this->HTMLClasses().'" target="_blank" title="'.$this->Text.'">'.str_replace(array('http://','https://'),'',$this->URL).'</a>';
	}

	public function canEdit(){return true;}
	public function canCreate(){return true;}
	public function canDelete(){return true;}
	public function canPublish(){return true;}

}
