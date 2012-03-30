<?

class UrlField extends TextField {

	function jsValidation() {
		$formID = $this->form->FormName();
		$error = _t('UrlField.VALIDATIONJS', 'Please enter a valid url.');
		$jsFunc =<<<JS
Behaviour.register({
	"#$formID": {
		validateUrlField: function(fieldName) {
			var el = _CURRENT_FORM.elements[fieldName];
			if(!el || !el.value) return true;

		 	if(el.value.match(/((ftp|http|https):\/\/)?(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/)) {
		 		return true;
		 	} else {
				validationError(el, "$error","validation");
		 		return false;
		 	}
		}
	}
});
JS;
		//fix for the problem with more than one form on a page.
		Requirements::customScript($jsFunc, 'func_validateUrlField' .'_' . $formID);

		//return "\$('$formID').validateEmailField('$this->name');";
		return <<<JS
if(typeof fromAnOnBlur != 'undefined'){
	if(fromAnOnBlur.name == '$this->name')
		$('$formID').validateUrlField('$this->name');
}else{
	$('$formID').validateUrlField('$this->name');
}
JS;
	}

	function validate($validator){
		$this->value = trim($this->value);

		$pattern = '|^(http(s)?://)?[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';

		if($this->value && !preg_match($pattern, $this->value)){
 			$validator->validationError(
 				$this->name,
				_t('UrlField.VALIDATION', "Please enter a valid url."),
				"validation"
			);
			return false;
		}

		if(substr($this->value, 0, 8) != 'https://' && substr($this->value, 0, 7) != 'http://') {
			$this->value = $this->value = 'http://' . $this->value;
		}
		
		return true;
	}
}